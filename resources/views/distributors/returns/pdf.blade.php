<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 12px;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .text-center {
            text-align: center;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Returns and Refunds Report</h1>
        <p>Generated on: {{ now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Return ID</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Status</th>
                <th>Date</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($returns as $return)
                <tr>
                    <td>{{ $return->id }}</td>
                    <td>{{ $return->order->formatted_order_id ?? 'N/A' }}</td>
                    <td>{{ $return->retailer->first_name }} {{ $return->retailer->last_name }}</td>
                    <td>{{ $return->items->count() }} items</td>
                    <td>{{ ucfirst($return->status) }}</td>
                    <td>{{ $return->created_at->format('M d, Y') }}</td>
                    <td>
                        {{ number_format($return->items->sum(function($item) {
                            return $item->quantity * ($item->orderDetail->price ?? 0);
                        }), 2) }} PHP
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No returns found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>