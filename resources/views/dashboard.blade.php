@extends('layouts.app')

@section('breadcrumb')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Home</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-4">
    <h2 class="mb-4">Dashboard</h2>

    {{-- Stat Cards --}}
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

    {{-- Monthly Chart --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">Monthly Registrations ({{ date('Y') }})</h5>
        </div>
        <div class="card-body">
            <canvas id="monthlyChart" height="100"></canvas>
        </div>
    </div>

    {{-- Status Breakdown + Quick Actions --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Status Breakdown</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('beneficiaries.index') }}" class="btn btn-primary mb-2 d-block">
                        Manage Beneficiaries
                    </a>
                    <a href="{{ route('beneficiaries.create') }}" class="btn btn-success mb-2 d-block">
                        Add New Beneficiary
                    </a>
                    <a href="{{ route('beneficiaries.index') }}?search=" class="btn btn-secondary d-block">
                        Search Beneficiaries
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Beneficiaries --}}
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Beneficiaries</h5>
            <a href="{{ route('beneficiaries.index') }}" class="btn btn-sm btn-primary">
                View All
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>CNIC</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent as $beneficiary)
                        <tr>
                            <td>{{ $beneficiary->beneficiary_code }}</td>
                            <td>{{ $beneficiary->full_name }}</td>
                            <td>{{ $beneficiary->cnic }}</td>
                            <td>
                                @if($beneficiary->status == 'Pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($beneficiary->status == 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $beneficiary->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No beneficiaries yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Bar Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Registrations',
                data: [
                    {{ $monthlyData[1] }},
                    {{ $monthlyData[2] }},
                    {{ $monthlyData[3] }},
                    {{ $monthlyData[4] }},
                    {{ $monthlyData[5] }},
                    {{ $monthlyData[6] }},
                    {{ $monthlyData[7] }},
                    {{ $monthlyData[8] }},
                    {{ $monthlyData[9] }},
                    {{ $monthlyData[10] }},
                    {{ $monthlyData[11] }},
                    {{ $monthlyData[12] }}
                ],
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Rejected'],
            datasets: [{
                data: [{{ $pending }}, {{ $approved }}, {{ $rejected }}],
                backgroundColor: [
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(25, 135, 84, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 193, 7, 1)',
                    'rgba(25, 135, 84, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection