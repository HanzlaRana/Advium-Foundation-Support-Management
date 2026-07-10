<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_type' => 'required|string',
            'cnic'     => 'nullable|string',
        ]);

        $file     = $request->file('file');
        $ext      = $file->getClientOriginalExtension();
        $cnic     = $request->cnic ? str_replace('-', '', $request->cnic) : 'unknown';
        $docType  = Str::slug($request->doc_type);
        $filename = "{$cnic}_{$docType}_" . time() . ".{$ext}";

        $path = $file->storeAs('documents', $filename, 'public');

        return response()->json([
            'success' => true,
            'path'    => $path,
            'url'     => Storage::disk('public')->url($path),
            'name'    => $filename,
        ]);
    }
}
