var storageBaseUrl = "{{ asset('storage') }}";
var currentOrderId = null;

function openModal(row) {
    var orderId = row.getAttribute('data-order-id');
    var formattedOrderId = row.querySelector('td:first-child').textContent.trim();
    var orderStatus = row.getAttribute('data-status');
    currentOrderId = orderId;
    var retailer = JSON.parse(row.getAttribute('data-retailer'));
    var details = JSON.parse(row.getAttribute('data-details'));
    var dateTime = row.getAttribute('data-created-at');
    var deliveryAddress = row.getAttribute('data-delivery-address');

    document.getElementById('modalTitle').innerText = 'Order ' + formattedOrderId;

    // First, check if this is a multi-address order by fetching additional details
    fetch(`/orders/${orderId}/detail`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let modalHtml = `
        <div class="space-y-6">
            <!-- Products Section -->
            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="p-4 border-b bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Products Ordered</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Product</th>
                                <th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Price</th>
                                <th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Quantity</th>
                                <th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
    `;

                let totalAmount = 0;
                details.forEach(function (detail) {
                    const originalPrice = parseFloat(detail.product.price).toFixed(2);
                    const discountedPrice = detail.discount_amount > 0 ?
                        (detail.product.price - (detail.discount_amount / detail.quantity)).toFixed(2) :
                        originalPrice;
                    modalHtml += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <img src="${detail.product.image ? storageBaseUrl + '/' + detail.product.image : 'img/default-product.jpg'}" 
                            alt="${detail.product.product_name}" 
                            class="object-cover w-16 h-16 rounded-lg" />
                        <span class="font-medium text-gray-800">${detail.product.product_name}</span>
                    </div>
                </td>
                <td class="px-4 py-3">
                    ${detail.discount_amount > 0
                            ? `<span class="text-xs text-gray-500 line-through">₱${originalPrice}</span><br>
                                <span class="text-green-600">₱${discountedPrice}</span>`
                            : `₱${originalPrice}`}
                </td>
                <td class="px-4 py-3">${detail.quantity}</td>
                <td class="px-4 py-3 font-medium text-blue-600">₱${parseFloat(detail.subtotal).toFixed(2)}</td>
            </tr>
        `;
                    totalAmount += parseFloat(detail.subtotal);
                });

                modalHtml += `
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 font-medium text-right text-gray-700">Total Amount:</td>
                                <td colspan="3" class="px-4 py-3 font-bold text-blue-600">₱${totalAmount.toFixed(2)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>`;

                // Check if this is a multi-address order and add the Multi-Delivery section if it is
                const isMultiAddress = data.orderDetails.some(detail => detail.is_multi_address);

                if (isMultiAddress) {
                    // Fetch order item deliveries
                    fetch(`/orders/${orderId}/deliveries`)
                        .then(res => res.json())
                        .then(deliveryData => {
                            if (deliveryData.success && deliveryData.deliveries) {
                                modalHtml += `
                            <!-- Multi-Address Delivery Section -->
                            <div class="overflow-hidden bg-white rounded-lg shadow">
                                <div class="flex items-center p-4 border-b bg-purple-50">
                                    <svg class="w-5 h-5 mr-2 text-purple-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-purple-800">Multiple Delivery Addresses</h3>
                                </div>
                                <div class="p-4 space-y-4">`;

                                deliveryData.deliveries.forEach((delivery, index) => {
                                    modalHtml += `
                                    <div class="p-4 border ${index % 2 === 0 ? 'bg-gray-50' : 'bg-white'} rounded-lg">
                                        <h4 class="mb-2 font-medium text-gray-900">Delivery Location ${index + 1}</h4>
                                        
                                        <!-- Delivery address -->
                                        <div class="flex items-start mb-3">
                                            <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <p class="ml-3 text-sm text-gray-600">
                                                ${delivery.address ? delivery.address.barangay_name +
                                            (delivery.address.street ? ', ' + delivery.address.street : '') : 'Address not available'}
                                            </p>
                                        </div>
                                        
                                        <!-- Products for this delivery -->
                                        <div class="mt-2">
                                            <p class="text-sm font-medium text-gray-700">Products for this location:</p>
                                            <ul class="pl-5 mt-1 space-y-1 text-sm list-disc">`;

                                    if (delivery.items && delivery.items.length > 0) {
                                        delivery.items.forEach(item => {
                                            modalHtml += `
                                                <li class="text-gray-600">
                                                    ${item.product_name || 'Product'} <span class="text-gray-500">(Qty: ${item.quantity})</span>
                                                </li>`;
                                        });
                                    } else {
                                        modalHtml +=
                                            `<li class="text-gray-500">No items found for this delivery</li>`;
                                    }

                                    modalHtml += `
                                            </ul>
                                        </div>
                                    </div>`;
                                });

                                modalHtml += `
                                </div>
                            </div>`;
                            }

                            // Add retailer profile section
                            modalHtml += getRetailerProfileHtml(retailer, deliveryAddress);

                            document.getElementById('modalContent').innerHTML = modalHtml;
                        })
                        .catch(err => {
                            console.error('Error fetching delivery information:', err);

                            // Still display the retailer profile even if delivery info fails
                            modalHtml += getRetailerProfileHtml(retailer, deliveryAddress);
                            document.getElementById('modalContent').innerHTML = modalHtml;
                        });
                } else {
                    // Regular single-address order
                    modalHtml += getRetailerProfileHtml(retailer, deliveryAddress);
                    document.getElementById('modalContent').innerHTML = modalHtml;
                }

                document.getElementById('orderModal').classList.remove('hidden');

                // Show or hide action buttons based on order status
                const actionButtons = document.getElementById('actionButtons');
                if (orderStatus === 'pending') {
                    actionButtons.classList.remove('hidden');
                } else {
                    actionButtons.classList.add('hidden');
                }

                // Show QR code button only for processing orders
                const qrCodeButton = document.getElementById('qrCodeButton');
                if (orderStatus === 'processing') {
                    qrCodeButton.href = `/orders/${orderId}/qrcode`;
                    qrCodeButton.classList.remove('hidden');
                } else {
                    qrCodeButton.classList.add('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error loading order details:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to load order details.',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        });
}

// Helper function to generate retailer profile HTML
function getRetailerProfileHtml(retailer, deliveryAddress) {
    return `
    <!-- Retailer Profile Section -->
    <div class="p-4 bg-white rounded-lg shadow">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <a href="/retailers/${retailer.id}">
                    <img src="${retailer.retailer_profile?.profile_picture ? storageBaseUrl + '/' + retailer.retailer_profile.profile_picture : 'img/default-avatar.jpg'}" 
                        alt="Profile" 
                        class="object-cover w-16 h-16 rounded-full shadow hover:opacity-80" />
                </a>
            </div>
            <div>
                <a href="/retailers/${retailer.id}" class="hover:underline">
                    <h4 class="text-lg font-medium text-gray-800">${retailer.first_name} ${retailer.last_name}</h4>
                </a>
                <p class="text-sm text-gray-600">${retailer.email}</p>
                <p class="text-sm text-gray-600">${retailer.retailer_profile?.phone || 'No phone number'}</p>
                <p class="text-sm text-gray-600">${deliveryAddress || 'No delivery address'}</p>
            </div>
        </div>
    </div>
</div>`;

    document.getElementById('modalContent').innerHTML = modalHtml;
    document.getElementById('orderModal').classList.remove('hidden');

    // Show or hide action buttons based on order status
    const actionButtons = document.getElementById('actionButtons');
    if (orderStatus === 'pending') {
        actionButtons.classList.remove('hidden');
    } else {
        actionButtons.classList.add('hidden');
    }

    // Show QR code button only for processing orders
    const qrCodeButton = document.getElementById('qrCodeButton');
    if (orderStatus === 'processing') {
        qrCodeButton.href = `/orders/${orderId}/qrcode`;
        qrCodeButton.classList.remove('hidden');
    } else {
        qrCodeButton.classList.add('hidden');
    }
}

function closeModal() {
    document.getElementById('orderModal').classList.add('hidden');
}

function acceptOrder() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to accept this order?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, accept it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/orders/${currentOrderId}/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Order accepted successfully.',
                            icon: 'success',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Show validation error in alert
                        Swal.fire({
                            title: 'Error!',
                            text: data.message ||
                                'An error occurred while accepting the order.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An unexpected error occurred.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                });
        }
    });
}

function openRejectModal() {
    document.getElementById('orderModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('orderModal').classList.remove('hidden');
    document.body.style.overflow = ''; // Restore background scrolling
}

function checkRejectOther(radio) {
    var otherReasonInput = document.getElementById('rejectOtherReason');
    if (radio.value === 'Other') {
        otherReasonInput.classList.remove('hidden');
    } else {
        otherReasonInput.classList.add('hidden');
        otherReasonInput.value = '';
    }
}

function openEditOrderModal() {
    const modal = document.getElementById('editOrderModal');
    const orderItemsContainer = document.getElementById('editOrderItems');
    orderItemsContainer.innerHTML = '';

    // Fetch order details and stock availability
    fetch(`/orders/${currentOrderId}/detail`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.orderDetails.forEach(detail => {
                    const stockLeft = detail.product.stockLeft;

                    orderItemsContainer.innerHTML += `
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">${detail.product.product_name}</p>
                        <p class="text-sm text-gray-600">Stock Left: ${stockLeft}</p>
                    </div>
                    <div>
                        <input type="number" name="order_details[${detail.id}][quantity]"
                            value="${detail.quantity}" min="1" max="${stockLeft}"
                            class="w-20 px-2 py-1 border rounded">
                    </div>
                </div>
            `;
                });

                modal.classList.remove('hidden');
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to load order details.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.message || 'An unexpected error occurred.',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        });
}

function submitEditOrder() {
    const form = document.getElementById('editOrderForm');
    const formData = new FormData(form);

    // Convert FormData to JSON object
    const orderDetails = [];
    formData.forEach((value, key) => {
        const match = key.match(
            /^order_details\[(\d+)\]\[quantity\]$/); // Match keys like "order_details[1][quantity]"
        if (match) {
            const detailId = match[1];
            orderDetails.push({
                id: detailId,
                quantity: parseInt(value),
            });
        }
    });

    // Ensure orderDetails is not empty
    if (orderDetails.length === 0) {
        Swal.fire({
            title: 'Error!',
            text: 'No order details found to update.',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
        return;
    }

    fetch(`/orders/${currentOrderId}/edit`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            order_details: orderDetails
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    location.reload(); // Refresh the page on success
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to update order.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.message || 'An unexpected error occurred.',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        });
}

function closeEditOrderModal() {
    document.getElementById('editOrderModal').classList.add('hidden');
}

function submitRejectOrder() {
    var selected = document.querySelector('input[name="reject_reason_option"]:checked');
    var reason = '';

    if (selected) {
        if (selected.value === 'Other') {
            reason = document.getElementById('rejectOtherReason').value;
            if (reason.trim() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please provide a custom rejection reason.'
                });
                return;
            }
        } else {
            reason = selected.value;
        }
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please select a rejection reason.'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to reject this order?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, reject it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('rejectForm').action = "/orders/" + currentOrderId +
                "/reject";
            document.getElementById('rejectReasonInput').value = reason;
            document.getElementById('rejectForm').submit();
        }
    });
}

function openBatchQrModal() {
    // Show loading state while fetching data
    const ordersList = document.getElementById('batchOrdersList');
    ordersList.innerHTML =
        '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">Loading orders...</td></tr>';

    document.getElementById('batchQrModal').classList.remove('hidden');

    // Fetch processing orders for QR code generation
    fetch('{{ route('distributors.orders.processing') }}')
        .then(response => response.json())
        .then(data => {
            console.log("Response data:", data); // Debug log

            if (data.orders && data.orders.length > 0) {
                console.log("First order details:", data.orders[0]);
                console.log("formatted_order_id exists:", data.orders[0].hasOwnProperty('formatted_order_id'));
            }

            ordersList.innerHTML = '';

            if (!data.orders || data.orders.length === 0) {
                ordersList.innerHTML =
                    '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">No processing orders found</td></tr>';
                return;
            }

            data.orders.forEach(order => {


                ordersList.innerHTML += `
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">
                <input type="checkbox" class="border-gray-300 rounded cursor-pointer order-checkbox" 
                    value="${order.id}" onchange="updateSelectedCount()">
            </td>
            <td class="px-4 py-3">${order.formatted_order_id}</td>
            <td class="px-4 py-3">${order.user.first_name} ${order.user.last_name}</td>
            <td class="px-4 py-3">${formatDate(order.created_at)}</td>
        </tr>
        `;
            });

            updateSelectedCount();
        })
        .catch(error => {
            console.error('Error fetching orders:', error);
            ordersList.innerHTML =
                '<tr><td colspan="4" class="px-4 py-3 text-center text-red-500">Error loading orders. Please try again.</td></tr>';
        });
}

function closeBatchQrModal() {
    document.getElementById('batchQrModal').classList.add('hidden');
}

function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.order-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedOrders = document.querySelectorAll('.order-checkbox:checked');
    const selectedCount = document.getElementById('selectedCount');
    const generateButton = document.getElementById('generateQrButton');

    selectedCount.textContent = selectedOrders.length;
    generateButton.disabled = selectedOrders.length === 0;
}

function generateSelectedQrCodes() {
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked'))
        .map(checkbox => checkbox.value);

    if (selectedOrders.length === 0) {
        return;
    }

    // Create a form to submit the selected order IDs
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('distributors.orders.batch - qrcode') }}';
    form.style.display = 'none';

    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrfToken);

    // Add selected order IDs
    selectedOrders.forEach(orderId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'order_ids[]';
        input.value = orderId;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';

    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        console.error('Error formatting date:', e);
        return 'Invalid date';
    }
}

// Add this to the existing script section at the bottom
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('orderToggle');
    const statusIndicator = document.getElementById('statusIndicator');

    // Set initial status text
    updateStatusIndicator();

    toggle.addEventListener('change', function () {
        // Show loading indicator
        statusIndicator.textContent = "Updating...";
        statusIndicator.className = "text-sm font-medium text-blue-500";

        // Send AJAX request to update status
        fetch('{{ route('distributors.toggle - order - acceptance') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                accepting_orders: toggle.checked
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatusIndicator();
                } else {
                    // Revert toggle if there was an error
                    toggle.checked = !toggle.checked;
                    statusIndicator.textContent = "Update failed";
                    statusIndicator.className = "text-sm font-medium text-red-500";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert toggle if there was an error
                toggle.checked = !toggle.checked;
                statusIndicator.textContent = "Update failed";
                statusIndicator.className = "text-sm font-medium text-red-500";
            });
    });

    function updateStatusIndicator() {
        if (toggle.checked) {
            statusIndicator.textContent = "Accepting Orders";
            statusIndicator.className = "text-sm font-medium text-green-500";
        } else {
            statusIndicator.textContent = "Not Accepting Orders";
            statusIndicator.className = "text-sm font-medium text-red-500";
        }
    }
});
