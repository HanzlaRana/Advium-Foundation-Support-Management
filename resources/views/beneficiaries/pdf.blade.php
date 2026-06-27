<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Beneficiaries Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead {
            background-color: #343a40;
            color: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 7px 10px;
            text-align: left;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge-pending  { color: #856404; font-weight: bold; }
        .badge-approved { color: #155724; font-weight: bold; }
        .badge-rejected { color: #721c24; font-weight: bold; }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>

    <h2>Beneficiaries Report</h2>
    <div class="subtitle">Generated on {{ date('d M Y, h:i A') }}</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Full Name</th>
                <th>CNIC</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Status</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            @forelse($beneficiaries as $index => $beneficiary)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $beneficiary->beneficiary_code }}</td>
                    <td>{{ $beneficiary->full_name }}</td>
                    <td>{{ $beneficiary->cnic }}</td>
                    <td>{{ $beneficiary->phone }}</td>
                    <td>{{ $beneficiary->address }}</td>
                    <td>
                        @if($beneficiary->status == 'Pending')
                            <span class="badge-pending">Pending</span>
                        @elseif($beneficiary->status == 'Approved')
                            <span class="badge-approved">Approved</span>
                        @else
                            <span class="badge-rejected">Rejected</span>
                        @endif
                    </td>
                    <td>{{ $beneficiary->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">No beneficiaries found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Records: {{ $beneficiaries->count() }}
    </div>

</body>
</html>