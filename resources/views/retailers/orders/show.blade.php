<x-app-layout>
    <x-dashboard-nav />
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="flex py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="flex-1 space-y-6 lg:pl-8">
            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <!-- Order Header Section -->
                <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-200">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->formatted_order_id }}</h1>
                        <p class="mt-1 text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Order Status Badge -->
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ 
                            $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                            ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                            ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                            ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                            'bg-gray-100 text-gray-800'))) 
                        }}">
                            {{ ucfirst($order->status) }}
                        </span>
                        
                        <!-- Delivery Status Badge -->
                        @if($order->delivery)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ 
                            $order->delivery->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                            ($order->delivery->status === 'out_for_delivery' ? 'bg-purple-100 text-purple-800' : 
                            'bg-blue-100 text-blue-800') 
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $order->delivery->status)) }}
                        </span>
                        @endif
                        
                        <!-- Payment Status Badge -->
                        @if($order->payment)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ 
                            $order->payment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                            'bg-yellow-100 text-yellow-800' 
                        }}">
                            {{ ucfirst($order->payment->payment_status) }}
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Order Details Section -->
                <div class="space-y-6">
                    <!-- Products Section -->
                    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 bg-gray-50 sm:px-6">
                            <h3 class="text-lg font-medium text-gray-900">Products</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Product</th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Price</th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Quantity</th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($order->orderDetails as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-10 h-10">
                                                    @if($detail->product && $detail->product->image)
                                                    <img class="object-cover w-10 h-10 rounded-full" src="{{ asset('storage/' . $detail->product->image) }}" alt="{{ $detail->product->product_name }}">
                                                    @else
                                                    <div class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-full">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $detail->product->product_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">₱{{ number_format($detail->price, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $detail->quantity }}</div>
                                            @if($detail->free_items > 0)
                                                <div class="text-xs text-green-600">+{{ $detail->free_items }} free</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">₱{{ number_format($detail->subtotal, 2) }}</div>
                                            @if($detail->discount_amount > 0)
                                                <div class="text-xs text-green-600">Saved ₱{{ number_format($detail->discount_amount, 2) }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 font-medium text-right text-gray-500">Total Amount:</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">₱{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Delivery Information -->
                        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 bg-gray-50 sm:px-6">
                                <h3 class="text-lg font-medium text-gray-900">Delivery Information</h3>
                            </div>
                            <div class="px-4 py-5 sm:p-6">
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Delivery Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $order->orderDetails->first()->delivery_address ?? 'N/A' }}</dd>
                                    </div>
                                    
                                    @if($order->delivery)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Tracking Number</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $order->delivery->tracking_number ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $order->delivery->updated_at->format('M d, Y h:i A') }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                        
                        <!-- Distributor & Payment Information -->
                        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 bg-gray-50 sm:px-6">
                                <h3 class="text-lg font-medium text-gray-900">Distributor & Payment</h3>
                            </div>
                            <div class="px-4 py-5 sm:p-6">
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Distributor</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $order->distributor->company_name }}</dd>
                                    </div>
                                    
                                    @if($order->payment)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->payment->payment_method ?? 'Cash on Delivery') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->payment->payment_status ?? 'Unpaid') }}</dd>
                                    </div>
                                    @if($order->payment->payment_status === 'paid' && $order->payment->paid_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Paid On</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->payment->paid_at)->format('M d, Y h:i A') }}</dd>
                                    </div>
                                    @endif
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end pt-6 mt-6 border-t border-gray-200">
                        <a href="{{ url()->previous() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-transparent rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>