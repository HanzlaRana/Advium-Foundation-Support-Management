@extends('layouts.app')

@section('content')

<div class="card shadow">

    <div class="card-header bg-primary text-white">
        <h3>Beneficiary Details</h3>
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <tr>
                <th width="30%">Beneficiary Code</th>
                <td>{{ $beneficiary->beneficiary_code }}</td>
            </tr>

           @if($beneficiary->photo)
    <div class="mb-3">
        <img src="{{ asset('storage/beneficiaries/'.$beneficiary->photo) }}"
             width="200"
             class="img-thumbnail">
    </div>
@endif

            <tr>
                <th>Full Name</th>
                <td>{{ $beneficiary->full_name }}</td>
            </tr>

            <tr>
                <th>CNIC</th>
                <td>{{ $beneficiary->cnic }}</td>
            </tr>

            <tr>
                <th>Phone</th>
                <td>{{ $beneficiary->phone }}</td>
            </tr>

            <tr>
                <th>Address</th>
                <td>{{ $beneficiary->address }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>

                    @if($beneficiary->status == 'Pending')
                        <span class="badge bg-warning">Pending</span>

                    @elseif($beneficiary->status == 'Approved')
                        <span class="badge bg-success">Approved</span>

                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif

                </td>
            </tr>

        </table>

        <a href="{{ route('beneficiaries.index') }}" class="btn btn-secondary">
            Back
        </a>

    </div>

</div>

@endsection