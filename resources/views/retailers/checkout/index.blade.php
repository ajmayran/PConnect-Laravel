<x-app-layout>
    <x-dashboard-nav />

    <div class="container py-8 mx-auto">
        <h1 class="mb-6 text-3xl font-bold">Checkout</h1>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Order Summary -->
            <div class="col-span-2">
                <div class="p-6 bg-white rounded-lg shadow">
                    <h2 class="mb-4 text-xl font-semibold">Order Summary</h2>
                    @foreach($cart->details as $item)
                        <div class="flex items-center justify-between py-4 border-b">
                            <div class="flex items-center">
                                <img src="{{ $item->product->image ? Storage::url($item->product->image) : asset('img/default-product.jpg') }}" 
                                     alt="{{ $item->product->product_name }}"
                                     class="object-cover w-16 h-16 mr-4 rounded"
                                     onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                                <div>
                                    <h3 class="font-semibold">{{ $item->product->product_name }}</h3>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">₱{{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-4 text-right">
                        <p class="text-xl font-bold">Total: ₱{{ number_format($total, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="col-span-1">
                <div class="p-6 bg-white rounded-lg shadow">
                    <h2 class="mb-4 text-xl font-semibold">Payment Details</h2>
                    <form action="{{ route('retailers.orders.store') }}" method="POST">
                        @csrf
                        <!-- Add payment form fields here -->
                        <button type="submit" 
                                class="w-full px-6 py-3 text-white bg-green-600 rounded-lg hover:bg-green-700">
                            Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>