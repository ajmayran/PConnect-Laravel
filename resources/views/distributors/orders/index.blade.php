<x-distributor-layout>
    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-4 mx-auto">
            <h1 class="mb-4 text-xl font-semibold text-center sm:text-2xl">Orders</h1>
            @if ($orders->isEmpty())
                <p class="text-sm text-center sm:text-base">No orders found.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs bg-white border sm:text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-2 py-1 border">Order ID</th>
                                <th class="px-2 py-1 border">Retailer Name</th>
                                <th class="px-2 py-1 border">Total Amount</th>
                                <th class="px-2 py-1 border">Date and Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr onclick="openModal(this)" data-order-id="{{ $order->id }}"
                                    data-retailer='@json($order->user)'
                                    data-details='@json($order->orderDetails)'
                                    data-created-at="{{ $order->created_at->format('F d, Y h:i A') }}"
                                    class="border-t cursor-pointer hover:bg-gray-100">
                                    <td class="px-2 py-1">{{ $order->id }}</td>
                                    <td class="px-2 py-1">{{ $order->user->first_name }} {{ $order->user->last_name }}
                                    </td>
                                    <td class="px-2 py-1">
                                        ₱{{ number_format(optional($order->orderDetails)->sum('subtotal') ?: 0, 2) }}
                                    </td>
                                    <td class="px-2 py-1">
                                        <div class="flex flex-col items-center sm:flex-row justify-evenly">
                                            <span
                                                class="font-semibold">{{ $order->created_at->format('F d, Y') }}</span>
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded mt-1 sm:mt-0">
                                                {{ $order->created_at->format('h:i A') }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Modal -->
        <div id="orderModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="w-11/12 p-4 bg-white rounded shadow-lg sm:p-6 md:w-1/2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold sm:text-xl" id="modalTitle">Order Details</h2>
                    <button onclick="closeModal()"
                        class="text-sm text-gray-500 sm:text-base hover:text-gray-700">&times;</button>
                </div>
                <div id="modalContent">
                    <p id="orderInfo" class="text-xs sm:text-sm"></p>
                </div>
                <div class="mt-4 text-right">
                    <button onclick="closeModal()"
                        class="px-3 py-1 text-xs text-white bg-blue-500 rounded sm:text-sm hover:bg-blue-600">Close</button>
                </div>
            </div>
        </div>

        <script>
            var storageBaseUrl = "{{ asset('storage') }}";

            function openModal(row) {
                var orderId = row.getAttribute('data-order-id');
                var retailer = JSON.parse(row.getAttribute('data-retailer'));
                var details = JSON.parse(row.getAttribute('data-details'));
                var dateTime = row.getAttribute('data-created-at');

                document.getElementById('modalTitle').innerText = 'Order #' + orderId + ' Details';

                var modalHtml = '<h3 class="mb-2 font-semibold text-md">Retailer Profile:</h3>';
                modalHtml += '<p><strong>Name:</strong> ' + retailer.first_name + ' ' + retailer.last_name + '</p>';
                if (retailer.email) {
                    modalHtml += '<p><strong>Email:</strong> ' + retailer.email + '</p>';
                }
                if (retailer.address) {
                    modalHtml += '<p><strong>Address:</strong> ' + retailer.address + '</p>';
                }

                // Use retailer_profile from the User model
                if (retailer.retailer_profile) {
                    var profile = retailer.retailer_profile;
                    if (profile.address) {
                        modalHtml += '<p><strong>Address:</strong> ' + profile.address + '</p>';
                    }
                    if (profile.phone) {
                        modalHtml += '<p><strong>Phone:</strong> ' + profile.phone + '</p>';
                    }
                    if (profile.profile_picture) {
                        modalHtml += '<div class="my-2"><img src="' + storageBaseUrl + '/' + profile.profile_picture +
                            '" alt="Profile Image" class="w-16 h-auto border rounded-full" /></div>';
                    }
                }

                modalHtml += '<h3 class="my-2 font-semibold text-md">Order Details:</h3>';
                modalHtml += '<table class="min-w-full text-xs bg-white border sm:text-sm">';
                modalHtml += '<thead><tr class="bg-gray-100">';
                modalHtml += '<th class="px-2 py-1 border">Image</th>';
                modalHtml += '<th class="px-2 py-1 border">Product</th>';
                modalHtml += '<th class="px-2 py-1 border">Price</th>';
                modalHtml += '<th class="px-2 py-1 border">Quantity</th>';
                modalHtml += '<th class="px-2 py-1 border">Subtotal</th>';
                modalHtml += '</tr></thead><tbody>';
                details.forEach(function(detail) {
                    modalHtml += '<tr class="border-t">';
                    // Adjust the image URL as needed – this assumes the image URL is stored in detail.product.image
                    if (detail.product.image) {
                        modalHtml += '<td class="px-2 py-1"><img src="' + storageBaseUrl + '/' + detail.product.image +
                            '" alt="' + '" class="w-16 h-auto" /></td>';
                    } else {
                        modalHtml += '<td class="px-2 py-1"><img src="img/default-product.jpg" class="w-16 h-auto" /></td>';    
                    }
                    modalHtml += '<td class="px-2 py-1">' + detail.product.product_name + '</td>';
                    modalHtml += '<td class="px-2 py-1">₱' + parseFloat(detail.product.price).toFixed(2) + '</td>';
                    modalHtml += '<td class="px-2 py-1">' + detail.quantity + '</td>';
                    modalHtml += '<td class="px-2 py-1">₱' + parseFloat(detail.subtotal).toFixed(2) + '</td>';
                    modalHtml += '</tr>';
                });
                modalHtml += '</tbody></table>';

                document.getElementById('modalContent').innerHTML = modalHtml;
                document.getElementById('orderModal').classList.remove('hidden');
            }

            function closeModal() {
                document.getElementById('orderModal').classList.add('hidden');
            }
        </script>
    </div>
</x-distributor-layout>
