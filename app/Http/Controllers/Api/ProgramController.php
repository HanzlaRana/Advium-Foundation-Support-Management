<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    // Get all active programs (public)
    public function index(Request $request)
    {
        $query = Program::where('is_active', true);

        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $programs = $query->orderBy('total_helped', 'desc')->get();

        return response()->json([
            'success'  => true,
            'programs' => $programs,
        ]);
    }

    // Get single program by slug (public)
    public function show($slug)
    {
        $program = Program::where('slug', $slug)
                          ->where('is_active', true)
                          ->firstOrFail();

        return response()->json([
            'success' => true,
            'program' => $program,
        ]);
    }

    // Create program (admin only)
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|unique:programs',
            'description' => 'required|string',
            'category'    => 'required|string',
            'type'        => 'required|in:free,loan',
            'icon'        => 'nullable|string',
        ]);

        $program = Program::create($request->all());

        return response()->json([
            'success' => true,
            'program' => $program,
        ], 201);
    }

    // Update program (admin only)
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category'    => 'sometimes|string',
            'type'        => 'sometimes|in:free,loan',
            'is_active'   => 'sometimes|boolean',
        ]);

        $program->update($request->all());

        return response()->json([
            'success' => true,
            'program' => $program,
        ]);
    }
}