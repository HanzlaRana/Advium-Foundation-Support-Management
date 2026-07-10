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

        // Only approved applications can receive a loan
        if ($application->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved applications can be issued a loan.',
            ], 422);
        }

        // Prevent duplicate loans for the same application
        if (Loan::where('application_id', $applicationId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'A loan already exists for this application.',
            ], 409);
        }

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
            $query->where(function ($q) use ($request) {
                $q->where('loan_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('applicant', function ($sub) use ($request) {
                      $sub->where('full_name', 'like', '%' . $request->search . '%')
                          ->orWhere('cnic', 'like', '%' . $request->search . '%');
                  });
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
    // Route: POST /loans/{id}/payment — {id} is the LOAN id.
    // Accepts whatever the frontend sends; fills sensible defaults for the rest.
    public function recordPayment(Request $request, $loanId)
    {
        $request->validate([
            'paid_amount'    => 'nullable|numeric|min:1',
            'amount'         => 'nullable|numeric|min:1',
            'payment_method' => 'nullable|string',
            'paid_date'      => 'nullable|date',
            'installment_id' => 'nullable|integer',
            'notes'          => 'nullable|string',
        ]);

        // Accept the amount under either key the frontend may use
        $paidAmount = $request->paid_amount ?? $request->amount;

        if (!$paidAmount || $paidAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount is required.',
            ], 422);
        }

        $paymentMethod = $request->payment_method ?? 'cash';
        $paidDate      = $request->paid_date ?? now()->toDateString();

        $loan = Loan::findOrFail($loanId);

        if ($request->installment_id) {
            $installment = Installment::where('loan_id', $loanId)
                                      ->findOrFail($request->installment_id);
        } else {
            // Pay the next unpaid installment automatically
            $installment = Installment::where('loan_id', $loanId)
                                      ->whereIn('status', ['pending', 'partial'])
                                      ->orderBy('installment_number')
                                      ->first();
        }

        if (!$installment) {
            return response()->json([
                'success' => false,
                'message' => 'No unpaid installments remaining for this loan.',
            ], 422);
        }

        if ($installment->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This installment has already been paid.',
            ], 409);
        }

        $receiptNumber = 'RCP-' . strtoupper(Str::random(8));
        $fullyPaid     = $paidAmount >= $installment->amount;

        $installment->update([
            'paid_amount'    => $paidAmount,
            'paid_date'      => $paidDate,
            'payment_method' => $paymentMethod,
            'receipt_number' => $receiptNumber,
            'collected_by'   => auth()->id(),
            'notes'          => $request->notes,
            'status'         => $fullyPaid ? 'paid' : 'partial',
        ]);

        // Update loan totals — only count fully paid installments
        $totalPaid        = $loan->total_paid + $paidAmount;
        $remainingBalance = $loan->remaining_balance - $paidAmount;

        $loan->update([
            'total_paid'         => $totalPaid,
            'remaining_balance'  => max(0, $remainingBalance),
            'paid_installments'  => $loan->paid_installments + ($fullyPaid ? 1 : 0),
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
            ->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'overdue' => $overdue,
        ]);
    }
}