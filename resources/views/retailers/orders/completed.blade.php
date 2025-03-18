@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-dashboard-nav />
    <div class="container px-4 py-8 mx-auto max-w-7xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Completed Orders</h1>
        </div>

        <x-retailer-orderstatus-tabs />

        @if ($orders->isEmpty())
            <div class="flex items-center justify-center p-8 mt-4 bg-white rounded-lg">
                <p class="text-lg text-gray-500">No completed orders</p>
            </div>
        @else
            <div class="mt-6 space-y-6">
                @foreach ($orders as $order)
                    @php
                        // Check if order was completed within the last 7 days
                        $orderDate = new \Carbon\Carbon($order->status_updated_at ?? $order->updated_at);
                        $isWithinReturnPeriod = $orderDate->diffInDays(now()) <= 7;
                        $daysLeft = 7 - $orderDate->diffInDays(now());

                        // Format the days left to be more readable
                        if ($daysLeft > 0) {
                            if ($daysLeft < 1) {
                                // Less than a day left, show hours
                                $hoursLeft = 24 - ($orderDate->diffInHours(now()) % 24);
                                $timeLeft = $hoursLeft . ' ' . Str::plural('hour', $hoursLeft);
                            } else {
                                // Round to whole days
                                $timeLeft = ceil($daysLeft) . ' ' . Str::plural('day', ceil($daysLeft));
                            }
                        } else {
                            $timeLeft = 'Expired';
                        }

                        // Check if return request was rejected
                        $rejectedReturnRequest = $order->returnRequests()->where('status', 'rejected')->exists();
                        
                        // Check if order has any other return request
                        $pendingOrApprovedReturnRequest = $order->returnRequests()
                            ->whereIn('status', ['pending', 'approved'])
                            ->exists();
                            
                        // General check for any return request
                        $hasReturnRequest = $rejectedReturnRequest || $pendingOrApprovedReturnRequest;
                    @endphp

                    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="space-y-1">
                                    <h2 class="text-xl font-bold text-gray-900">
                                        {{ $order->formatted_order_id }}
                                    </h2>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Distributor:</span>
                                        {{ $order->distributor->company_name }}
                                    </p>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Status:</span>
                                        <span
                                            class="px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full">
                                            Completed
                                        </span>
                                    </p>
                                    @if ($isWithinReturnPeriod && !$rejectedReturnRequest)
                                        <p class="text-sm text-orange-600">
                                            <span class="font-medium">Return Period:</span>
                                            {{ $timeLeft }} left
                                        </p>
                                    @elseif (!$rejectedReturnRequest)
                                        <p class="text-sm text-gray-500">
                                            <span class="font-medium">Return Period:</span>
                                            Expired
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-500">Order Date</p>
                                    <p class="text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                                    <p class="mt-1 text-sm font-medium text-gray-500">Completed On</p>
                                    <p class="text-gray-900">
                                        {{ ($order->status_updated_at ?? $order->updated_at)->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <div class="mt-6 -mx-6">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-sm font-medium tracking-wider text-left text-gray-500">
                                                Product
                                            </th>
                                            <th
                                                class="px-6 py-3 text-sm font-medium tracking-wider text-center text-gray-500">
                                                Quantity
                                            </th>
                                            <th
                                                class="px-6 py-3 text-sm font-medium tracking-wider text-right text-gray-500">
                                                Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($order->orderDetails as $detail)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    {{ $detail->product->product_name }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                    {{ $detail->quantity }}
                                                </td>
                                                <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">
                                                    ₱{{ number_format($detail->subtotal, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="2"
                                                class="px-6 py-4 text-sm font-bold text-right text-gray-900">
                                                Total Amount:
                                            </td>
                                            <td class="px-6 py-4 text-sm font-bold text-right text-gray-900">
                                                ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Delivery Address: {{ optional($order->orderDetails->first())->delivery_address }}
                                </div>
                                <div class="flex mt-4 space-x-3">
                                    <a href="{{ route('retailers.orders.view-receipt', $order->id) }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-500 rounded-lg hover:bg-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        View Receipt
                                    </a>

                                    @if ($rejectedReturnRequest)
                                        <span
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Return Declined
                                        </span>
                                    @elseif ($isWithinReturnPeriod)
                                        @if ($pendingOrApprovedReturnRequest)
                                            <span
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                </svg>
                                                Return Request Submitted
                                            </span>
                                        @else
                                            <a href="#" x-data
                                                @click.prevent="$dispatch('open-modal', 'request-return-{{ $order->id }}')"
                                                class="flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-yellow-500 rounded-lg hover:bg-yellow-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                </svg>
                                                Request Return
                                            </a>
                                        @endif
                                    @else
                                        <span
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                            </svg>
                                            Return Period Expired
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @foreach ($orders as $order)
        @php
            // Check if order was completed within the last 7 days
            $orderDate = new \Carbon\Carbon($order->status_updated_at ?? $order->updated_at);
            $isWithinReturnPeriod = $orderDate->diffInDays(now()) <= 7;
            
            // Check if return request was rejected
            $rejectedReturnRequest = $order->returnRequests()->where('status', 'rejected')->exists();
            
            // Check if order has any other return request
            $hasReturnRequest = $order->returnRequests()->exists();
        @endphp

        @if ($isWithinReturnPeriod && !$hasReturnRequest)
            <!-- Modal for Return Request -->
            <x-modal name="request-return-{{ $order->id }}" focusable>
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Request Return for Order #{{ $order->formatted_order_id }}
                    </h2>

                    <form method="POST" action="{{ route('retailers.orders.request-return', $order->id) }}"
                        class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-2">
                            <x-input-label for="reason" value="Reason for Return" />
                            <x-text-area-input id="reason" name="reason" class="block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="receipt" value="Upload Receipt (Image or PDF)" />
                            <input type="file" id="receipt" name="receipt"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                accept=".jpg,.jpeg,.png,.pdf" required />
                            <p class="mt-1 text-xs text-gray-500">JPG, JPEG, PNG or PDF (max 5MB)</p>
                            <x-input-error class="mt-2" :messages="$errors->get('receipt')" />
                        </div>

                        <div>
                            <h3 class="mb-2 text-sm font-medium text-gray-700">Products to Return</h3>
                            <div class="overflow-y-auto border border-gray-200 rounded-md max-h-60">
                                @foreach ($order->orderDetails as $detail)
                                    <div class="flex items-center justify-between p-3 border-b border-gray-200">
                                        <div class="flex items-start">
                                            <input type="checkbox" name="products[{{ $detail->id }}][selected]"
                                                id="product-{{ $detail->id }}"
                                                class="w-4 h-4 mt-1 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <label for="product-{{ $detail->id }}"
                                                class="ml-3 text-sm text-gray-700">
                                                {{ $detail->product->product_name }} (Qty: {{ $detail->quantity }})
                                            </label>
                                        </div>
                                        <div class="w-20">
                                            <x-input-label for="quantity-{{ $detail->id }}" value="Qty"
                                                class="sr-only" />
                                            <x-text-input id="quantity-{{ $detail->id }}"
                                                name="products[{{ $detail->id }}][quantity]" type="number"
                                                class="block w-full text-xs" min="1"
                                                max="{{ $detail->quantity }}" value="1" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-x-6">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                Cancel
                            </x-secondary-button>
                            <x-primary-button>
                                Submit Return Request
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endif
    @endforeach
</x-app-layout>