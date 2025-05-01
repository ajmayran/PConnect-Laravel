<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Report</title>
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
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .summary-data {
            font-size: 12px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-header {
            background-color: #f2f2f2;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
            font-weight: bold;
        }
        .status-delivered {
            background-color: #d1e7dd;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .status-out {
            background-color: #fff3cd;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .status-failed {
            background-color: #f8d7da;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Delivery Report</h1>
        <p>Period: {{ ucfirst($period) }} ({{ $startDate->format('M d, Y') }} - {{ \Carbon\Carbon::now()->format('M d, Y') }})</p>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('M d, Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-title">Delivery Summary</div>
        <div class="summary-data">
            <p>Total Trucks: {{ $trucks->count() }}</p>
            <p>Total Deliveries: {{ $trucks->sum(function($truck) { return $truck->deliveries->count(); }) }}</p>
            <p>Completed Deliveries: {{ $trucks->sum(function($truck) { return $truck->deliveries->where('status', 'delivered')->count(); }) }}</p>
            <p>Ongoing Deliveries: {{ $trucks->sum(function($truck) { return $truck->deliveries->where('status', 'out_for_delivery')->count(); }) }}</p>
        </div>
    </div>

    <!-- Out for Delivery Section -->
    <div class="section">
        <div class="section-header">Out for Delivery</div>
        <table>
            <thead>
                <tr>
                    <th>Truck</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Est. Delivery</th>
                </tr>
            </thead>
            <tbody>
                @php $outForDeliveryCount = 0; @endphp
                
                @foreach($trucks as $truck)
                    @php 
                        $outForDeliveries = $truck->deliveries->where('status', 'out_for_delivery');
                        $outForDeliveryCount += $outForDeliveries->count();
                    @endphp
                    
                    @foreach($outForDeliveries as $delivery)
                        <tr>
                            <td>{{ $truck->plate_number }}</td>
                            <td>{{ $delivery->order->formatted_order_id ?? 'N/A' }}</td>
                            <td>
                                {{ $delivery->order->user->first_name ?? '' }} {{ $delivery->order->user->last_name ?? '' }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($delivery->created_at)->format('M d, Y') }}</td>
                            <td>{{ $delivery->estimated_delivery ? \Carbon\Carbon::parse($delivery->estimated_delivery)->format('M d, Y') : 'Not specified' }}</td>
                        </tr>
                    @endforeach
                @endforeach
                
                @if($outForDeliveryCount == 0)
                    <tr>
                        <td colspan="5" style="text-align: center;">No deliveries currently out for delivery</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Delivered Section -->
    <div class="section">
        <div class="section-header">Delivered</div>
        <table>
            <thead>
                <tr>
                    <th>Truck</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Delivery Date</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                @php $deliveredCount = 0; @endphp
                
                @foreach($trucks as $truck)
                    @php 
                        $deliveredItems = $truck->deliveries->where('status', 'delivered'); 
                        $deliveredCount += $deliveredItems->count();
                    @endphp
                    
                    @foreach($deliveredItems as $delivery)
                        <tr>
                            <td>{{ $truck->plate_number }}</td>
                            <td>{{ $delivery->order->formatted_order_id ?? 'N/A' }}</td>
                            <td>
                                {{ $delivery->order->user->first_name ?? '' }} {{ $delivery->order->user->last_name ?? '' }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($delivery->updated_at)->format('M d, Y') }}</td>
                            <td>
                                @if($delivery->order && $delivery->order->payment)
                                    @if($delivery->order->payment->payment_status === 'paid')
                                        <span class="status-delivered">Paid</span>
                                    @else
                                        <span class="status-failed">Unpaid</span>
                                    @endif
                                @else
                                    No Data
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                
                @if($deliveredCount == 0)
                    <tr>
                        <td colspan="5" style="text-align: center;">No completed deliveries in this period</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Truck Details Section -->
    <div class="section">
        <div class="section-header">Truck Details</div>
        
        @foreach($trucks as $truck)
            <div style="margin-bottom: 20px;">
                <h3 style="margin-bottom: 10px;">{{ $truck->plate_number }}</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($truck->deliveries as $delivery)
                            <tr>
                                <td>{{ $delivery->order->formatted_order_id ?? 'N/A' }}</td>
                                <td>
                                    {{ $delivery->order->user->first_name ?? '' }} {{ $delivery->order->user->last_name ?? '' }}
                                </td>
                                <td>
                                    @if($delivery->status === 'delivered')
                                        <span class="status-delivered">Delivered</span>
                                    @elseif($delivery->status === 'out_for_delivery')
                                        <span class="status-out">Out for Delivery</span>
                                    @elseif($delivery->status === 'failed')
                                        <span class="status-failed">Failed</span>
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($delivery->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center;">No deliveries found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(!$loop->last)
                <div style="margin-bottom: 20px;"></div>
            @endif
        @endforeach
    </div>

    <div class="footer">
        <p>PConnect - Distributor Delivery Report</p>
    </div>
</body>
</html>