<x-distributor-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <header class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-900">
                                {{ __('Notification Center') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-800">
                                {{ __("Stay updated with all your business activities and important announcements.") }}
                            </p>
                        </div>
                        @if ($notifications->count() > 0)
                        <form method="POST" action="{{ route('distributors.notifications.mark-all-read') }}" class="inline">
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
                                    <div class="py-5 @if(!$notification->is_read) bg-blue-50 rounded-md my-1 @endif">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 pt-0.5">
                                                @if (!$notification->is_read)
                                                    <div class="p-1 bg-blue-100 rounded-full">
                                                        <span class="block w-3 h-3 bg-blue-600 rounded-full"></span>
                                                    </div>
                                                @else
                                                    <div class="p-1 bg-gray-100 rounded-full">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
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
                                                        <form method="POST" action="{{ route('distributors.notifications.mark-read') }}" class="inline">
                                                            @csrf
                                                            <input type="hidden" name="notification_id" value="{{ $notification->id }}">
                                                            <button type="submit" class="flex items-center text-xs text-blue-600 hover:text-blue-800">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                Mark as read
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <!-- Add conditional links based on notification type -->
                                                    @if(isset($notification->type) && isset($notification->related_id))
                                                        @if($notification->type == 'order_status' && isset($notification->related_id))
                                                            <a href="{{ route('distributors.orders.show', $notification->related_id) }}" 
                                                               class="flex items-center text-xs text-green-600 hover:text-green-800">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                                View Order
                                                            </a>
                                                        @elseif($notification->type == 'payment_update' && isset($notification->related_id))
                                                            <a href="{{ route('distributors.payments.show', $notification->related_id) }}" 
                                                               class="flex items-center text-xs text-green-600 hover:text-green-800">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                                View Payment
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
                            <div class="flex flex-col items-center justify-center mt-10 text-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">No notifications yet</h3>
                                <p class="mt-2 text-sm text-gray-500">You'll be notified here when there are updates to orders, payments, or your account.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-distributor-layout>