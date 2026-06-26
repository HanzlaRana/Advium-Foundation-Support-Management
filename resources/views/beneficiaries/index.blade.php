<!DOCTYPE html>
<html>
<head>
    <title>Beneficiaries Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

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
            <button type="submit" class="btn btn-primary">
                Search
            </button>
        </div>

        <div class="col-md-2">
            <a href="{{ route('beneficiaries.index') }}"
               class="btn btn-secondary">
                Reset
            </a>
        </div>
    </div>
</form>


            <a href="{{ route('beneficiaries.create') }}" class="btn btn-primary">
                Add Beneficiary
            </a>
        </div>

        <div class="card-body">

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>
@endif

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
    <th>ID</th>
    <th>Code</th>
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
                            <td>{{ $beneficiary->full_name }}</td>
                            <td>{{ $beneficiary->cnic }}</td>
                            <td>{{ $beneficiary->phone }}</td>
                            <td>
                                <span class="badge bg-warning">
                                    {{ $beneficiary->status }}
                                </span>
                            </td>
<td>
    <a href="{{ route('beneficiaries.edit', $beneficiary->id) }}"
       class="btn btn-warning btn-sm">
        Edit
    </a>

    <form action="{{ route('beneficiaries.destroy', $beneficiary->id) }}"
          method="POST"
          style="display:inline;">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-danger btn-sm"
                onclick="return confirm('Delete this beneficiary?')">
            Delete
        </button>
    </form>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No beneficiaries found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="mt-3">
                 {{ $beneficiaries->links() }}
            </div>

        </div>
    </div>

</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>