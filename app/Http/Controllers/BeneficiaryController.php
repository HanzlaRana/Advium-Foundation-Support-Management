<?php
namespace App\Http\Controllers;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        'cnic' => 'required|unique:beneficiaries,cnic|regex:/^\d{5}-\d{7}-\d{1}$/',
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
        'full_name'        => 'required',
        'cnic' => 'required|unique:beneficiaries,cnic,' . $id . '|regex:/^\d{5}-\d{7}-\d{1}$/',
        'phone'            => 'required',
        'address'          => 'required',
        'status'           => 'required',
        'photo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $beneficiary = Beneficiary::findOrFail($id);

    $photoName = $beneficiary->photo; // keep old photo by default

    if ($request->hasFile('photo')) {

        // Delete old photo from storage if it exists
        if ($beneficiary->photo) {
            \Storage::disk('public')->delete('beneficiaries/' . $beneficiary->photo);
        }

        // Store new photo
        $photoName = time() . '.' . $request->photo->extension();
        $request->photo->storeAs('beneficiaries', $photoName, 'public');
    }

    $beneficiary->update([
        'beneficiary_code' => $request->beneficiary_code,
        'full_name'        => $request->full_name,
        'cnic'             => $request->cnic,
        'phone'            => $request->phone,
        'address'          => $request->address,
        'status'           => $request->status,
        'photo'            => $photoName,
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

    // Delete photo from storage before deleting record
    if ($beneficiary->photo) {
        \Storage::disk('public')->delete('beneficiaries/' . $beneficiary->photo);
    }

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

                  public function exportPdf()
    {
        $beneficiaries = Beneficiary::all();

        $pdf = Pdf::loadView('beneficiaries.pdf', compact('beneficiaries'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('beneficiaries-' . date('Y-m-d') . '.pdf');
    }
public function exportExcel()
{
    $beneficiaries = Beneficiary::all();

    $filename = 'beneficiaries-' . date('Y-m-d') . '.csv';

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function () use ($beneficiaries) {
        $file = fopen('php://output', 'w');

        // Header row
        fputcsv($file, ['#', 'Code', 'Full Name', 'CNIC', 'Phone', 'Address', 'Status', 'Registered']);

        // Data rows
        foreach ($beneficiaries as $index => $beneficiary) {
            fputcsv($file, [
                $index + 1,
                $beneficiary->beneficiary_code,
                $beneficiary->full_name,
                $beneficiary->cnic,
                $beneficiary->phone,
                $beneficiary->address,
                $beneficiary->status,
                $beneficiary->created_at->format('d M Y'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}
