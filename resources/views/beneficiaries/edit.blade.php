<!DOCTYPE html>
<html>
<head>
    <title>Edit Beneficiary</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

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

            <form method="POST" action="{{ route('beneficiaries.update', $beneficiary->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Beneficiary Code</label>
                    <input type="text" name="beneficiary_code" class="form-control"
                           value="{{ $beneficiary->beneficiary_code }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control"
                           value="{{ $beneficiary->full_name }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">CNIC</label>
                    <input type="text" name="cnic" class="form-control"
                           value="{{ $beneficiary->cnic }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ $beneficiary->phone }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control">{{ $beneficiary->address }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="Pending" {{ $beneficiary->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ $beneficiary->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ $beneficiary->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Beneficiary
                </button>

                <a href="{{ route('beneficiaries.index') }}" class="btn btn-secondary">
                    Back
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>