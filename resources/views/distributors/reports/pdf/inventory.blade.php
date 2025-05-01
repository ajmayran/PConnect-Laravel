<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary h2 {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .summary-item {
            display: inline-block;
            border: 1px solid #ddd;
            padding: 10px;
            margin-right: 10px;
            background-color: #f9f9f9;
            width: 28%;
        }
        .summary-label {
            display: block;
            font-size: 10px;
            color: #666;
        }
        .summary-value {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .type-in {
            background-color: #d1e7dd;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .type-out {
            background-color: #f8d7da;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Report</h1>
        <p>Period: {{ ucfirst($period) }} ({{ $startDate->format('M d, Y') }} - {{ \Carbon\Carbon::now()->format('M d, Y') }})</p>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('M d, Y H:i') }}</p>
    </div>

    <div class="summary">
        <h2>Summary</h2>
        <div class="summary-item">
            <span class="summary-label">Total Stock In</span>
            <span class="summary-value">{{ $inventory->where('type', 'in')->sum('quantity') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Stock Out</span>
            <span class="summary-value">{{ $inventory->where('type', 'out')->sum('quantity') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Net Inventory Change</span>
            <span class="summary-value">{{ $inventory->where('type', 'in')->sum('quantity') - $inventory->where('type', 'out')->sum('quantity') }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Batch</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Supplier</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventory as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                    <td>{{ $movement->product->product_name }}</td>
                    <td>{{ $movement->batch ? $movement->batch->batch_number : 'N/A' }}</td>
                    <td>
                        @if($movement->type == 'in')
                            <span class="type-in">Stock In</span>
                        @else
                            <span class="type-out">Stock Out</span>
                        @endif
                    </td>
                    <td>{{ $movement->quantity }}</td>
                    <td>{{ $movement->batch ? $movement->batch->supplier : 'N/A' }}</td>
                    <td>{{ $movement->notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No inventory movement records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>PConnect - Distributor Inventory Report - Page 1 of 1</p>
    </div>
</body>
</html>