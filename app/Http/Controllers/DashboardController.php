<?php
namespace App\Http\Controllers;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $total    = Beneficiary::count();
        $pending  = Beneficiary::where('status', 'Pending')->count();
        $approved = Beneficiary::where('status', 'Approved')->count();
        $rejected = Beneficiary::where('status', 'Rejected')->count();

        // PostgreSQL uses EXTRACT instead of MONTH()
        $monthly = Beneficiary::select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->orderBy('month')
            ->get();

        $monthlyData = array_fill(1, 12, 0);
        foreach ($monthly as $row) {
            $monthlyData[(int)$row->month] = $row->total;
        }

        $recent = Beneficiary::latest()->take(5)->get();

        return view('dashboard', compact(
            'total',
            'pending',
            'approved',
            'rejected',
            'monthlyData',
            'recent'
        ));
    }
}