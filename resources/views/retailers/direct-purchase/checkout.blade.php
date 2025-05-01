<x-app-layout>
    <x-dashboard-nav />
    <div class="container py-8 mx-auto">
        <div class="flex flex-col gap-8 md:flex-row">
            <!-- Left Column: Checkout Products Card -->
            <div class="md:w-2/3">
                <div class="p-6 bg-white rounded-lg shadow min-h-[500px]">
                    <h2 class="mb-4 text-2xl font-bold">Checkout Products</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border rounded">
                            <div class="flex items-center space-x-4">
                                <img class="object-cover w-16 h-16"
                                    src="{{ $directProduct->image ? asset('storage/products/' . basename($directProduct->image)) : asset('img/default-product.jpg') }}"
                                    alt="{{ $directProduct->product_name }}">
                                <div>
                                    <p class="font-semibold">{{ $directProduct->product_name }}</p>
                                    <p class="text-sm text-gray-600">Qty: {{ $directPurchase['quantity'] }}</p>

                                    <!-- Display discount information -->
                                    @if ($directPurchase['applied_discount'])
                                        <p class="text-sm text-green-600">
                                            Discount: {{ $directPurchase['applied_discount'] }}
                                        </p>
                                        @if ($directPurchase['discount_amount'] > 0)
                                            <p class="text-sm text-green-600">
                                                -₱{{ number_format($directPurchase['discount_amount'], 2) }}
                                            </p>
                                        @endif
                                        @if ($directPurchase['free_items'] > 0)
                                            <p class="text-sm text-green-600">
                                                +{{ $directPurchase['free_items'] }} free item(s)
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                @if ($directPurchase['discount_amount'] > 0)
                                    <p class="text-sm text-gray-500" style="text-decoration:line-through">
                                        ₱{{ number_format($directPurchase['subtotal'], 2) }}</p>
                                @endif
                                <p class="font-bold">₱{{ number_format($directPurchase['final_subtotal'], 2) }}</p>
                                @if ($directPurchase['free_items'] > 0)
                                    <p class="text-sm font-semibold text-green-600">
                                        +{{ $directPurchase['free_items'] }} free item(s)
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 text-right">
                        <!-- Display the discounted total -->
                        <p class="text-2xl font-bold">
                            Total: ₱ {{ number_format($directPurchase['final_subtotal'], 2) }}
                        </p>
                    </div>
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
                        <p><strong>Default Address:</strong>
                            @if ($user->retailerProfile && $user->retailerProfile->defaultAddress)
                                {{ $user->retailerProfile->defaultAddress->barangay_name }},
                                {{ $user->retailerProfile->defaultAddress->street ?? '' }}
                            @elseif ($user->retailerProfile)
                                {{ $user->retailerProfile->barangay_name }},
                                {{ $user->retailerProfile->street ?? '' }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>

                    <div class="pt-4 mt-6 border-t">
                        <!-- Display the grand total -->
                        <p class="text-xl font-bold">
                            Grand Total: ₱ {{ number_format($directPurchase['final_subtotal'], 2) }}
                        </p>
                    </div>
                </div>

                <!-- Delivery Address Card -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <h2 class="mb-4 text-xl font-semibold">Delivery Address</h2>
                    <form id="orderForm" action="{{ route('retailers.direct-purchase.place-order') }}" method="POST">
                        
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $directPurchase['product_id'] }}">
                        <input type="hidden" name="distributor_id" value="{{ $directPurchase['distributor_id'] }}">
                        <input type="hidden" name="quantity" value="{{ $directPurchase['quantity'] }}">
                        <input type="hidden" name="price" value="{{ $directPurchase['price'] }}">
                        <input type="hidden" name="subtotal" value="{{ $directPurchase['subtotal'] }}">
                        <input type="hidden" name="discount_amount" value="{{ $directPurchase['discount_amount'] ?? 0 }}">
                        <input type="hidden" name="free_items" value="{{ $directPurchase['free_items'] ?? 0 }}">
                        <input type="hidden" name="applied_discount" value="{{ $directPurchase['applied_discount'] ?? '' }}">
                        <input type="hidden" name="final_subtotal" value="{{ $directPurchase['final_subtotal'] ?? $directPurchase['subtotal'] }}">
                        <input type="hidden" name="total_amount" value="{{ $directPurchase['final_subtotal'] ?? $directPurchase['subtotal'] }}">

                        <div class="flex flex-col space-y-4">
                            <!-- Standard delivery options -->
                            <div class="flex items-center">
                                <input type="radio" id="default_address" name="delivery_option" value="default"
                                    checked class="form-radio">
                                <label for="default_address" class="ml-2">
                                    Use my default address:
                                    <span class="font-medium">
                                        @if ($user->retailerProfile && $user->retailerProfile->defaultAddress)
                                            {{ $user->retailerProfile->defaultAddress->barangay_name }},
                                            {{ $user->retailerProfile->defaultAddress->street ?? '' }}
                                        @elseif ($user->retailerProfile)
                                            {{ $user->retailerProfile->barangay_name }},
                                            {{ $user->retailerProfile->street ?? '' }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </label>
                            </div>

                            <!-- Use saved addresses option -->
                            <div class="flex items-center">
                                <input type="radio" id="saved_address" name="delivery_option" value="saved"
                                    class="form-radio">
                                <label for="saved_address" class="ml-2">Use a saved address</label>
                            </div>

                            <!-- Saved addresses selector (hidden by default) -->
                            <div id="savedAddressesContainer" class="hidden mt-2 ml-6">
                                @if($user->retailerProfile && $user->retailerProfile->addresses && $user->retailerProfile->addresses->count() > 0)
                                    @foreach($user->retailerProfile->addresses as $address)
                                        <div class="flex items-center mb-2">
                                            <input type="radio" id="address_{{ $address->id }}" 
                                                name="selected_address_id" value="{{ $address->id }}"
                                                {{ $address->is_default ? 'checked' : '' }}
                                                class="form-radio">
                                            <label for="address_{{ $address->id }}" class="ml-2">
                                                {{ $address->label }}: {{ $address->barangay_name }}, {{ $address->street }}
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500">No saved addresses found. Please add an address in your profile.</p>
                                @endif
                            </div>

                            <!-- Multi-Address Option -->
                            <div class="pt-4 mt-6 border-t border-gray-200">
                                <div class="flex items-center">
                                    <input type="checkbox" id="enable_multi_address" name="is_multi_address" value="1"
                                        class="w-5 h-5 text-green-600 form-checkbox" 
                                        {{ $directPurchase['final_subtotal'] < 5000 ? 'disabled' : '' }}>
                                    <label for="enable_multi_address" class="ml-2 font-medium">
                                        Split Order for Multiple Addresses
                                    </label>
                                </div>
                                
                                @if($directPurchase['final_subtotal'] < 5000)
                                    <p class="mt-1 text-sm text-orange-600 ml-7">
                                        Your order must be at least ₱5,000 to qualify for multiple address delivery.
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-gray-600 ml-7">
                                        You can split this order and deliver to multiple addresses.
                                    </p>
                                @endif
                            </div>

                            <!-- Multi-Address Selection (initially hidden) -->
                            <div id="multiAddressSection" class="hidden pt-4 mt-4 space-y-4 border-t border-gray-200">
                                <h3 class="text-lg font-medium">Split Order Quantities</h3>
                                <p class="text-sm text-gray-600">
                                    For product: <strong>{{ $directProduct->product_name }}</strong> 
                                    (Total Quantity: {{ $directPurchase['quantity'] }})
                                </p>

                                <div id="splitQuantityContainer" class="space-y-4">
                                    <!-- First delivery address section (always shown) -->
                                    <div class="p-4 border rounded-lg bg-gray-50">
                                        <h4 class="font-medium">Delivery Location 1</h4>
                                        
                                        <div class="mt-2">
                                            <label class="block text-sm font-medium text-gray-700">Address</label>
                                            <select name="multi_address[0][address_id]" 
                                                class="block w-full px-3 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                                <option value="default">Default Address</option>
                                                @if($user->retailerProfile && $user->retailerProfile->addresses && $user->retailerProfile->addresses->count() > 0)
                                                    @foreach($user->retailerProfile->addresses as $address)
                                                        <option value="{{ $address->id }}">
                                                            {{ $address->label }}: {{ $address->barangay_name }}, {{ $address->street }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                            <input type="number" name="multi_address[0][quantity]" min="1" 
                                                max="{{ $directPurchase['quantity'] }}" 
                                                value="{{ $directPurchase['quantity'] }}"
                                                class="block w-full px-3 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                        </div>
                                    </div>
                                    
                                    <!-- Additional delivery locations will be added dynamically -->
                                </div>

                                <!-- Add more locations button -->
                                <div class="text-center">
                                    <button type="button" id="addLocationBtn"
                                        class="px-4 py-2 text-sm text-green-600 border border-green-600 rounded-lg hover:bg-green-50">
                                        + Add Another Delivery Location
                                    </button>
                                </div>
                                
                                <div class="text-sm text-red-600">
                                    <span id="quantityErrorMessage" class="hidden">
                                        Total quantity must equal {{ $directPurchase['quantity'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full px-4 py-3 mt-6 text-white transition-colors duration-200 bg-green-600 rounded-xl hover:bg-green-700">
                            Place Order
                        </button>
                    </form>
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
        // Variables for quantity validation
        const totalQuantity = {{ $directPurchase['quantity'] }};
        let locationCount = 1;
        
        // Toggle display based on radio selection
        const defaultAddressRadio = document.getElementById("default_address");
        const savedAddressRadio = document.getElementById("saved_address");

        const savedAddressesContainer = document.getElementById("savedAddressesContainer");
        
        // Multi-address related elements
        const enableMultiAddressCheckbox = document.getElementById("enable_multi_address");
        const multiAddressSection = document.getElementById("multiAddressSection");
        const addLocationBtn = document.getElementById("addLocationBtn");
        const splitQuantityContainer = document.getElementById("splitQuantityContainer");
        const quantityErrorMessage = document.getElementById("quantityErrorMessage");
        
        // Handle radio button changes
        newAddressRadio.addEventListener("change", function() {
            if (this.checked) {
                newAddressInput.classList.remove("hidden");
                savedAddressesContainer.classList.add("hidden");
            }
        });

        savedAddressRadio.addEventListener("change", function() {
            if (this.checked) {
                savedAddressesContainer.classList.remove("hidden");
            }
        });

        // Toggle multi-address section
        enableMultiAddressCheckbox.addEventListener("change", function() {
            if (this.checked) {
                multiAddressSection.classList.remove("hidden");
                // Disable other delivery options
                newAddressRadio.disabled = true;
                defaultAddressRadio.disabled = true;
                savedAddressRadio.disabled = true;
            } else {
                multiAddressSection.classList.add("hidden");
                // Enable other delivery options
                newAddressRadio.disabled = false;
                defaultAddressRadio.disabled = false;
                savedAddressRadio.disabled = false;
            }
        });
        
        // Add location button functionality
        addLocationBtn.addEventListener("click", function() {
            if (locationCount >= 3) {
                Swal.fire({
                    title: 'Limit Reached',
                    text: 'You can only deliver to a maximum of 3 locations.',
                    icon: 'warning',
                });
                return;
            }
            
            locationCount++;
            
            const newLocationDiv = document.createElement('div');
            newLocationDiv.className = 'border rounded-lg p-4 bg-gray-50';
            newLocationDiv.id = `location-${locationCount}`;
            
            newLocationDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <h4 class="font-medium">Delivery Location ${locationCount}</h4>
                    <button type="button" onclick="removeLocation(${locationCount})" 
                        class="text-red-600 hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <select name="multi_address[${locationCount-1}][address_id]" 
                        class="block w-full px-3 py-2 mt-1 border rounded-lg multi-address-select focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="default">Default Address</option>
                        @if($user->retailerProfile && $user->retailerProfile->addresses && $user->retailerProfile->addresses->count() > 0)
                            @foreach($user->retailerProfile->addresses as $address)
                                <option value="{{ $address->id }}">
                                    {{ $address->label }}: {{ $address->barangay_name }}, {{ $address->street }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="multi_address[${locationCount-1}][quantity]" min="1" 
                        max="${totalQuantity}" value="1"
                        class="block w-full px-3 py-2 mt-1 border rounded-lg multi-quantity-input focus:outline-none focus:ring-2 focus:ring-green-500"
                        onchange="validateTotalQuantity()">
                </div>
            `;
            
            splitQuantityContainer.appendChild(newLocationDiv);
            validateTotalQuantity();
        });
        
        // Remove location function
        window.removeLocation = function(id) {
            const locationDiv = document.getElementById(`location-${id}`);
            if (locationDiv) {
                locationDiv.remove();
                locationCount--;
                validateTotalQuantity();
            }
        };
        
        // Validate total quantities match the original order
        function validateTotalQuantity() {
            const quantityInputs = document.querySelectorAll('.multi-quantity-input, [name="multi_address[0][quantity]"]');
            let currentTotal = 0;
            
            quantityInputs.forEach(input => {
                currentTotal += parseInt(input.value) || 0;
            });
            
            if (currentTotal !== totalQuantity) {
                quantityErrorMessage.classList.remove('hidden');
                return false;
            } else {
                quantityErrorMessage.classList.add('hidden');
                return true;
            }
        }
        
        // Add validation to the first quantity input
        document.querySelector('[name="multi_address[0][quantity]"]').addEventListener('change', validateTotalQuantity);
        

        // Form submission validation
        const orderForm = document.getElementById("orderForm");
        orderForm.addEventListener("submit", function(e) {
            e.preventDefault();

            // Validate multi-address quantities if enabled
            if (enableMultiAddressCheckbox.checked) {
                if (!validateTotalQuantity()) {
                    Swal.fire({
                        title: 'Invalid Quantities',
                        text: `Total quantity across all locations must equal ${totalQuantity}`,
                        icon: 'error',
                    });
                    return;
                }
            }
            // Check if new address is selected but no barangay is chosen
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