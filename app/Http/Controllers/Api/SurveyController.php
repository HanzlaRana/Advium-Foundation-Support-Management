<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Application;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    // Submit survey (volunteer)
    public function store(Request $request, $applicationId)
    {
        $request->validate([
            'house_type'              => 'nullable|in:owned,rented,shared,homeless',
            'house_condition'         => 'nullable|in:good,average,poor,very_poor',
            'rooms'                   => 'nullable|integer|min:0',
            'has_electricity'         => 'boolean',
            'has_gas'                 => 'boolean',
            'has_water'               => 'boolean',
            'has_internet'            => 'boolean',
            'total_members'           => 'nullable|integer|min:1',
            'earning_members'         => 'nullable|integer|min:0',
            'school_going_children'   => 'nullable|integer|min:0',
            'total_monthly_income'    => 'nullable|numeric|min:0',
            'total_monthly_expenses'  => 'nullable|numeric|min:0',
            'employment_status'       => 'nullable|in:employed,self_employed,unemployed,disabled,retired',
            'eligibility_result'      => 'required|in:eligible,conditionally_eligible,not_eligible',
            'notes'                   => 'nullable|string',
            'visited_at'              => 'nullable|date',
        ]);

        $application = Application::findOrFail($applicationId);

        // Check if survey already exists
        if ($application->survey) {
            return response()->json([
                'success' => false,
                'message' => 'Survey already submitted for this application.',
            ], 409);
        }

        $survey = Survey::create([
            'application_id'          => $applicationId,
            'volunteer_id'            => auth()->id(),
            'house_type'              => $request->house_type,
            'house_condition'         => $request->house_condition,
            'rooms'                   => $request->rooms,
            'has_electricity'         => $request->has_electricity ?? false,
            'has_gas'                 => $request->has_gas ?? false,
            'has_water'               => $request->has_water ?? false,
            'has_internet'            => $request->has_internet ?? false,
            'total_members'           => $request->total_members,
            'earning_members'         => $request->earning_members,
            'school_going_children'   => $request->school_going_children,
            'total_monthly_income'    => $request->total_monthly_income,
            'total_monthly_expenses'  => $request->total_monthly_expenses,
            'employment_status'       => $request->employment_status,
            'eligibility_result'      => $request->eligibility_result,
            'notes'                   => $request->notes,
            'visited_at'              => $request->visited_at ?? now(),
        ]);

        // Update application status to under_review
        $application->update(['status' => 'under_review']);

        return response()->json([
            'success' => true,
            'message' => 'Survey submitted successfully.',
            'survey'  => $survey,
        ], 201);
    }

    // Get survey for an application
    public function show($applicationId)
    {
        $application = Application::with(['survey.volunteer'])->findOrFail($applicationId);

        if (!$application->survey) {
            return response()->json([
                'success' => false,
                'message' => 'No survey found for this application.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'survey'  => $application->survey,
        ]);
    }

    // Update survey (volunteer)
    public function update(Request $request, $applicationId)
    {
        $application = Application::findOrFail($applicationId);

        if (!$application->survey) {
            return response()->json([
                'success' => false,
                'message' => 'No survey found for this application.',
            ], 404);
        }

        $application->survey->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Survey updated successfully.',
            'survey'  => $application->survey,
        ]);
    }

    // Get all surveys assigned to volunteer
    public function myAssignments()
    {
        $applications = Application::with(['applicant', 'program', 'survey'])
            ->where('assigned_to', auth()->id())
            ->latest()
            ->paginate(15);

        return response()->json([
            'success'      => true,
            'applications' => $applications,
        ]);
    }
}