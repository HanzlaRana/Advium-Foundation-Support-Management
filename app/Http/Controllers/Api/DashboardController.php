<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Applicant;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        // Application stats
        $totalApplications  = Application::count();
        $submitted          = Application::where('status', 'submitted')->count();
        $underReview        = Application::where('status', 'under_review')->count();
        $approved           = Application::where('status', 'approved')->count();
        $rejected           = Application::where('status', 'rejected')->count();
        $distributed        = Application::where('status', 'distributed')->count();

        // Applicant stats
        $totalApplicants    = Applicant::count();

        // Program stats
        $totalPrograms      = Program::where('is_active', true)->count();

        // User stats
        $totalStaff         = User::count();

        // Monthly applications for current year
        $monthly = Application::select(
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

        // Applications by program
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

        // Recent applications
        $recent = Application::with(['applicant:id,full_name,cnic', 'program:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($app) {
                return [
                    'code'         => $app->application_code,
                    'applicant'    => $app->applicant->full_name ?? '',
                    'program'      => $app->program->name ?? '',
                    'status'       => $app->status,
                    'submitted_at' => $app->submitted_at,
                ];
            });

        return response()->json([
            'success' => true,
            'stats'   => [
                'applications' => [
                    'total'        => $totalApplications,
                    'submitted'    => $submitted,
                    'under_review' => $underReview,
                    'approved'     => $approved,
                    'rejected'     => $rejected,
                    'distributed'  => $distributed,
                ],
                'applicants'   => $totalApplicants,
                'programs'     => $totalPrograms,
                'staff'        => $totalStaff,
                'monthly'      => array_values($monthlyData),
                'by_program'   => $byProgram,
                'recent'       => $recent,
            ],
        ]);
    }
}