<x-app-layout>
    <x-dashboard-nav />
    <div class="container py-8 mx-auto">
        @if ($checkoutProducts->count())
            <form id="orderForm" action="{{ route('retailers.checkout.placeOrderAll') }}" method="POST">
                @csrf
                <div class="flex flex-col gap-8 md:flex-row">
                    <!-- Left Column: Checkout Products -->
                    <div class="md:w-2/3">
                        @foreach ($checkoutProducts->groupBy('product.distributor_id') as $distributorId => $products)
                            <div class="p-6 mb-6 bg-white rounded-lg shadow-md cart-group"
                                data-distributor-id="{{ $distributorId }}">
                                <h2 class="mb-4 text-2xl font-semibold">
                                    Checkout Products for
                                    <span>{{ $products->first()->product->distributor->company_name ?? $distributorId }}</span>
                                </h2>
                                @if ($products->isEmpty())
                                    <p>No products added for this distributor.</p>
                                @else
                                    <div class="space-y-4">

                                        <div class="flex px-4 py-2 font-bold border-b border-gray-200">
                                            <span class="flex-1">Product</span>
                                            <span class="w-24 text-right">Price</span>
                                            <span class="w-24 text-right">Subtotal</span>
                                        </div>
                                        @foreach ($products as $product)
                                            <div class="flex items-center px-4 py-4 border-b border-gray-100">
                                                <div class="flex items-center flex-1">
                                                    <img class="object-cover w-16 h-16 mr-4 rounded"
                                                        src="{{ asset('storage/' . $product->product->image) }}"
                                                        alt="{{ $product->product->product_name }}">
                                                    <div>
                                                        <p class="font-semibold">
                                                            {{ $product->product->product_name }}</p>
                                                        <p class="text-sm text-gray-600">Qty:
                                                            {{ $product->quantity }}</p>
                                                    </div>
                                                </div>
                                                <div class="w-24 text-right">
                                                    ₱{{ number_format($product->product->price, 2) }}
                                                </div>
                                                <div class="w-24 text-right">
                                                    ₱{{ number_format($product->subtotal, 2) }}
                                                </div>
                                            </div>
                                            <input type="hidden" name="cart_details[{{ $distributorId }}][]"
                                                value="{{ $product->id }}">
                                        @endforeach
                                        <div class="flex px-4 py-4 font-semibold border-t border-gray-200">
                                            <span class="flex-1 text-right">Total Amount:</span>
                                            <span
                                                class="w-24 text-right">₱{{ number_format($products->sum('subtotal'), 2) }}</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="carts[]" value="{{ $products->first()->cart_id }}">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Right Column: Retailer Profile & Delivery Address -->
                    <div class="space-y-8 md:w-1/3">
                        <!-- Retailer Profile Card -->
                        <div class="p-6 bg-white rounded-lg shadow">
                            <h2 class="mb-4 text-2xl font-bold">Retailer Profile</h2>
                            <div class="flex items-center space-x-4">
                                <img class="object-cover w-16 h-16 rounded-full"
                                    src="{{ isset($user->retailerProfile->profile_picture) ? asset('storage/' . $user->retailerProfile->profile_picture) : asset('default-profile.png') }}"
                                    alt="Profile Image">
                                <div>
                                    <p class="font-bold text-gray-800">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </p>
                                    <p class="text-gray-600">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p><strong>Business Name:</strong> {{ $user->retailerProfile->business_name ?? 'N/A' }}
                                </p>
                                <p><strong>Phone:</strong> {{ $user->retailerProfile->phone ?? 'N/A' }}</p>
                                <p><strong>Address:</strong> {{ $user->retailerProfile->address ?? 'N/A' }}</p>
                            </div>

                            <div class="pt-4 mt-6 border-t">
                                <p class="text-xl font-bold">
                                    Grand Total: ₱ {{ number_format($grandTotal, 2) }}
                                </p>
                            </div>
                        </div>

                        <!-- Delivery Address Card -->
                        <div class="p-6 bg-white rounded-lg shadow">
                            <h2 class="mb-4 text-xl font-semibold">Delivery Address</h2>
                            <div class="flex flex-col space-y-4">
                                <div class="flex items-center">
                                    <input type="radio" id="default_address" name="delivery_option" value="default"
                                        checked class="form-radio">
                                    <label for="default_address" class="ml-2">
                                        Use my default address:
                                        <span class="font-medium">{{ $user->retailerProfile->address ?? 'N/A' }}</span>
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="new_address" name="delivery_option" value="other"
                                        class="form-radio">
                                    <label for="new_address" class="ml-2">Deliver to a different address</label>
                                </div>

                                <div id="newAddressInput" class="hidden">
                                    <input type="text" name="new_delivery_address"
                                        placeholder="Enter new delivery address"
                                        class="w-full px-3 py-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full px-4 py-3 mt-6 text-white transition-colors duration-200 bg-green-600 rounded-xl hover:bg-green-700">
                                Place Order
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <p>No distributor carts available.</p>
        @endif
    </div>
    @include('components.footer')

    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        const newAddressRadio = document.getElementById("new_address");
        const defaultAddressRadio = document.getElementById("default_address");
        const newAddressInput = document.getElementById("newAddressInput");

        newAddressRadio.addEventListener("change", function() {
            if (this.checked) {
                newAddressInput.classList.remove("hidden");
            }
        });

        defaultAddressRadio.addEventListener("change", function() {
            if (this.checked) {
                newAddressInput.classList.add("hidden");
            }
        });

        const orderForm = document.getElementById("orderForm");
        orderForm.addEventListener("submit", function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Confirm Order?',
                text: 'Do you want to place this order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, place order',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    orderForm.submit();
                }
            });
        });
    </script>
</x-app-layout>
