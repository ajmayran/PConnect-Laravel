<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
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
            margin-right: 15px;
            background-color: #f9f9f9;
            width: 22%;
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
        .status-completed {
            background-color: #d1e7dd;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .status-returned {
            background-color: #fff3cd;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .status-refunded {
            background-color: #e2d4f0;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .status-cancelled {
            background-color: #f8d7da;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orders Report</h1>
        <p>Period: {{ ucfirst($period) }} ({{ $startDate->format('M d, Y') }} - {{ \Carbon\Carbon::now()->format('M d, Y') }})</p>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('M d, Y H:i') }}</p>
    </div>

    <div class="summary">
        <h2>Summary</h2>
        <div class="summary-item">
            <span class="summary-label">Total Orders</span>
            <span class="summary-value">{{ $orders->count() }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Completed</span>
            <span class="summary-value">{{ $orders->where('status', 'completed')->count() }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Cancelled</span>
            <span class="summary-value">{{ $orders->where('status', 'cancelled')->count() }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Returned/Refunded</span>
            <span class="summary-value">
                {{ $returnedCount ?? 0 }}
            </span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Date</th>
                <th>Status</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->formatted_order_id }}</td>
                    <td>{{ $order->user->first_name ?? 'Unknown' }} {{ $order->user->last_name ?? '' }}</td>
                    <td>{{ $order->orderDetails->count() }}</td>
                    <td>₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($order->status === 'completed')
                            <span class="status-completed">Completed</span>
                        @elseif($order->status === 'cancelled')
                            <span class="status-cancelled">Cancelled</span>
                        @else
                            {{ ucfirst($order->status) }}
                        @endif

                        @if($order->returnRequests && $order->returnRequests->where('status', 'approved')->count() > 0)
                            <span class="status-returned">Returned</span>
                        @endif
                    </td>
                    <td>
                        @if($order->payment && $order->payment->payment_status === 'refunded')
                            <span class="status-refunded">Refunded</span>
                        @elseif($order->payment && $order->payment->payment_status === 'paid')
                            Paid
                        @else
                            {{ $order->payment ? ucfirst($order->payment->payment_status) : 'N/A' }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No orders found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>PConnect - Distributor Order Report - Page 1 of 1</p>
    </div>
</body>
</html>