@extends('layouts.app')

@section('breadcrumb')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('beneficiaries.index') }}">Beneficiaries</a>
        </li>
        <li class="breadcrumb-item active">Add Beneficiary</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h3>Add Beneficiary</h3>
    </div>
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('beneficiaries.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Beneficiary Code</label>
                <input type="text"
                       name="beneficiary_code"
                       class="form-control @error('beneficiary_code') is-invalid @enderror"
                       value="{{ old('beneficiary_code') }}">
                @error('beneficiary_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text"
                       name="full_name"
                       class="form-control @error('full_name') is-invalid @enderror"
                       value="{{ old('full_name') }}">
                @error('full_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">CNIC</label>
                <input type="text"
                       name="cnic"
                       class="form-control @error('cnic') is-invalid @enderror"
                       placeholder="42101-1234567-1"
                       pattern="\d{5}-\d{7}-\d{1}"
                       value="{{ old('cnic') }}">
                @error('cnic')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Format: 42101-1234567-1</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text"
                       name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address"
                          class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Photo</label>
                <input type="file"
                       name="photo"
                       class="form-control @error('photo') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png">
                @error('photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Save Beneficiary</button>
            <a href="{{ route('beneficiaries.index') }}" class="btn btn-secondary">Back</a>

        </form>
    </div>
</div>
@endsection