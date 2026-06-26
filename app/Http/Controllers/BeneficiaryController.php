<?php

namespace App\Http\Controllers;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $search = $request->search;

    $beneficiaries = Beneficiary::when($search, function ($query) use ($search) {
        $query->where('beneficiary_code', 'like', "%{$search}%")
              ->orWhere('full_name', 'like', "%{$search}%")
              ->orWhere('cnic', 'like', "%{$search}%");
    })->paginate(5);

    $beneficiaries->appends($request->all());

return view('beneficiaries.index', compact('beneficiaries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('beneficiaries.create');
    }

    /**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    $request->validate([
        'beneficiary_code' => 'required|unique:beneficiaries',
        'full_name' => 'required',
        'cnic' => 'required|unique:beneficiaries,cnic',
        'phone' => 'required',
        'address' => 'required',
        'status' => 'required',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $photoName = null;

    if ($request->hasFile('photo')) {
        $photoName = time() . '.' . $request->photo->extension();
        $request->photo->storeAs('beneficiaries', $photoName, 'public');
    }

    Beneficiary::create([
        'beneficiary_code' => $request->beneficiary_code,
        'full_name' => $request->full_name,
        'cnic' => $request->cnic,
        'phone' => $request->phone,
        'address' => $request->address,
        'status' => $request->status,
        'photo' => $photoName,
    ]);

    return redirect()
        ->route('beneficiaries.index')
        ->with('success', 'Beneficiary added successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    $beneficiary = Beneficiary::findOrFail($id);

    return view('beneficiaries.show', compact('beneficiary'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);

    return view('beneficiaries.edit', compact('beneficiary'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
        'beneficiary_code' => 'required',
        'full_name' => 'required',
        'cnic' => 'required',
        'phone' => 'required',
        'address' => 'required',
        'status' => 'required',
    ]);

    $beneficiary = Beneficiary::findOrFail($id);

    $beneficiary->update([
        'beneficiary_code' => $request->beneficiary_code,
        'full_name' => $request->full_name,
        'cnic' => $request->cnic,
        'phone' => $request->phone,
        'address' => $request->address,
        'status' => $request->status,
    ]);

    return redirect()
    ->route('beneficiaries.index')
    ->with('success', 'Beneficiary updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);

    $beneficiary->delete();

    return redirect()
    ->route('beneficiaries.index')
    ->with('success', 'Beneficiary deleted successfully.');
    }
    
    public function changeStatus(Beneficiary $beneficiary, $status)
{
    if (!in_array($status, ['Pending', 'Approved', 'Rejected'])) {
        abort(404);
    }

    $beneficiary->update([
        'status' => $status,
    ]);

    return redirect()
        ->route('beneficiaries.index')
        ->with('success', 'Status updated successfully.');
}
}
