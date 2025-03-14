<x-app-layout>
    <x-dashboard-nav />
    <div class="container px-4 py-8 mx-auto max-w-7xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Track Order</h1>
            <a href="{{ route('retailers.orders.index') }}" class="px-4 py-2 font-medium text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                Back to Orders
            </a>
        </div>
        
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="pb-4 mb-6 border-b">
                <div class="flex justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $delivery->order->formatted_order_id }}</h2>
                        <p class="text-gray-600">Ordered from: {{ $delivery->order->distributor->company_name }}</p>
                    </div>
                    <div class="text-right">
                        <div class="px-4 py-1 text-sm font-semibold text-white bg-green-600 rounded-full">
                            {{ ucfirst($delivery->status) }}
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Tracking #: {{ $delivery->tracking_number }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mb-8">
                <h3 class="mb-4 text-lg font-medium text-gray-800">Delivery Status Timeline</h3>
                
                <div class="relative">
                    <!-- Timeline -->
                    <div class="absolute left-6 top-0 h-full w-0.5 bg-gray-200"></div>
                    
                    <!-- Status Points -->
                    <div class="relative mb-8">
                        <div class="flex items-center">
                            <div class="z-10 flex items-center justify-center w-12 h-12 {{ $delivery->status == 'pending' ? 'bg-blue-500' : 'bg-green-500' }} rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Order Confirmed</h4>
                                <p class="text-sm text-gray-600">{{ $delivery->order->status_updated_at->format('F j, Y, g:i a') }}</p>
                                <p class="mt-1 text-gray-500">Your order has been confirmed and is being processed.</p>
                            </div>
                        </div>
                    </div>
                    
                    @if(in_array($delivery->status, ['in_transit', 'out_for_delivery', 'delivered']))
                    <div class="relative mb-8">
                        <div class="flex items-center">
                            <div class="z-10 flex items-center justify-center w-12 h-12 {{ $delivery->status == 'in_transit' ? 'bg-blue-500' : 'bg-green-500' }} rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">In Transit</h4>
                                <p class="text-sm text-gray-600">
                                    @if($delivery->status == 'in_transit' && $delivery->started_at)
                                        {{ \Carbon\Carbon::parse($delivery->started_at)->format('F j, Y, g:i a') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($delivery->updated_at)->format('F j, Y, g:i a') }}
                                    @endif
                                </p>
                                <p class="mt-1 text-gray-500">Your order is on its way to you.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(in_array($delivery->status, ['out_for_delivery', 'delivered']))
                    <div class="relative mb-8">
                        <div class="flex items-center">
                            <div class="z-10 flex items-center justify-center w-12 h-12 {{ $delivery->status == 'out_for_delivery' ? 'bg-blue-500' : 'bg-green-500' }} rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Out for Delivery</h4>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($delivery->updated_at)->format('F j, Y, g:i a') }}
                                </p>
                                <p class="mt-1 text-gray-500">Your order is out for delivery today.</p>
                                @if($delivery->estimated_delivery)
                                <p class="mt-1 font-medium text-blue-600">
                                    Estimated delivery: {{ \Carbon\Carbon::parse($delivery->estimated_delivery)->format('F j, Y') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($delivery->status == 'delivered')
                    <div class="relative">
                        <div class="flex items-center">
                            <div class="z-10 flex items-center justify-center w-12 h-12 bg-green-500 rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Delivered</h4>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($delivery->updated_at)->format('F j, Y, g:i a') }}
                                </p>
                                <p class="mt-1 font-medium text-green-600">Your order has been successfully delivered.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="pt-6 mt-8 border-t">
                <h3 class="mb-4 text-lg font-medium text-gray-800">Order Summary</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Product
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Quantity
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Price
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($delivery->order->orderDetails as $detail)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            @if($detail->product && $detail->product->image_path)
                                            <img class="w-10 h-10 rounded-full" src="{{ asset('storage/' . $detail->product->image_path) }}" alt="{{ $detail->product->product_name }}">
                                            @else
                                            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $detail->product ? $detail->product->product_name : 'Product Unavailable' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $detail->quantity }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">₱{{ number_format($detail->price, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">₱{{ number_format($detail->subtotal, 2) }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap">
                                    Total:
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">
                                    ₱{{ number_format($delivery->order->orderDetails->sum('subtotal'), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="pt-6 mt-8 border-t">
                <h3 class="mb-4 text-lg font-medium text-gray-800">Delivery Information</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <p class="text-gray-600"><span class="font-medium">Delivery Address:</span> {{ $delivery->order->orderDetails->first() ? $delivery->order->orderDetails->first()->delivery_address : 'Not available' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600"><span class="font-medium">Tracking Number:</span> {{ $delivery->tracking_number }}</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>