<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">Batch QR Codes</h1>
            <div>
                <button onclick="window.print()"
                    class="flex items-center px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    <iconify-icon icon="mdi:printer" class="mr-2 text-xl"></iconify-icon> Print QR Codes
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 print:grid-cols-2">
            @foreach ($orders as $order)
                <div class="p-4 bg-white rounded-lg shadow-md print:break-inside-avoid">
                    <div class="mb-3 text-center">
                        <h3 class="text-lg font-semibold">Order #{{ $order->formatted_order_id }}</h3>
                        <p class="text-sm text-gray-600">{{ $order->user->first_name }} {{ $order->user->last_name }}
                        </p>
                    </div>

                    <div class="flex justify-center mb-4">
                        <div class="p-2 bg-white border border-gray-200 rounded-lg">
                            {!! QrCode::size(200)->generate(route('distributors.orders.verify', $order->qr_token)) !!}
                        </div>
                    </div>

                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-700">Scan to verify payment & delivery</p>
                        <p class="mt-1 text-xs text-gray-500">Generated: {{ now()->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .container,
            .container * {
                visibility: visible;
            }

            .container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            button,
            a {
                display: none !important;
            }
        }
    </style>
</x-distributor-layout>
