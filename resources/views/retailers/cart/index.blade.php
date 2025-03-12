<x-app-layout>
    <x-dashboard-nav />

    <div class="container px-4 py-6 mx-auto" id="cartContainer">
        <h2 class="mb-6 text-3xl font-bold text-gray-800">Shopping Cart</h2>

        @forelse($groupedItems as $distributorId => $items)
            <div class="p-6 mb-6 bg-white rounded-lg shadow-md cart-group" data-distributor-id="{{ $distributorId }}"
                data-cart-id="{{ $items->first()->cart->id }}">
                <!-- Distributor Header with Clear Cart button -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-700">
                        {{ $items->first()->product->distributor->company_name }}
                    </h3>
                    <button onclick="deleteCart('{{ $items->first()->cart->id }}')"
                        class="text-red-500 hover:text-red-700" title="Clear Cart">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Products Table -->
                <table class="w-full">
                    <thead class="border-b border-gray-200">
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
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <img src="{{ $item->product->image ? asset('storage/products/' . basename($item->product->image)) : asset('img/default-product.jpg') }}"
                                            class="object-cover w-16 h-16 mr-4 rounded"
                                            onerror="this.src='{{ asset('img/default-product.jpg') }}'"
                                            alt="{{ $item->product->product_name }}">
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $item->product->product_name }}
                                            </h4>
                                            <p class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center" data-price="{{ $item->product->price }}">
                                    ₱{{ number_format($item->product->price, 2) }}
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
                                <td class="px-4 py-4 text-center item-subtotal">
                                    ₱{{ number_format($item->product->price * $item->quantity, 2) }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button onclick="removeItem('{{ $item->id }}')"
                                        class="p-2 text-red-500 rounded-full hover:bg-red-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                            <td colspan="3" class="px-4 py-4 font-semibold text-right">
                                Total Amount:
                            </td>
                            <td class="px-4 py-4 font-semibold text-center distributor-subtotal">
                                ₱{{ number_format(
                                    $items->sum(function ($item) {
                                        return $item->product->price * $item->quantity;
                                    }),
                                    2,
                                ) }}
                            </td>
                            <td class="px-4 py-4">
                                <button onclick="proceedToCheckout('{{ $distributorId }}')"
                                    class="flex items-center justify-center w-full gap-2 px-4 py-2 text-sm text-white bg-green-600 rounded hover:bg-green-700 lg:w-auto">
                                    <span>Checkout</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @empty
            <div id="emptyCartPlaceholder"
                class="flex flex-col items-center justify-center p-12 bg-white rounded-lg shadow-md">
                <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <p class="mb-4 text-lg text-gray-600">Your cart is empty</p>
                <a href="{{ route('retailers.all-product') }}"
                    class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                    Continue Shopping
                </a>
            </div>
        @endforelse

        @if (count($groupedItems) > 0)
            <div class="flex justify-end mt-6">
                <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xl font-semibold text-gray-800">Total All Carts:</span>
                        <span class="text-xl font-bold text-gray-900" id="globalCartTotal">
                            ₱{{ number_format(
                                $groupedItems->flatMap(function ($items) {
                                        return $items->map(function ($item) {
                                            return $item->product->price * $item->quantity;
                                        });
                                    })->sum(),
                                2,
                            ) }}
                        </span>
                    </div>
                    @if (count($groupedItems) > 1)
                        <button onclick="proceedToCheckoutAll()"
                            class="flex items-center justify-center w-full gap-2 px-6 py-3 text-white bg-green-600 rounded-lg hover:bg-green-700">
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
    </div>
    <script>
        let cartChanges = {};

        function updateCartEmptyState() {
            const cartContainer = document.getElementById('cartContainer');
            if (!document.querySelector('.cart-group')) {
                cartContainer.innerHTML = `
                    <h2 class="mb-6 text-3xl font-bold text-gray-800">Shopping Cart</h2>
                    <div class="flex flex-col items-center justify-center p-12 bg-white rounded-lg shadow-md">
                        <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <p class="mb-4 text-lg text-gray-600">Your cart is empty</p>
                        <a href="{{ route('retailers.dashboard') }}"
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
                    Swal.fire({
                        title: 'Deleting cart...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/retailers/cart/delete/${cartId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                const cartGroup = document.querySelector(`[data-cart-id="${cartId}"]`);
                                if (cartGroup) {
                                    cartGroup.remove();
                                }
                                updateCartTotal();
                                updateCartEmptyState();
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Your cart has been cleared.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                throw new Error(data.message || 'Failed to delete cart');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: error.message || 'Failed to delete cart',
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                        });
                }
            });
        }

        function removeItem(itemId) {
            Swal.fire({
                title: 'Removing item...',
                text: 'Please wait',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/retailers/cart/remove/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                        if (itemElement) {
                            const cartGroup = itemElement.closest('.cart-group');
                            itemElement.remove();
                            // If no items remain in the group, remove the entire group
                            if (!cartGroup.querySelector('[data-item-id]')) {
                                cartGroup.remove();
                            } else {
                                updateDistributorSubtotal(cartGroup.querySelector('tr'));
                            }
                            updateCartTotal();
                            updateCartEmptyState();
                        }
                        Swal.fire({
                            title: 'Success!',
                            text: 'Item removed from cart',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.message || 'Failed to remove item');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Failed to remove item',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                });
        }

        function updateQuantity(button, action) {
            const row = button.closest('tr');
            const input = row.querySelector('input[type="number"]');
            const currentQty = parseInt(input.value);
            const minQty = parseInt(input.dataset.minimum);
            const itemId = input.dataset.itemId;
            let newQty = action === 'increase' ? currentQty + 1 : currentQty - 1;

            if (newQty < minQty) {
                askToRemoveItem(itemId, minQty);
                return;
            }

            input.value = newQty;
            updateItemSubtotal(row, newQty);
            updateDistributorSubtotal(row);
            updateCartTotal();
        }

        function updateItemSubtotal(row, quantity) {
            const price = parseFloat(row.querySelector('[data-price]').dataset.price);
            const subtotal = price * quantity;
            row.querySelector('.item-subtotal').textContent =
                `₱${subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        function updateDistributorSubtotal(row) {
            const cartGroup = row.closest('.cart-group');
            const items = cartGroup.querySelectorAll('.item-subtotal');
            const subtotal = Array.from(items).reduce((sum, item) => {
                let sub = parseFloat(item.textContent.replace('₱', '').replace(/,/g, ''));
                return sum + sub;
            }, 0);
            cartGroup.querySelector('.distributor-subtotal').textContent =
                `₱${subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        function updateCartTotal() {
            const subtotals = document.querySelectorAll('.distributor-subtotal');
            const total = Array.from(subtotals).reduce((sum, item) => {
                let sub = parseFloat(item.textContent.replace('₱', '').replace(/,/g, ''));
                return sum + sub;
            }, 0);
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
                    const input = document.querySelector(`input[data-item-id="${itemId}"]`);
                    if (input) {
                        input.value = minQty;
                        const row = input.closest('tr');
                        updateItemSubtotal(row, minQty);
                        updateDistributorSubtotal(row);
                        updateCartTotal();
                    }
                }
            });
        }

        async function updateCartQuantities() {
            const items = [];
            document.querySelectorAll('input[type="number"][data-item-id]').forEach(input => {
                items.push({
                    cart_detail_id: input.dataset.itemId,
                    quantity: parseInt(input.value)
                });
            });

            try {
                const response = await fetch('/retailers/cart/update-quantities', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        items
                    })
                });
                return response.ok;
            } catch (error) {
                return false;
            }
        }

        async function proceedToCheckout(distributorId) {
            await updateCartQuantities();
            Swal.fire({
                title: 'Processing...',
                text: 'Preparing your checkout...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Use the named route for checkout
            setTimeout(() => {
                window.location.href = `/retailers/checkout/${distributorId}`;
            }, 1500);
        }

        async function proceedToCheckoutAll() {
            await updateCartQuantities();
            Swal.fire({
                title: 'Processing...',
                text: 'Preparing your checkout...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Use the named route for checkout all
            setTimeout(() => {
                window.location.href = '/retailers/checkout-all';
            }, 1500);
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
                    input.value = minQty;
                    newQty = minQty;
                    cartChanges[itemId] = newQty;
                    const row = input.closest('tr');
                    updateItemSubtotal(row, newQty);
                    updateDistributorSubtotal(row);
                    updateCartTotal();
                });
            } else {
                cartChanges[itemId] = newQty;
                const row = input.closest('tr');
                updateItemSubtotal(row, newQty);
                updateDistributorSubtotal(row);
                updateCartTotal();
            }
        }
    </script>
</x-app-layout>
<x-footer />