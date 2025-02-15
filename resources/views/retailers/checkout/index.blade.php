<x-app-layout>
    <x-dashboard-nav />
    <div class="container py-8 mx-auto">
        <div class="flex flex-col gap-8 md:flex-row">
            <!-- Left Column: Checkout Products Card -->
            <div class="md:w-2/3">
                <div class="p-6 bg-white rounded-lg shadow min-h-[500px]">
                    <h2 class="mb-4 text-2xl font-bold">Checkout Products</h2>
                    @if ($checkoutProducts->isEmpty())
                        <p>No products added to checkout.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($checkoutProducts as $product)
                                <div class="flex items-center justify-between p-4 border rounded">
                                    <div class="flex items-center space-x-4">
                                        <img class="object-cover w-16 h-16"
                                            src="{{ asset('storage/' . $product->product->image) }}"
                                            alt="{{ $product->product->product_name }}">
                                        <div>
                                            <p class="font-semibold">{{ $product->product->product_name }}</p>
                                            <p class="text-sm text-gray-600">Qty: {{ $product->quantity }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold">₱ {{ number_format($product->subtotal, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 text-right">
                            <p class="text-2xl font-bold">
                                Total: ₱ {{ number_format($checkoutProducts->sum('subtotal'), 2) }}
                            </p>
                        </div>
                    @endif
                </div>
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
                        <p><strong>Business Name:</strong> {{ $user->retailerProfile->business_name ?? 'N/A' }}</p>
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
                    @if (isset($checkoutProducts->first()->product->distributor_id))
                        <form id="orderForm"
                            action="{{ route('retailers.checkout.placeOrder', ['distributorId' => $checkoutProducts->first()->product->distributor_id]) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="total_amount" value="{{ $checkoutProducts->sum('subtotal') }}">

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
                                <!-- New address input, hidden by default -->
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
                        </form>
                    @else
                        <div class="p-4 mb-4 rounded bg-red-50">
                            <p class="text-red-600">Unable to process checkout due to missing distributor information.
                                Please contact support.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
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
        // Toggle display of new address input based on radio selection.
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
