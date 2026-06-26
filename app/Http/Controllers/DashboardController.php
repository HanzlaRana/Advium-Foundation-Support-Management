<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;

class DashboardController extends Controller
{
    public function index()
    {
        $total = Beneficiary::count();

        $pending = Beneficiary::where('status', 'Pending')->count();

        $approved = Beneficiary::where('status', 'Approved')->count();

        $rejected = Beneficiary::where('status', 'Rejected')->count();

        return view('dashboard', compact(
            'total',
            'pending',
            'approved',
            'rejected'
        ));
    }
}