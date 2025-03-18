<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt #{{ $order->formatted_order_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .receipt {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background-color: #f8f8f8;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
        .print-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <img src="{{ asset('img/Pconnect Logo.png') }}" alt="PConnect Logo" class="logo">
            <h1>Order Receipt</h1>
            <p>Thank you for your order!</p>
        </div>
        
        <div class="order-info">
            <div>
                <p><strong>Order ID:</strong> {{ $order->formatted_order_id }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('F j, Y') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            </div>
            <div>
                <p><strong>Retailer:</strong> {{ $order->user->first_name }} {{ $order->user->last_name }}</p>
                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                <p><strong>Phone:</strong> {{ $order->user->retailerProfile->phone ?? 'N/A' }}</p>
            </div>
        </div>
        
        <div>
            <h3>Delivery Information</h3>
            <p><strong>Address:</strong> {{ $order->orderDetails->first()->delivery_address ?? 'N/A' }}</p>
            @if($order->delivery)
                <p><strong>Tracking Number:</strong> {{ $order->delivery->tracking_number ?? 'N/A' }}</p>
                <p><strong>Delivery Date:</strong> {{ $order->delivery->updated_at ? $order->delivery->updated_at->format('F j, Y') : 'Pending' }}</p>
            @endif
        </div>
        
        <div>
            <h3>Order Details</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderDetails as $detail)
                        <tr>
                            <td>{{ $detail->product ? $detail->product->product_name : 'Product not available' }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>₱{{ number_format($detail->price, 2) }}</td>
                            <td>₱{{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Total:</td>
                        <td>₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div>
            <h3>Payment Information</h3>
            @if($order->payment)
                <p><strong>Payment Status:</strong> {{ ucfirst($order->payment->payment_status) }}</p>
                @if($order->payment->paid_at)
                    <p><strong>Paid On:</strong> {{ \Carbon\Carbon::parse($order->payment->paid_at)->format('F j, Y') }}</p>
                @endif
            @else
                <p>No payment information available.</p>
            @endif
        </div>
        
        <div class="footer">
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p>&copy; {{ date('Y') }} PConnect - All rights reserved</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button class="print-button" onclick="window.print()">Print Receipt</button>
        <a href="{{ route('retailers.orders.download-receipt', $order->id) }}" class="print-button" style="text-decoration: none; display: inline-block; background-color: #2196F3;">Download PDF</a>
        <button onclick="window.history.back()" class="print-button" style="text-decoration: none; display: inline-block; background-color: #607D8B;">Back to Orders</button>
    </div>
    
    <script>
        // Auto print when directly downloading
        if (window.location.href.includes('download=true')) {
            window.print();
        }
    </script>
</body>
</html>