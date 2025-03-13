<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Order QR Code</h1>
            <a href="{{ route('distributors.orders.index') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                <i class="mr-1 fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-sm">
            <div class="flex flex-col items-center space-y-4">
                <div class="p-4 bg-white border rounded-lg shadow-sm">
                    <!-- QR Code -->
                    <div class="flex justify-center">
                        {!! QrCode::size(250)->generate($verificationUrl) !!}
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <h2 class="text-xl font-semibold">Order: {{ $order->formatted_order_id ?? $order->id }}</h2>
                    <p class="mt-2 text-gray-600">
                        Amount: ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}
                    </p>
                    <p class="mt-2 text-gray-600">
                        Retailer: {{ $order->user->first_name }} {{ $order->user->last_name }}
                    </p>
                </div>
                
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Scan this QR code to verify order, confirm payment, or delivery</p>
                </div>
                
                <div class="flex flex-col items-center mt-4 space-y-2">
                    
                    <button onclick="printQRCode()" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        <i class="mr-1 fas fa-print"></i> Print QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function printQRCode() {
            window.print();
        }
    </script>
    @endpush
</x-distributor-layout>