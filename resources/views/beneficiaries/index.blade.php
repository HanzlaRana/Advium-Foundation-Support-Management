@extends('layouts.app')

@section('breadcrumb')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="breadcrumb-item active">Beneficiaries</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Beneficiaries Management</h3>

        <form method="GET" action="{{ route('beneficiaries.index') }}" class="mt-3 mb-3">
            <div class="row">
                <div class="col-md-6">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by Code, Name or CNIC"
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('beneficiaries.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <div class="d-flex gap-2">
    <a href="{{ route('beneficiaries.export.pdf') }}" class="btn btn-danger">
        Export PDF
    </a>
    <a href="{{ route('beneficiaries.export.excel') }}" class="btn btn-success">
        Export Excel
    </a>
    <a href="{{ route('beneficiaries.create') }}" class="btn btn-primary">
        Add Beneficiary
    </a>
</div>

    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" id="success-alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Photo</th>
                    <th>Name</th>

                    <th>CNIC</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($beneficiaries as $beneficiary)
                    <tr>
                        <td>{{ $beneficiary->id }}</td>
                        <td>{{ $beneficiary->beneficiary_code }}</td>
                        <td>
                            @if($beneficiary->photo)
                                <img src="{{ asset('storage/beneficiaries/'.$beneficiary->photo) }}"
                                     width="60" height="60"
                                     class="img-thumbnail"
                                     style="object-fit: cover;">
                            @else
                                No Photo
                            @endif
                        </td>
                        <td>{{ $beneficiary->full_name }}</td>
                        <td>{{ $beneficiary->cnic }}</td>
                        <td>{{ $beneficiary->phone }}</td>
                        <td>
                            @if($beneficiary->status == 'Pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($beneficiary->status == 'Approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('beneficiaries.show', $beneficiary->id) }}"
                               class="btn btn-info btn-sm">View</a>

                            <a href="{{ route('beneficiaries.edit', $beneficiary->id) }}"
                               class="btn btn-warning btn-sm">Edit</a>

                            <form action="{{ route('beneficiaries.destroy', $beneficiary->id) }}"
                                  method="POST"
                                  class="delete-form d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-btn">
                                    Delete
                                </button>
                            </form>

                            @if($beneficiary->status != 'Approved')
                                <form action="{{ route('beneficiaries.status', [$beneficiary->id, 'Approved']) }}"
                                      method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-success btn-sm">Approve</button>
                                </form>
                            @endif

                            @if($beneficiary->status != 'Rejected')
                                <form action="{{ route('beneficiaries.status', [$beneficiary->id, 'Rejected']) }}"
                                      method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-secondary btn-sm">Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No beneficiaries found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $beneficiaries->links() }}
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    const alertEl = document.getElementById('success-alert');
    if (alertEl) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
            bsAlert.close();
        }, 3000);
    }

    document.querySelectorAll('.delete-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This beneficiary will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection