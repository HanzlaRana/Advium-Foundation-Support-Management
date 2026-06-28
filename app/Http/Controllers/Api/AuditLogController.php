<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // Get all audit logs
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->action) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->model_type) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'logs'    => $logs,
        ]);
    }

    // Get single log
    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'log'     => $log,
        ]);
    }
}