<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Applicant;
use App\Models\Loan;
use App\Models\Installment;
use App\Models\Distribution;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Application report
    public function applications(Request $request)
    {
        $query = Application::with(['applicant', 'program']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->program_id) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Summary stats
        $summary = [
            'total'       => Application::count(),
            'submitted'   => Application::where('status', 'submitted')->count(),
            'approved'    => Application::where('status', 'approved')->count(),
            'rejected'    => Application::where('status', 'rejected')->count(),
            'distributed' => Application::where('status', 'distributed')->count(),
        ];

        // By program
        $byProgram = Application::select('program_id', DB::raw('COUNT(*) as total'))
            ->with('program:id,name')
            ->groupBy('program_id')
            ->get()
            ->map(function ($item) {
                return [
                    'program' => $item->program->name ?? 'Unknown',
                    'total'   => $item->total,
                ];
            });

        // Monthly
        $monthly = Application::select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw('EXTRACT(YEAR FROM created_at)'), DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->orderBy('month')
            ->get();

        $applications = $query->latest()->paginate(20);

        return response()->json([
            'success'      => true,
            'summary'      => $summary,
            'by_program'   => $byProgram,
            'monthly'      => $monthly,
            'applications' => $applications,
        ]);
    }

    // Loan recovery report
    public function loanRecovery(Request $request)
    {
        $totalLoans        = Loan::count();
        $activeLoans       = Loan::where('status', 'active')->count();
        $completedLoans    = Loan::where('status', 'completed')->count();
        $defaultedLoans    = Loan::where('status', 'defaulted')->count();
        $totalDisbursed    = Loan::sum('loan_amount');
        $totalRecovered    = Loan::sum('total_paid');
        $totalOutstanding  = Loan::sum('remaining_balance');
        $recoveryRate      = $totalDisbursed > 0
                             ? round(($totalRecovered / $totalDisbursed) * 100, 2)
                             : 0;

        // Overdue installments
        $overdueCount  = Installment::where('status', 'pending')
                                    ->where('due_date', '<', now())
                                    ->count();

        $overdueAmount = Installment::where('status', 'pending')
                                    ->where('due_date', '<', now())
                                    ->sum('amount');

        // By asset type
        $byAssetType = Loan::select('asset_type', DB::raw('COUNT(*) as total'), DB::raw('SUM(remaining_balance) as outstanding'))
            ->groupBy('asset_type')
            ->get();

        return response()->json([
            'success' => true,
            'summary' => [
                'total_loans'       => $totalLoans,
                'active_loans'      => $activeLoans,
                'completed_loans'   => $completedLoans,
                'defaulted_loans'   => $defaultedLoans,
                'total_disbursed'   => $totalDisbursed,
                'total_recovered'   => $totalRecovered,
                'total_outstanding' => $totalOutstanding,
                'recovery_rate'     => $recoveryRate . '%',
                'overdue_count'     => $overdueCount,
                'overdue_amount'    => $overdueAmount,
            ],
            'by_asset_type' => $byAssetType,
        ]);
    }

    // Distribution report
    public function distributions(Request $request)
    {
        $query = Distribution::with(['application.applicant', 'application.program', 'distributedBy']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        $summary = [
            'total'     => Distribution::count(),
            'scheduled' => Distribution::where('status', 'scheduled')->count(),
            'completed' => Distribution::where('status', 'completed')->count(),
            'failed'    => Distribution::where('status', 'failed')->count(),
        ];

        $distributions = $query->latest()->paginate(20);

        return response()->json([
            'success'       => true,
            'summary'       => $summary,
            'distributions' => $distributions,
        ]);
    }

    // Inventory report
    public function inventory()
    {
        $totalItems      = InventoryItem::count();
        $lowStockItems   = InventoryItem::whereRaw('quantity_in_stock <= reorder_level')->count();
        $totalStockValue = InventoryItem::selectRaw('SUM(quantity_in_stock * unit_cost) as total')->value('total');

        $byCategory = InventoryItem::select('category', DB::raw('COUNT(*) as items'), DB::raw('SUM(quantity_in_stock) as total_stock'))
            ->groupBy('category')
            ->get();

        $expiringItems = InventoryItem::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->whereDate('expiry_date', '>=', now())
            ->count();

        return response()->json([
            'success' => true,
            'summary' => [
                'total_items'       => $totalItems,
                'low_stock_items'   => $lowStockItems,
                'expiring_items'    => $expiringItems,
                'total_stock_value' => round($totalStockValue, 2),
            ],
            'by_category' => $byCategory,
        ]);
    }
}