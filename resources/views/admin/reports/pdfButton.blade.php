<!-- filepath: c:\Users\EMMAN\Documents\PConnect-Laravel\resources\views\admin\reports\pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>System Reports</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>System Reports</h1>
    <p>Time Range: {{ ucfirst(str_replace('_', ' ', $timeRange)) }}</p>
    <table>
        <thead>
            <tr>
                <th>Distributor</th>
                <th>Total Orders</th>
                <th>Products Sold</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->distributor->company_name }}</td>
                    <td>{{ $report->total_orders }}</td>
                    <td>{{ $report->total_products_sold }}</td>
                    <td>${{ number_format($report->total_revenue, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>