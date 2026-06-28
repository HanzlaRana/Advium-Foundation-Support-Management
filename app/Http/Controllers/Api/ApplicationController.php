<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    // Submit new application (public)
    public function store(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'cnic'           => 'required|string|regex:/^\d{5}-\d{7}-\d{1}$/',
            'phone'          => 'required|string',
            'address'        => 'required|string',
            'city'           => 'nullable|string',
            'province'       => 'nullable|string',
            'family_members' => 'nullable|integer|min:1',
            'monthly_income' => 'nullable|numeric|min:0',
            'occupation'     => 'nullable|string',
            'program_slug'   => 'required|string|exists:programs,slug',
        ]);

        // Check if program exists and is active
        $program = Program::where('slug', $request->program_slug)
                          ->where('is_active', true)
                          ->firstOrFail();

        // Find or create applicant by CNIC
        $applicant = Applicant::firstOrCreate(
            ['cnic' => $request->cnic],
            [
                'full_name'      => $request->full_name,
                'phone'          => $request->phone,
                'address'        => $request->address,
                'city'           => $request->city,
                'province'       => $request->province,
                'family_members' => $request->family_members ?? 1,
                'monthly_income' => $request->monthly_income ?? 0,
                'occupation'     => $request->occupation,
            ]
        );

        // Check if blacklisted
        if ($applicant->is_blacklisted) {
            return response()->json([
                'success' => false,
                'message' => 'Your application cannot be processed at this time.',
            ], 403);
        }

        // Check if already applied for this program
        $existing = Application::where('applicant_id', $applicant->id)
                               ->where('program_id', $program->id)
                               ->whereNotIn('status', ['rejected', 'closed'])
                               ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active application for this program.',
                'application_code' => $existing->application_code,
            ], 409);
        }

        // Generate unique application code
        $code = 'APP-' . strtoupper(Str::random(8));

        // Create application
        $application = Application::create([
            'application_code' => $code,
            'applicant_id'     => $applicant->id,
            'program_id'       => $program->id,
            'status'           => 'submitted',
            'submitted_at'     => now(),
        ]);

        return response()->json([
            'success'          => true,
            'message'          => 'Application submitted successfully.',
            'application_code' => $code,
            'program'          => $program->name,
        ], 201);
    }

    // Track application by code or CNIC (public)
    public function track(Request $request)
    {
        $request->validate([
            'code' => 'required_without:cnic|string',
            'cnic' => 'required_without:code|string',
        ]);

        if ($request->code) {
            $application = Application::with(['program', 'applicant'])
                ->where('application_code', $request->code)
                ->first();
        } else {
            $applicant = Applicant::where('cnic', $request->cnic)->first();
            if (!$applicant) {
                return response()->json([
                    'success' => false,
                    'message' => 'No applications found.',
                ], 404);
            }
            $application = Application::with(['program', 'applicant'])
                ->where('applicant_id', $applicant->id)
                ->latest()
                ->first();
        }

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found.',
            ], 404);
        }

        return response()->json([
            'success'     => true,
            'application' => [
                'code'         => $application->application_code,
                'status'       => $application->status,
                'program'      => $application->program->name,
                'submitted_at' => $application->submitted_at,
                'approved_at'  => $application->approved_at,
                'applicant'    => $application->applicant->full_name,
            ],
        ]);
    }

    // Get all applications (admin only)
    public function index(Request $request)
    {
        $query = Application::with(['applicant', 'program', 'assignedTo']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->program_id) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->search) {
            $query->whereHas('applicant', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('cnic', 'like', '%' . $request->search . '%');
            })->orWhere('application_code', 'like', '%' . $request->search . '%');
        }

        $applications = $query->latest()->paginate(15);

        return response()->json([
            'success'      => true,
            'applications' => $applications,
        ]);
    }

    // Get single application (admin only)
    public function show($id)
    {
        $application = Application::with(['applicant', 'program', 'assignedTo'])
            ->findOrFail($id);

        return response()->json([
            'success'     => true,
            'application' => $application,
        ]);
    }

    // Update application status (admin only)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'           => 'required|in:draft,submitted,under_review,approved,rejected,on_hold,distributed,closed',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $application = Application::findOrFail($id);
        $application->update([
            'status'           => $request->status,
            'rejection_reason' => $request->rejection_reason,
            'notes'            => $request->notes,
            'approved_at'      => $request->status === 'approved' ? now() : $application->approved_at,
            'distributed_at'   => $request->status === 'distributed' ? now() : $application->distributed_at,
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Application status updated.',
            'application' => $application,
        ]);
    }
}