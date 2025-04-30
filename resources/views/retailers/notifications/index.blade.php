<x-app-layout>
    <x-dashboard-nav />
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="flex py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <x-retailer-sidebar :user="Auth::user()" /> <!-- Retailer Side bar -->

        <div class="flex-1 space-y-6 lg:pl-8">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Notifications</h1>
                <div>
                    <span class="text-sm text-gray-500">Manage your notifications</span>
                </div>
            </div>

            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <header class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-900">
                            {{ __('Notification Center') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-800">
                            {{ __('Stay updated with all your account activities and important announcements.') }}
                        </p>
                    </div>
                    @if ($notifications->count() > 0)
                        <form method="POST" action="{{ route('retailers.notifications.mark-all-read') }}">
                            @csrf
                            <x-secondary-button type="submit">
                                {{ __('Mark all as read') }}
                            </x-secondary-button>
                        </form>
                    @endif
                </header>

                <div class="mt-6">
                    @if ($notifications->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach ($notifications as $notification)
                                <div class="py-5 @if (!$notification->is_read) bg-blue-50 rounded-md my-1 @endif">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 pt-0.5">
                                            @if (!$notification->is_read)
                                                <div class="p-1 bg-blue-100 rounded-full">
                                                    <span class="block w-3 h-3 bg-blue-600 rounded-full"></span>
                                                </div>
                                            @else
                                                <div class="p-1 bg-gray-100 rounded-full">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 ml-4">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ $notification->data['message'] ?? '' }}
                                            </p>

                                            <div class="flex items-center mt-3 space-x-3">
                                                @if (!$notification->is_read)
                                                    <form method="POST"
                                                        action="{{ route('retailers.notifications.mark-read') }}"
                                                        class="inline">
                                                        @csrf
                                                        <input type="hidden" name="notification_id"
                                                            value="{{ $notification->id }}">
                                                        <button type="submit"
                                                            class="flex items-center text-xs text-blue-600 hover:text-blue-800">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Mark as read
                                                        </button>
                                                    </form>
                                                @endif

                                                @if (isset($notification->type) && isset($notification->related_id))
                                                    @if ($notification->type == 'order_status' && isset($notification->related_id))
                                                        <a href="{{ route('retailers.orders.show', $notification->related_id) }}"
                                                            class="flex items-center text-xs text-green-600 hover:text-green-800">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                </path>
                                                            </svg>
                                                            View Order Details
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <div class="p-6 rounded-lg bg-gray-50">
                                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">No notifications yet</h3>
                                <p class="mt-2 text-sm text-gray-500">You'll be notified here when there are updates to
                                    your orders, products, or account.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="relative max-w-4xl p-6 mx-auto mt-10 bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-medium text-gray-900">Order Details</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="modalContent" class="mt-4">
                <!-- Order details will be loaded here -->
                <div class="flex items-center justify-center p-8">
                    <svg class="w-12 h-12 text-green-500 animate-spin" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>

            <div class="flex justify-end pt-4 mt-6 border-t border-gray-200">
                <button onclick="closeOrderModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-transparent rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function openOrderModal(orderId) {
            // Show the modal first with loading indicator
            document.getElementById('orderModal').classList.remove('hidden');

            // Fetch the order details
            fetch(`/retailers/profile/${orderId}/order-details`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    document.getElementById('modalContent').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalContent').innerHTML =
                        '<p class="text-center text-red-500">Error loading order details. Please try again.</p>';
                });
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.add('hidden');
            document.getElementById('modalContent').innerHTML =
                '<div class="flex items-center justify-center p-8"><svg class="w-12 h-12 text-green-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
        }

        // Close modal when clicking outside
        document.getElementById('orderModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeOrderModal();
            }
        });

        // Close modal with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeOrderModal();
            }
        });
    </script>
</x-app-layout>
<x-footer />
