<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    // Get all applicants
    public function index(Request $request)
    {
        $query = Applicant::withCount('applications');

        if ($request->search) {
            $query->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('cnic', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        if ($request->city) {
            $query->where('city', $request->city);
        }

        if ($request->blacklisted) {
            $query->where('is_blacklisted', $request->blacklisted === 'true');
        }

        $applicants = $query->latest()->paginate(15);

        return response()->json([
            'success'    => true,
            'applicants' => $applicants,
        ]);
    }

    // Get single applicant with all applications
    public function show($id)
    {
        $applicant = Applicant::with([
            'applications.program',
            'applications.assignedTo'
        ])->findOrFail($id);

        return response()->json([
            'success'   => true,
            'applicant' => $applicant,
        ]);
    }

    // Update applicant
    public function update(Request $request, $id)
    {
        $applicant = Applicant::findOrFail($id);

        $request->validate([
            'full_name'      => 'sometimes|string|max:255',
            'phone'          => 'sometimes|string',
            'address'        => 'sometimes|string',
            'city'           => 'sometimes|string',
            'province'       => 'sometimes|string',
            'family_members' => 'sometimes|integer|min:1',
            'monthly_income' => 'sometimes|numeric|min:0',
            'occupation'     => 'sometimes|string',
        ]);

        $applicant->update($request->only([
            'full_name',
            'phone',
            'address',
            'city',
            'province',
            'family_members',
            'monthly_income',
            'occupation',
        ]));

        return response()->json([
            'success'   => true,
            'message'   => 'Applicant updated successfully.',
            'applicant' => $applicant,
        ]);
    }

    // Blacklist applicant
    public function blacklist(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $applicant = Applicant::findOrFail($id);
        $applicant->update([
            'is_blacklisted'   => true,
            'blacklist_reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Applicant blacklisted successfully.',
        ]);
    }

    // Remove from blacklist
    public function unblacklist($id)
    {
        $applicant = Applicant::findOrFail($id);
        $applicant->update([
            'is_blacklisted'   => false,
            'blacklist_reason' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Applicant removed from blacklist.',
        ]);
    }
}