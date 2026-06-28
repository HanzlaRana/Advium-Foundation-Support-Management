<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApprovalLog;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    // Get approval history for an application
    public function logs($applicationId)
    {
        $application = Application::with([
            'approvalLogs.user',
            'applicant',
            'program',
            'survey'
        ])->findOrFail($applicationId);

        return response()->json([
            'success'     => true,
            'application' => $application,
            'logs'        => $application->approvalLogs,
        ]);
    }

    // Approve application
    public function approve(Request $request, $applicationId)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);

        $application = Application::findOrFail($applicationId);
        $user        = auth()->user();

        // Determine level
        $level = $user->role === 'superadmin' ? 'superadmin' : 'admin';

        // Determine new status
        $newStatus = $user->isSuperAdmin() ? 'approved' : 'under_review';

        $fromStatus = $application->status;

        $application->update([
            'status'      => $newStatus,
            'approved_at' => $user->isSuperAdmin() ? now() : null,
        ]);

        // Log the action
        ApprovalLog::create([
            'application_id' => $applicationId,
            'user_id'        => $user->id,
            'action'         => 'approved',
            'level'          => $level,
            'remarks'        => $request->remarks,
            'from_status'    => $fromStatus,
            'to_status'      => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application approved successfully.',
            'status'  => $newStatus,
        ]);
    }

    // Reject application
    public function reject(Request $request, $applicationId)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);

        $application = Application::findOrFail($applicationId);
        $user        = auth()->user();
        $level       = $user->role === 'superadmin' ? 'superadmin' : 'admin';
        $fromStatus  = $application->status;

        $application->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->remarks,
        ]);

        ApprovalLog::create([
            'application_id' => $applicationId,
            'user_id'        => $user->id,
            'action'         => 'rejected',
            'level'          => $level,
            'remarks'        => $request->remarks,
            'from_status'    => $fromStatus,
            'to_status'      => 'rejected',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application rejected.',
        ]);
    }

    // Put on hold
    public function hold(Request $request, $applicationId)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);

        $application = Application::findOrFail($applicationId);
        $user        = auth()->user();
        $level       = $user->role === 'superadmin' ? 'superadmin' : 'admin';
        $fromStatus  = $application->status;

        $application->update(['status' => 'on_hold']);

        ApprovalLog::create([
            'application_id' => $applicationId,
            'user_id'        => $user->id,
            'action'         => 'on_hold',
            'level'          => $level,
            'remarks'        => $request->remarks,
            'from_status'    => $fromStatus,
            'to_status'      => 'on_hold',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application put on hold.',
        ]);
    }

    // Assign to volunteer
    public function assign(Request $request, $applicationId)
    {
        $request->validate([
            'volunteer_id' => 'required|exists:users,id',
            'remarks'      => 'nullable|string',
        ]);

        $application = Application::findOrFail($applicationId);
        $fromStatus  = $application->status;

        $application->update([
            'assigned_to' => $request->volunteer_id,
            'status'      => 'under_review',
        ]);

        ApprovalLog::create([
            'application_id' => $applicationId,
            'user_id'        => auth()->id(),
            'action'         => 'assigned',
            'level'          => 'admin',
            'remarks'        => $request->remarks ?? 'Assigned to volunteer for field survey.',
            'from_status'    => $fromStatus,
            'to_status'      => 'under_review',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application assigned to volunteer.',
        ]);
    }
}