<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products Activity Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }
        h1 {
            color: #2E7D32;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .summary-item {
            width: 22%;
            margin-right: 2%;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .summary-label {
            display: block;
            font-size: 12px;
            color: #666;
        }
        .summary-value {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #2E7D32;
            margin-top: 5px;
        }
        .status-active {
            color: #2E7D32;
            font-weight: bold;
        }
        .status-removed {
            color: #C62828;
            font-weight: bold;
        }
        .section-title {
            margin-top: 30px;
            color: #2E7D32;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Products Activity Report</h1>
        <p>Period: {{ ucfirst($period) }} ({{ $startDate->format('M d, Y') }} - {{ \Carbon\Carbon::now()->format('M d, Y') }})</p>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('M d, Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Products Created</span>
            <span class="summary-value">{{ $products->where('action_type', 'created')->count() }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Products Removed</span>
            <span class="summary-value">{{ $products->where('action_type', 'deleted')->count() }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Units Sold</span>
            <span class="summary-value">{{ $products->sum('sold') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Active Products</span>
            <span class="summary-value">{{ $products->where('deleted_at', null)->count() }}</span>
        </div>
    </div>

    <h2 class="section-title">Product Activity by Date</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Products Added</th>
                <th>Products Removed</th>
                <th>Units Sold</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activityByDate as $activity)
                <tr>
                    <td>{{ $activity['date'] }}</td>
                    <td>{{ $activity['added'] }}</td>
                    <td>{{ $activity['removed'] }}</td>
                    <td>{{ $activity['sold'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No activity data available for this period</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="section-title">Product Details</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Current Stock</th>
                <th>Date Added</th>
                <th>Units Sold</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product['product_name'] }}</td>
                    <td>₱{{ number_format($product['price'], 2) }}</td>
                    <td>{{ $product['current_stock'] }}</td>
                    <td>{{ $product['created_at'] }}</td>
                    <td>{{ $product['sold'] }}</td>
                    <td>
                        @if($product['deleted_at'])
                            <span class="status-removed">Removed</span>
                        @else
                            <span class="status-active">Active</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No products found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>PConnect - Distributor Products Activity Report - Page 1 of 1</p>
    </div>
</body>
</html>