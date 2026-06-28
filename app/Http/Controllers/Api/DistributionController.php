<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use App\Models\Application;
use App\Models\ApprovalLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DistributionController extends Controller
{
    // Schedule distribution
    public function store(Request $request, $applicationId)
    {
        $request->validate([
            'delivery_method'  => 'required|in:physical,home_delivery,partner',
            'scheduled_date'   => 'required|date',
            'location'         => 'nullable|string',
            'recipient_name'   => 'nullable|string',
            'recipient_cnic'   => 'nullable|string',
            'recipient_phone'  => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $application = Application::findOrFail($applicationId);

        // Only approved applications can be distributed
        if ($application->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved applications can be scheduled for distribution.',
            ], 422);
        }

        // Check if distribution already exists
        if ($application->distribution) {
            return response()->json([
                'success' => false,
                'message' => 'Distribution already scheduled for this application.',
            ], 409);
        }

        // Generate QR code string
        $qrCode = 'DIST-' . strtoupper(Str::random(10));

        $distribution = Distribution::create([
            'application_id'  => $applicationId,
            'distributed_by'  => auth()->id(),
            'delivery_method' => $request->delivery_method,
            'scheduled_date'  => $request->scheduled_date,
            'location'        => $request->location,
            'recipient_name'  => $request->recipient_name,
            'recipient_cnic'  => $request->recipient_cnic,
            'recipient_phone' => $request->recipient_phone,
            'notes'           => $request->notes,
            'status'          => 'scheduled',
            'qr_code'         => $qrCode,
        ]);

        // Log the action
        ApprovalLog::create([
            'application_id' => $applicationId,
            'user_id'        => auth()->id(),
            'action'         => 'distributed',
            'level'          => 'admin',
            'remarks'        => 'Distribution scheduled for ' . $request->scheduled_date,
            'from_status'    => 'approved',
            'to_status'      => 'approved',
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Distribution scheduled successfully.',
            'distribution' => $distribution,
            'qr_code'      => $qrCode,
        ], 201);
    }

    // Complete distribution
    public function complete(Request $request, $applicationId)
    {
        $request->validate([
            'actual_date'     => 'required|date',
            'recipient_name'  => 'required|string',
            'recipient_cnic'  => 'required|string',
            'notes'           => 'nullable|string',
        ]);

        $application = Application::with('distribution')->findOrFail($applicationId);

        if (!$application->distribution) {
            return response()->json([
                'success' => false,
                'message' => 'No distribution found for this application.',
            ], 404);
        }

        $application->distribution->update([
            'status'         => 'completed',
            'actual_date'    => $request->actual_date,
            'recipient_name' => $request->recipient_name,
            'recipient_cnic' => $request->recipient_cnic,
            'notes'          => $request->notes,
        ]);

        // Update application status
        $application->update([
            'status'         => 'distributed',
            'distributed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Distribution completed successfully.',
        ]);
    }

    // Get all distributions
    public function index(Request $request)
    {
        $query = Distribution::with([
            'application.applicant',
            'application.program',
            'distributedBy'
        ]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('scheduled_date', $request->date);
        }

        $distributions = $query->latest()->paginate(15);

        return response()->json([
            'success'       => true,
            'distributions' => $distributions,
        ]);
    }

    // Get single distribution
    public function show($applicationId)
    {
        $application = Application::with([
            'distribution.distributedBy',
            'applicant',
            'program'
        ])->findOrFail($applicationId);

        return response()->json([
            'success'      => true,
            'distribution' => $application->distribution,
            'application'  => $application,
        ]);
    }
}