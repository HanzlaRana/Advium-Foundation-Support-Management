<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::orderBy('date', 'desc')->paginate(50);
        return response()->json(['success' => true, 'expenses' => $expenses]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'    => 'required|in:distribution,operational,procurement,administrative,transport,utilities,other',
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:500',
            'date'        => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        $validated['recorded_by'] = auth()->id();
        $expense = Expense::create($validated);

        return response()->json(['success' => true, 'expense' => $expense], 201);
    }

    public function show(Expense $expense)
    {
        return response()->json(['success' => true, 'expense' => $expense]);
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category'    => 'sometimes|in:distribution,operational,procurement,administrative,transport,utilities,other',
            'amount'      => 'sometimes|numeric|min:1',
            'description' => 'sometimes|string|max:500',
            'date'        => 'sometimes|date',
            'notes'       => 'nullable|string',
        ]);

        $expense->update($validated);
        return response()->json(['success' => true, 'expense' => $expense]);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(['success' => true, 'message' => 'Expense deleted.']);
    }
}