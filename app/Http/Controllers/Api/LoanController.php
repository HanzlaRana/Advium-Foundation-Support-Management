<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Installment;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LoanController extends Controller
{
    // Create loan for approved application
    public function store(Request $request, $applicationId)
    {
        $request->validate([
            'asset_type'        => 'required|in:disabled_bike,rickshaw',
            'total_amount'      => 'required|numeric|min:1',
            'down_payment'      => 'nullable|numeric|min:0',
            'total_installments'=> 'required|integer|min:1|max:60',
            'start_date'        => 'required|date',
            'guarantor_name'    => 'nullable|string',
            'guarantor_cnic'    => 'nullable|string',
            'guarantor_phone'   => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        $application = Application::findOrFail($applicationId);

        // Calculate loan details
        $downPayment        = $request->down_payment ?? 0;
        $loanAmount         = $request->total_amount - $downPayment;
        $monthlyInstallment = round($loanAmount / $request->total_installments, 2);
        $startDate          = Carbon::parse($request->start_date);
        $endDate            = $startDate->copy()->addMonths($request->total_installments);
        $loanNumber         = 'LOAN-' . strtoupper(Str::random(8));

        $loan = Loan::create([
            'application_id'      => $applicationId,
            'applicant_id'        => $application->applicant_id,
            'loan_number'         => $loanNumber,
            'asset_type'          => $request->asset_type,
            'total_amount'        => $request->total_amount,
            'down_payment'        => $downPayment,
            'loan_amount'         => $loanAmount,
            'monthly_installment' => $monthlyInstallment,
            'total_installments'  => $request->total_installments,
            'paid_installments'   => 0,
            'total_paid'          => 0,
            'remaining_balance'   => $loanAmount,
            'start_date'          => $startDate,
            'end_date'            => $endDate,
            'status'              => 'active',
            'guarantor_name'      => $request->guarantor_name,
            'guarantor_cnic'      => $request->guarantor_cnic,
            'guarantor_phone'     => $request->guarantor_phone,
            'notes'               => $request->notes,
        ]);

        // Generate installment schedule
        for ($i = 1; $i <= $request->total_installments; $i++) {
            Installment::create([
                'loan_id'            => $loan->id,
                'installment_number' => $i,
                'due_date'           => $startDate->copy()->addMonths($i),
                'amount'             => $monthlyInstallment,
                'status'             => 'pending',
            ]);
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Loan created and installment schedule generated.',
            'loan'         => $loan,
            'loan_number'  => $loanNumber,
            'installments' => $loan->installments()->count(),
        ], 201);
    }

    // Get all loans
    public function index(Request $request)
    {
        $query = Loan::with(['applicant', 'application.program']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('loan_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('applicant', function ($q) use ($request) {
                      $q->where('full_name', 'like', '%' . $request->search . '%')
                        ->orWhere('cnic', 'like', '%' . $request->search . '%');
                  });
        }

        $loans = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'loans'   => $loans,
        ]);
    }

    // Get single loan with installments
    public function show($id)
    {
        $loan = Loan::with([
            'applicant',
            'application.program',
            'installments.collectedBy'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'loan'    => $loan,
        ]);
    }

    // Record installment payment
    public function recordPayment(Request $request, $loanId, $installmentId)
    {
        $request->validate([
            'paid_amount'    => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'paid_date'      => 'required|date',
            'notes'          => 'nullable|string',
        ]);

        $loan        = Loan::findOrFail($loanId);
        $installment = Installment::where('loan_id', $loanId)
                                  ->findOrFail($installmentId);

        $receiptNumber = 'RCP-' . strtoupper(Str::random(8));

        $installment->update([
            'paid_amount'    => $request->paid_amount,
            'paid_date'      => $request->paid_date,
            'payment_method' => $request->payment_method,
            'receipt_number' => $receiptNumber,
            'collected_by'   => auth()->id(),
            'notes'          => $request->notes,
            'status'         => $request->paid_amount >= $installment->amount
                                ? 'paid'
                                : 'partial',
        ]);

        // Update loan totals
        $totalPaid        = $loan->total_paid + $request->paid_amount;
        $remainingBalance = $loan->remaining_balance - $request->paid_amount;
        $paidInstallments = $loan->paid_installments + 1;

        $loan->update([
            'total_paid'         => $totalPaid,
            'remaining_balance'  => max(0, $remainingBalance),
            'paid_installments'  => $paidInstallments,
            'status'             => $remainingBalance <= 0 ? 'completed' : 'active',
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Payment recorded successfully.',
            'receipt_number' => $receiptNumber,
            'remaining'      => max(0, $remainingBalance),
        ]);
    }

    // Get overdue installments
    public function overdue()
    {
        $overdue = Installment::with(['loan.applicant'])
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'overdue' => $overdue,
        ]);
    }
}