<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue Report</title>
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
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Revenue Report</h1>
        <p>Period: {{ ucfirst($period) }} ({{ $startDate->format('M d, Y') }} - {{ \Carbon\Carbon::now()->format('M d, Y') }})</p>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('M d, Y H:i') }}</p>
    </div>

    <div class="summary">
        <h2>Summary</h2>
        <div class="summary-item">
            <span class="summary-label">Total Revenue</span>
            <span class="summary-value">₱{{ number_format($totalRevenue, 2) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Orders</span>
            <span class="summary-value">{{ $revenueData->sum('orders_count') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Avg. Revenue per Order</span>
            <span class="summary-value">
                @if($revenueData->sum('orders_count') > 0)
                    ₱{{ number_format($totalRevenue / $revenueData->sum('orders_count'), 2) }}
                @else
                    ₱0.00
                @endif
            </span>
        </div>
    </div>

    <div class="section">
        <h2>Top Selling Products</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Units Sold</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $product)
                    <tr>
                        <td>{{ $product->product_name }}</td>
                        <td>₱{{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->total_sold }}</td>
                        <td>₱{{ number_format($product->total_revenue, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No products sold</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Daily Revenue Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($revenueData as $data)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($data->date)->format('M d, Y') }}</td>
                        <td>{{ $data->orders_count }}</td>
                        <td>₱{{ number_format($data->total_revenue, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">No revenue data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>PConnect - Distributor Revenue Report - Page 1 of 1</p>
    </div>
</body>
</html>