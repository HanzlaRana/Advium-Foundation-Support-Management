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
        <li class="breadcrumb-item active">Edit Beneficiary</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h3>Edit Beneficiary</h3>
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

        <form method="POST" action="{{ route('beneficiaries.update', $beneficiary->id) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Beneficiary Code</label>
                <input type="text"
                       name="beneficiary_code"
                       class="form-control @error('beneficiary_code') is-invalid @enderror"
                       value="{{ old('beneficiary_code', $beneficiary->beneficiary_code) }}">
                @error('beneficiary_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text"
                       name="full_name"
                       class="form-control @error('full_name') is-invalid @enderror"
                       value="{{ old('full_name', $beneficiary->full_name) }}">
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
                       value="{{ old('cnic', $beneficiary->cnic) }}">
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
                       value="{{ old('phone', $beneficiary->phone) }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address"
                          class="form-control @error('address') is-invalid @enderror">{{ old('address', $beneficiary->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="Pending" {{ old('status', $beneficiary->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ old('status', $beneficiary->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ old('status', $beneficiary->status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Photo</label>

                @if ($beneficiary->photo)
                    <div class="mb-2">
                        <img id="current-photo"
                             src="{{ asset('storage/beneficiaries/' . $beneficiary->photo) }}"
                             width="80" height="80"
                             class="rounded border"
                             alt="Current Photo">
                        <p class="text-muted small mt-1">
                            Current photo. Upload a new one to replace it.
                        </p>
                    </div>
                @else
                    <p class="text-muted small">No photo uploaded yet.</p>
                @endif

                <input type="file"
                       name="photo"
                       id="photo"
                       class="form-control @error('photo') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png">

                @error('photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Beneficiary</button>
            <a href="{{ route('beneficiaries.index') }}" class="btn btn-secondary">Back</a>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('photo').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                let img = document.getElementById('current-photo');
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'current-photo';
                    img.width = 80;
                    img.height = 80;
                    img.className = 'rounded border mb-2 d-block';
                    document.getElementById('photo').before(img);
                }
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection