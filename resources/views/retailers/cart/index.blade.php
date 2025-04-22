<x-app-layout>
    <x-dashboard-nav />

    <div class="container px-4 py-6 mx-auto" id="cartContainer">
        <div class="px-4 py-6 mx-auto center">
            <h2 class="mb-6 text-3xl font-bold text-gray-800">Shopping Cart</h2>
            <a href="{{ route('retailers.profile.my-purchase') }}"
                class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">My Purchases</a>
        </div>

        @forelse($groupedItems as $distributorId => $items)
            <div class="p-4 mb-6 bg-white rounded-lg shadow-md sm:p-6 cart-group"
                data-distributor-id="{{ $distributorId }}" data-cart-id="{{ $items->first()->cart->id }}">
                <!-- Distributor Header with Clear Cart button -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 sm:text-xl">
                        {{ $items->first()->product->distributor->company_name }}
                    </h3>
                    <button onclick="deleteCart('{{ $items->first()->cart->id }}')"
                        class="text-red-500 hover:text-red-700" title="Clear Cart">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Products Table - Hidden on mobile -->
                <div class="hidden md:block">
                    <div class="max-h-[400px] overflow-y-auto">
                        <table class="w-full">
                            <thead class="sticky top-0 z-10 bg-white border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left">Product</th>
                                    <th class="px-4 py-2 text-center">Price</th>
                                    <th class="px-4 py-2 text-center">Quantity</th>
                                    <th class="px-4 py-2 text-center">Subtotal</th>
                                    <th class="px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr class="border-b border-gray-100" data-item-id="{{ $item->id }}"
                                        data-distributor-id="{{ $distributorId }}">
                                        <td class="px-4 py-2">
                                            <div class="flex items-center">
                                                <img src="{{ $item->product->image ? asset('storage/products/' . basename($item->product->image)) : asset('img/default-product.jpg') }}"
                                                    class="object-cover w-16 h-16 mr-4 rounded"
                                                    onerror="this.src='{{ asset('img/default-product.jpg') }}'"
                                                    alt="{{ $item->product->product_name }}">
                                                <div>
                                                    <h4 class="font-medium text-gray-800">
                                                        {{ $item->product->product_name }}
                                                    </h4>
                                                    <p class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            @if($item->discount_amount > 0)
                                                <div class="flex flex-col">
                                                    <span class="text-gray-500 line-through">₱{{ number_format($item->product->price, 2) }}</span>
                                                    <span class="text-green-600" 
                                                          data-price="{{ $item->product->price }}" 
                                                          data-discount="{{ $item->discount_amount / $item->quantity }}">
                                                        ₱{{ number_format(($item->product->price * $item->quantity - $item->discount_amount) / $item->quantity, 2) }}
                                                    </span>
                                                    <span class="text-xs text-green-500">
                                                        {{ $item->applied_discount ?? 'Discount Applied' }}
                                                    </span>
                                                </div>
                                            @else
                                                <span data-price="{{ $item->product->price }}" data-discount="0">
                                                    ₱{{ number_format($item->product->price, 2) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center justify-center">
                                                <button type="button" onclick="updateQuantity(this, 'decrease')"
                                                    class="px-2 py-1 text-gray-600 bg-gray-100 rounded-l hover:bg-green-100">
                                                    -
                                                </button>
                                                <input type="number" value="{{ $item->quantity }}"
                                                    min="{{ $item->product->minimum_purchase_qty }}"
                                                    class="w-16 px-2 py-1 text-center border-gray-200"
                                                    data-item-id="{{ $item->id }}"
                                                    data-minimum="{{ $item->product->minimum_purchase_qty }}"
                                                    onchange="validateQuantity(this)">
                                                <button type="button" onclick="updateQuantity(this, 'increase')"
                                                    class="px-2 py-1 text-gray-600 bg-gray-100 rounded-r hover:bg-green-100">
                                                    +
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-center item-subtotal">
                                            ₱{{ number_format($item->subtotal, 2) }}
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button onclick="removeItem('{{ $item->id }}')"
                                                class="p-2 text-red-500 rounded-full hover:bg-red-50">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 font-semibold text-right">
                                        Total Amount:
                                    </td>
                                    <td class="px-4 py-4 font-semibold text-center distributor-subtotal">
                                        ₱{{ number_format($items->sum('subtotal'), 2) }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <button onclick="proceedToCheckout('{{ $distributorId }}')"
                                            class="flex items-center justify-center w-full gap-2 px-4 py-2 text-sm text-white bg-green-600 rounded hover:bg-green-700 lg:w-auto">
                                            <span>Checkout</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View - Visible only on mobile -->
                <div class="space-y-4 md:hidden">
                    @foreach ($items as $item)
                        <div class="p-3 border border-gray-200 rounded-lg cart-item-mobile"
                            data-item-id="{{ $item->id }}" data-distributor-id="{{ $distributorId }}">
                            <div class="flex items-center gap-3">
                                <!-- Product image -->
                                <img src="{{ $item->product->image ? asset('storage/products/' . basename($item->product->image)) : asset('img/default-product.jpg') }}"
                                    class="object-cover w-20 h-20 rounded"
                                    onerror="this.src='{{ asset('img/default-product.jpg') }}'"
                                    alt="{{ $item->product->product_name }}">

                                <!-- Product details -->
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">{{ $item->product->product_name }}</h4>
                                    <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                                    
                                    @if($item->discount_amount > 0)
                                        <p class="mt-1 text-sm text-gray-500 line-through">
                                            ₱{{ number_format($item->product->price, 2) }}
                                        </p>
                                        <p class="text-sm font-semibold text-green-600"
                                           data-price="{{ $item->product->price }}" 
                                           data-discount="{{ $item->discount_amount / $item->quantity }}">
                                            ₱{{ number_format(($item->product->price * $item->quantity - $item->discount_amount) / $item->quantity, 2) }}
                                            <span class="text-xs">{{ $item->applied_discount ?? 'Discount Applied' }}</span>
                                        </p>
                                    @else
                                        <p class="mt-1 text-sm font-semibold text-gray-800"
                                           data-price="{{ $item->product->price }}" data-discount="0">
                                            ₱{{ number_format($item->product->price, 2) }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Remove button -->
                                <button onclick="removeItem('{{ $item->id }}')"
                                    class="self-start p-2 text-red-500 rounded-full hover:bg-red-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Quantity controls -->
                            <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-100">
                                <div class="flex items-center gap-1">
                                    <span class="text-sm text-gray-600">Quantity:</span>
                                    <div class="flex items-center ml-2">
                                        <button type="button" onclick="updateQuantity(this, 'decrease')"
                                            class="flex items-center justify-center w-8 h-8 text-gray-600 bg-gray-100 rounded-l hover:bg-green-100">
                                            -
                                        </button>
                                        <input type="number" value="{{ $item->quantity }}"
                                            min="{{ $item->product->minimum_purchase_qty }}"
                                            class="w-12 h-8 px-0 text-center border-t border-b border-gray-200"
                                            data-item-id="{{ $item->id }}"
                                            data-minimum="{{ $item->product->minimum_purchase_qty }}"
                                            onchange="validateQuantity(this)">
                                        <button type="button" onclick="updateQuantity(this, 'increase')"
                                            class="flex items-center justify-center w-8 h-8 text-gray-600 bg-gray-100 rounded-r hover:bg-green-100">
                                            +
                                        </button>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">Subtotal</div>
                                    <div class="font-semibold text-gray-800 item-subtotal">
                                        ₱{{ number_format($item->subtotal, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Mobile Cart Total -->
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-semibold text-gray-700">Total Amount:</span>
                            <span class="font-bold text-gray-900 distributor-subtotal">
                                ₱{{ number_format($items->sum('subtotal'), 2) }}
                            </span>
                        </div>
                        <button onclick="proceedToCheckout('{{ $distributorId }}')"
                            class="flex items-center justify-center w-full gap-2 px-4 py-3 text-white bg-green-600 rounded-lg hover:bg-green-700">
                            <span>Checkout</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div id="emptyCartPlaceholder"
                class="flex flex-col items-center justify-center p-6 bg-white rounded-lg shadow-md sm:p-12">
                <svg class="w-12 h-12 mb-4 text-gray-400 sm:w-16 sm:h-16" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <p class="mb-4 text-base text-gray-600 sm:text-lg">Your cart is empty</p>
                <a href="{{ route('retailers.all-product') }}"
                    class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                    Continue Shopping
                </a>
            </div>
        @endforelse

        @if (count($groupedItems) > 0)
            <div class="flex justify-end mt-6">
                <div class="w-full max-w-md p-4 bg-white rounded-lg shadow-md sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-lg font-semibold text-gray-800 sm:text-xl">Total All Carts:</span>
                        <span class="text-lg font-bold text-gray-900 sm:text-xl" id="globalCartTotal">
                            ₱{{ number_format(
                                $groupedItems->flatMap(function ($items) {
                                    return $items->map(function ($item) {
                                        return $item->subtotal;
                                    });
                                })->sum(),
                                2,
                            ) }}
                        </span>
                    </div>
                    @if (count($groupedItems) > 1)
                        <button onclick="proceedToCheckoutAll()"
                            class="flex items-center justify-center w-full gap-2 px-4 py-3 text-white bg-green-600 rounded-lg hover:bg-green-700">
                            <span>Checkout All Items</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script>
        let cartChanges = {};

        function updateCartEmptyState() {
            const cartContainer = document.getElementById('cartContainer');
            if (!document.querySelector('.cart-group')) {
                cartContainer.innerHTML = `
                    <div class="px-4 py-6 mx-auto center">
                        <h2 class="mb-6 text-3xl font-bold text-gray-800">Shopping Cart</h2>
                        <a href="{{ route('retailers.profile.my-purchase') }}"
                            class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">My Purchases</a>
                    </div>
                    <div class="flex flex-col items-center justify-center p-6 bg-white rounded-lg shadow-md sm:p-12">
                        <svg class="w-12 h-12 mb-4 text-gray-400 sm:w-16 sm:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <p class="mb-4 text-base text-gray-600 sm:text-lg">Your cart is empty</p>
                        <a href="{{ route('retailers.all-product') }}"
                            class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                            Continue Shopping
                        </a>
                    </div>
                `;
            }
        }

        function deleteCart(cartId) {
            Swal.fire({
                title: 'Delete Cart?',
                text: 'Are you sure you want to clear your entire cart for this distributor?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    showProcessingAlert('Deleting cart...');

                    fetch(`/retailers/cart/delete/${cartId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    })
                    .then(handleApiResponse)
                    .then(data => {
                        if (data.success) {
                            const cartGroup = document.querySelector(`[data-cart-id="${cartId}"]`);
                            if (cartGroup) cartGroup.remove();
                            updateCartTotal();
                            updateCartEmptyState();
                            showSuccessAlert('Deleted!', 'Your cart has been cleared.');
                        } else {
                            throw new Error(data.message || 'Failed to delete cart');
                        }
                    })
                    .catch(error => showErrorAlert('Error!', error.message || 'Failed to delete cart'));
                }
            });
        }

        function removeItem(itemId) {
            showProcessingAlert('Removing item...');

            fetch(`/retailers/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(handleApiResponse)
            .then(data => {
                if (data.success) {
                    const itemElements = document.querySelectorAll(`[data-item-id="${itemId}"]`);
                    itemElements.forEach(itemElement => {
                        const cartGroup = itemElement.closest('.cart-group');
                        itemElement.remove();
                        
                        // If no items remain in the group, remove the entire group
                        if (!cartGroup.querySelector('[data-item-id]')) {
                            cartGroup.remove();
                        } else {
                            updateDistributorSubtotal(cartGroup.dataset.distributorId);
                        }
                    });
                    
                    updateCartTotal();
                    updateCartEmptyState();
                    showSuccessAlert('Success!', 'Item removed from cart');
                } else {
                    throw new Error(data.message || 'Failed to remove item');
                }
            })
            .catch(error => showErrorAlert('Error!', error.message || 'Failed to remove item'));
        }

        function updateQuantity(button, action) {
            // Find closest parent that has data-item-id (could be tr or mobile div)
            const container = button.closest('[data-item-id]');
            const input = container.querySelector('input[type="number"]');
            const currentQty = parseInt(input.value);
            const minQty = parseInt(input.dataset.minimum);
            const itemId = container.dataset.itemId;
            let newQty = action === 'increase' ? currentQty + 1 : currentQty - 1;

            if (newQty < minQty) {
                askToRemoveItem(itemId, minQty);
                return;
            }

            input.value = newQty;
            cartChanges[itemId] = newQty;

            // Update all instances of this item (both mobile and desktop views)
            updateAllItemInstances(itemId, newQty);

            // Update the global cart total
            updateCartTotal();
        }

        function updateAllItemInstances(itemId, newQty) {
            // Find all DOM elements for this cart item (mobile and desktop views)
            const allItemContainers = document.querySelectorAll(`[data-item-id="${itemId}"]`);
            let distributorId = null;
            
            allItemContainers.forEach(container => {
                // Update quantity input value
                const itemInput = container.querySelector('input[type="number"]');
                if (itemInput) itemInput.value = newQty;
                
                // Calculate and update subtotal based on price and discount
                updateItemSubtotal(container, newQty);
                
                if (!distributorId) distributorId = container.dataset.distributorId;
            });
            
            // Update distributor subtotal after updating all items
            if (distributorId) updateDistributorSubtotal(distributorId);
        }

        function updateItemSubtotal(container, quantity) {
            const priceElement = container.querySelector('[data-price]');
            if (!priceElement) return;

            const price = parseFloat(priceElement.dataset.price);
            const discountPerUnit = parseFloat(priceElement.dataset.discount) || 0;
            
            // Calculate subtotal with discount applied
            const subtotal = (price - discountPerUnit) * quantity;

            // Update all subtotal elements within this container
            const subtotalElements = container.querySelectorAll('.item-subtotal');
            subtotalElements.forEach(element => {
                element.textContent = `₱${subtotal.toLocaleString('en-US', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                })}`;
            });
        }

        function updateDistributorSubtotal(distributorId) {
            // Get the cart group for this distributor
            const cartGroup = document.querySelector(`.cart-group[data-distributor-id="${distributorId}"]`);
            if (!cartGroup) return;

            // Calculate the total by summing up all item subtotals in this cart group
            let subtotal = 0;
            const items = cartGroup.querySelectorAll('[data-item-id]');

            // Process each item once (avoid double-counting mobile and desktop views)
            const processedItems = new Set();

            items.forEach(item => {
                const itemId = item.dataset.itemId;

                // Skip if we've already processed this item
                if (processedItems.has(itemId)) return;
                processedItems.add(itemId);

                // Get subtotal from the item-subtotal element
                const subtotalElement = item.querySelector('.item-subtotal');
                if (subtotalElement) {
                    const itemSubtotal = parseFloat(subtotalElement.textContent.replace('₱', '').replace(/,/g, ''));
                    if (!isNaN(itemSubtotal)) subtotal += itemSubtotal;
                }
            });

            // Update all distributor subtotal elements in this cart group
            const subtotalElements = cartGroup.querySelectorAll('.distributor-subtotal');
            subtotalElements.forEach(element => {
                element.textContent = `₱${subtotal.toLocaleString('en-US', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                })}`;
            });
        }

        function updateCartTotal() {
            // Calculate the total from all distributor subtotals
            const subtotalElements = document.querySelectorAll('.distributor-subtotal');
            const processedDistributors = new Set();
            let total = 0;

            subtotalElements.forEach(element => {
                // Get the distributor ID from the closest cart-group
                const cartGroup = element.closest('.cart-group');
                if (!cartGroup) return;

                const distributorId = cartGroup.dataset.distributorId;

                // Skip if we've already processed this distributor
                if (processedDistributors.has(distributorId)) return;
                processedDistributors.add(distributorId);

                // Add this distributor's subtotal to the total
                const subtotalText = element.textContent.replace('₱', '').replace(/,/g, '');
                const subtotal = parseFloat(subtotalText);
                if (!isNaN(subtotal)) total += subtotal;
            });

            // Update the global cart total
            const globalTotalElement = document.getElementById('globalCartTotal');
            if (globalTotalElement) {
                globalTotalElement.textContent = `₱${total.toLocaleString('en-US', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                })}`;
            }
        }

        function askToRemoveItem(itemId, minQty) {
            Swal.fire({
                title: 'Remove Item?',
                text: `Minimum purchase quantity is ${minQty}. Would you like to remove this item from your cart?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    removeItem(itemId);
                } else {
                    resetItemToMinimum(itemId, minQty);
                }
            });
        }

        function resetItemToMinimum(itemId, minQty) {
            updateAllItemInstances(itemId, minQty);
            updateCartTotal();
        }

        async function updateCartQuantities() {
            const items = [];
            const processedItemIds = new Set();
            
            document.querySelectorAll('input[type="number"][data-item-id]').forEach(input => {
                const itemId = input.dataset.itemId;
                if (!processedItemIds.has(itemId)) {
                    processedItemIds.add(itemId);
                    items.push({
                        cart_detail_id: itemId,
                        quantity: parseInt(input.value)
                    });
                }
            });

            try {
                const response = await fetch('/retailers/cart/update-quantities', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ items })
                });
                
                const result = await response.json();
                if (result.success) {
                    return true;
                } else {
                    console.error('Error updating quantities:', result.message);
                    return false;
                }
            } catch (error) {
                console.error('Error updating quantities:', error);
                return false;
            }
        }

        async function proceedToCheckout(distributorId) {
            showProcessingAlert('Preparing your checkout...');
            
            // First save any quantity changes
            const updateSuccess = await updateCartQuantities();
            if (!updateSuccess) {
                showErrorAlert('Error', 'Could not update quantities. Please try again.');
                return;
            }
            
            // Redirect to checkout page
            setTimeout(() => {
                window.location.href = `/retailers/checkout/${distributorId}`;
            }, 1000);
        }

        async function proceedToCheckoutAll() {
            showProcessingAlert('Preparing your checkout...');
            
            // First save any quantity changes
            const updateSuccess = await updateCartQuantities();
            if (!updateSuccess) {
                showErrorAlert('Error', 'Could not update quantities. Please try again.');
                return;
            }
            
            // Redirect to checkout all page
            setTimeout(() => {
                window.location.href = '/retailers/checkout-all';
            }, 1000);
        }

        function validateQuantity(input) {
            const itemId = input.dataset.itemId;
            const minQty = parseInt(input.dataset.minimum);
            let newQty = parseInt(input.value) || minQty;

            if (newQty < minQty) {
                Swal.fire({
                    title: 'Warning!',
                    text: `Minimum purchase quantity is ${minQty}`,
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    resetItemToMinimum(itemId, minQty);
                });
            } else {
                updateAllItemInstances(itemId, newQty);
                updateCartTotal();
            }
        }
        
        // Helper functions for alerts
        function showProcessingAlert(text) {
            Swal.fire({
                title: 'Processing...',
                text: text || 'Please wait',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        function showSuccessAlert(title, text) {
            Swal.fire({
                title: title || 'Success',
                text: text || 'Operation completed successfully',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
        
        function showErrorAlert(title, text) {
            Swal.fire({
                title: title || 'Error',
                text: text || 'Something went wrong',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        }
        
        function handleApiResponse(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        }
    </script>
</x-app-layout>
<x-footer />