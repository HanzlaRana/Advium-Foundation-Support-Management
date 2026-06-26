@extends('layouts.app')

@section('content')

<div class="container py-4">

    <h2 class="mb-4">Dashboard</h2>

    <div class="row">

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <h5>Total Beneficiaries</h5>
                    <h2>{{ $total }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning shadow">
                <div class="card-body">
                    <h5>Pending</h5>
                    <h2>{{ $pending }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <h5>Approved</h5>
                    <h2>{{ $approved }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-danger shadow">
                <div class="card-body">
                    <h5>Rejected</h5>
                    <h2>{{ $rejected }}</h2>
                </div>
            </div>
        </div>

    </div>

    <a href="{{ route('beneficiaries.index') }}" class="btn btn-primary">
        Manage Beneficiaries
    </a>

</div>

@endsection