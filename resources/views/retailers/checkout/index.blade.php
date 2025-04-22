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
                                            src="{{ asset('storage/' . $product['product']->image) }}"
                                            alt="{{ $product['product']->product_name }}">
                                        <div>
                                            <p class="font-semibold">{{ $product['product']->product_name }}</p>
                                            <p class="text-sm text-gray-600">Qty: {{ $product['quantity'] }}</p>

                                            <!-- Display discount information -->
                                            @if ($product['applied_discount'])
                                                <p class="text-sm text-green-600">
                                                    Discount: {{ $product['applied_discount'] }}
                                                </p>
                                                @if ($product['discount_amount'] > 0)
                                                    <p class="text-sm text-green-600">
                                                        -₱{{ number_format($product['discount_amount'], 2) }}
                                                    </p>
                                                @endif
                                                @if ($product['free_items'] > 0)
                                                    <p class="text-sm text-green-600">
                                                        +{{ $product['free_items'] }} free item(s)
                                                    </p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if ($product['discount_amount'] > 0)
                                            <p class="text-sm text-gray-500 line-through">
                                                ₱{{ number_format($product['original_subtotal'], 2) }}</p>
                                        @endif
                                        <p class="font-bold">₱{{ number_format($product['final_subtotal'], 2) }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Pagination links -->
                            <div class="flex justify-end mt-6">
                                {{ $checkoutProducts->links() }}
                            </div>
                        </div>
                        <div class="mt-6 text-right">
                            <p class="text-2xl font-bold">
                                Total: ₱ {{ number_format($grandTotal, 2) }}
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
                            src="{{ Auth::user()->retailerProfile && Auth::user()->retailerProfile->profile_picture ? asset('storage/' . Auth::user()->retailerProfile->profile_picture) : asset('img/default-profile.png') }}"
                            alt="Profile">
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
                        <p><strong>Address:</strong>
                            @if ($user->retailerProfile && $user->retailerProfile->barangay_name)
                                {{ $user->retailerProfile->barangay_name }},
                                {{ $user->retailerProfile->street ?? '' }}
                            @else
                                N/A
                            @endif
                        </p>
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
                    @if (isset($cart) && $cart)
                        <form id="orderForm"
                            action="{{ route('retailers.checkout.placeOrder', ['distributorId' => $cart->distributor_id]) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="cart_id" value="{{ $cart->id }}">
                            <input type="hidden" name="total_amount" value="{{ $grandTotal }}">

                            <div class="flex flex-col space-y-4">
                                <div class="flex items-center">
                                    <input type="radio" id="default_address" name="delivery_option" value="default"
                                        checked class="form-radio">
                                    <label for="default_address" class="ml-2">
                                        Use my default address:
                                        <span
                                            class="font-medium">{{ $user->retailerProfile->barangay_name ?? 'Unknown' }},
                                            {{ $user->retailerProfile->street ?? '' }}</span>
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="new_address" name="delivery_option" value="other"
                                        class="form-radio">
                                    <label for="new_address" class="ml-2">Deliver to a different address</label>
                                </div>
                                <!-- New address input, hidden by default -->
                                <div id="newAddressInput" class="hidden space-y-3">
                                    <div>
                                        <label for="new_barangay"
                                            class="block text-sm font-medium text-gray-700">Barangay</label>
                                        <select id="new_barangay" name="new_barangay"
                                            class="block w-full px-3 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                            <option value="">Select Barangay</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="new_street" class="block text-sm font-medium text-gray-700">Street
                                            Address</label>
                                        <textarea id="new_street" name="new_street" rows="2"
                                            class="block w-full px-3 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                            placeholder="Enter street address"></textarea>
                                    </div>

                                    <input type="hidden" id="new_delivery_address" name="new_delivery_address"
                                        value="">
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full px-4 py-3 mt-6 text-white transition-colors duration-200 bg-green-600 rounded-xl hover:bg-green-700">
                                Place Order
                            </button>
                        </form>
                    @else
                        <div class="p-4 mb-4 rounded bg-red-50">
                            <p class="text-red-600">Unable to process checkout due to missing cart or distributor
                                information.</p>
                            <p class="mt-2">
                                <a href="{{ route('retailers.cart.index') }}" class="text-blue-600 underline">Return to
                                    your cart</a> and try again or contact support if the problem persists.
                            </p>
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

        // Load barangays when selecting "different address" option
        if (newAddressRadio) {
            // Add to your existing event listener
            newAddressRadio.addEventListener("change", function() {
                if (this.checked) {
                    newAddressInput.classList.remove("hidden");
                    loadBarangays();
                }
            });

            const newBarangaySelect = document.getElementById("new_barangay");
            const newStreetInput = document.getElementById("new_street");
            const newDeliveryAddressInput = document.getElementById("new_delivery_address");

            // Function to load barangays
            function loadBarangays() {
                fetch('/barangays/093170')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Clear previous options
                        newBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';

                        // Add options
                        data.forEach(barangay => {
                            const option = document.createElement('option');
                            option.value = barangay.code;
                            option.textContent = barangay.name;
                            option.setAttribute('data-name', barangay.name);
                            newBarangaySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading barangays:', error);
                    });
            }

            // Update hidden input when either barangay or street changes
            newBarangaySelect.addEventListener('change', updateNewDeliveryAddress);
            newStreetInput.addEventListener('input', updateNewDeliveryAddress);

            function updateNewDeliveryAddress() {
                const selectedOption = newBarangaySelect.options[newBarangaySelect.selectedIndex];
                const barangayName = selectedOption.textContent;
                const street = newStreetInput.value;

                if (barangayName && barangayName !== 'Select Barangay') {
                    newDeliveryAddressInput.value = street ? `${barangayName}, ${street}` : barangayName;
                } else {
                    newDeliveryAddressInput.value = street;
                }
            }
        }

        // Add form validation to original submit handler
        orderForm.addEventListener("submit", function(e) {
            e.preventDefault();

            // Check if new address is selected but no barangay is chosen
            if (newAddressRadio.checked) {
                const newBarangaySelect = document.getElementById("new_barangay");
                if (newBarangaySelect.value === "") {
                    Swal.fire({
                        title: 'Missing Information',
                        text: 'Please select a barangay for your delivery address',
                        icon: 'warning',
                    });
                    return;
                }

                // Ensure the hidden field is updated
                updateNewDeliveryAddress();
            }

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
