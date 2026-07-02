<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::orderBy('date', 'desc')->paginate(50);
        return response()->json(['success' => true, 'donations' => $donations]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'donor_name'  => 'required|string|max:255',
            'donor_phone' => 'nullable|string|max:20',
            'donor_email' => 'nullable|email|max:255',
            'type'        => 'required|in:Cash,Zakat,Sadqa,In-Kind,Bank Transfer',
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        $validated['recorded_by'] = auth()->id();
        $donation = Donation::create($validated);

        return response()->json(['success' => true, 'donation' => $donation], 201);
    }

    public function show(Donation $donation)
    {
        return response()->json(['success' => true, 'donation' => $donation]);
    }

    public function update(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'donor_name'  => 'sometimes|string|max:255',
            'type'        => 'sometimes|in:Cash,Zakat,Sadqa,In-Kind,Bank Transfer',
            'amount'      => 'sometimes|numeric|min:1',
            'date'        => 'sometimes|date',
            'notes'       => 'nullable|string',
        ]);

        $donation->update($validated);
        return response()->json(['success' => true, 'donation' => $donation]);
    }

    public function destroy(Donation $donation)
    {
        $donation->delete();
        return response()->json(['success' => true, 'message' => 'Donation deleted.']);
    }
}