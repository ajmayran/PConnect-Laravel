<x-distributor-layout>
    <div class="container py-8 mx-auto">
        <h1 class="mb-6 text-2xl font-bold text-gray-800">My Subscription</h1>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Current Subscription Status -->
            <div class="lg:col-span-2">
                <div class="overflow-hidden bg-white rounded-lg shadow-md">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-700">Current Subscription</h2>
                        
                        @if($activeSubscription)
                            <div class="mt-4">
                                <div class="flex items-center mt-6">
                                    <div class="p-3 rounded-full bg-gradient-to-r from-green-400 to-green-500">
                                        <iconify-icon icon="mdi:crown" class="w-8 h-8 text-white"></iconify-icon>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $activeSubscription->plan)) }} Plan
                                        </h3>
                                        <p class="text-gray-600">
                                            <span class="font-medium text-green-600">Active</span> until {{ is_string($activeSubscription->expires_at) ? $activeSubscription->expires_at : ($activeSubscription->expires_at ? $activeSubscription->expires_at->format('M d, Y') : 'N/A') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="p-4 mt-6 bg-gray-50 rounded-xl">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Started on</p>
                                            <p class="font-semibold">{{ is_string($activeSubscription->starts_at) ? $activeSubscription->starts_at : ($activeSubscription->starts_at ? $activeSubscription->starts_at->format('M d, Y') : 'N/A') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Expires on</p>
                                            <p class="font-semibold">{{ is_string($activeSubscription->expires_at) ? $activeSubscription->expires_at : ($activeSubscription->expires_at ? $activeSubscription->expires_at->format('M d, Y') : 'N/A') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Days remaining</p>
                                            <p class="font-semibold">
                                                @if(is_string($activeSubscription->expires_at))
                                                    {{ \Carbon\Carbon::parse($activeSubscription->expires_at)->diffInDays(now()) }} days
                                                @elseif($activeSubscription->expires_at)
                                                    {{ $activeSubscription->expires_at->diffInDays(now()) }} days
                                                @else
                                                    0 days
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Reference number</p>
                                            <p class="font-semibold">{{ $activeSubscription->reference_number }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <div class="flex items-center mb-2">
                                        <iconify-icon icon="material-symbols:info-outline" class="w-5 h-5 mr-2 text-blue-500"></iconify-icon>
                                        <p class="text-sm text-gray-600">Your subscription will automatically expire on 
                                            {{ is_string($activeSubscription->expires_at) ? $activeSubscription->expires_at : ($activeSubscription->expires_at ? $activeSubscription->expires_at->format('M d, Y') : 'N/A') }}
                                        </p>
                                    </div>
                                    
                                    <a href="{{ route('distributors.subscription') }}" class="inline-flex items-center px-4 py-2 mt-4 text-white bg-green-600 rounded-md hover:bg-green-700">
                                        <iconify-icon icon="mdi:renew" class="w-5 h-5 mr-2"></iconify-icon>
                                        Renew Subscription
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center p-6 mt-4 text-center bg-gray-50 rounded-xl">
                                <iconify-icon icon="mdi:crown-outline" class="w-16 h-16 text-yellow-500"></iconify-icon>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">Free Account</h3>
                                <p class="mt-2 text-gray-600">You're currently on a free account with basic features.</p>
                                <a href="{{ route('distributors.subscription') }}" class="inline-flex items-center px-4 py-2 mt-6 font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                    Upgrade to Premium
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Benefits/Features -->
            <div class="lg:col-span-1">
                <div class="overflow-hidden bg-white rounded-lg shadow-md">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-700">Subscription Benefits</h2>
                        
                        <ul class="mt-4 space-y-3">
                            <li class="flex items-start">
                                <iconify-icon icon="mdi:check-circle" class="flex-shrink-0 w-5 h-5 mt-1 text-green-500"></iconify-icon>
                                <span class="ml-3 text-gray-600">Unlimited product listings</span>
                            </li>
                            <li class="flex items-start">
                                <iconify-icon icon="mdi:check-circle" class="flex-shrink-0 w-5 h-5 mt-1 text-green-500"></iconify-icon>
                                <span class="ml-3 text-gray-600">Advanced analytics and insights</span>
                            </li>
                            <li class="flex items-start">
                                <iconify-icon icon="mdi:check-circle" class="flex-shrink-0 w-5 h-5 mt-1 text-green-500"></iconify-icon>
                                <span class="ml-3 text-gray-600">Priority customer support</span>
                            </li>
                            <li class="flex items-start">
                                <iconify-icon icon="mdi:check-circle" class="flex-shrink-0 w-5 h-5 mt-1 text-green-500"></iconify-icon>
                                <span class="ml-3 text-gray-600">Featured in retailer searches</span>
                            </li>
                            <li class="flex items-start">
                                <iconify-icon icon="mdi:check-circle" class="flex-shrink-0 w-5 h-5 mt-1 text-green-500"></iconify-icon>
                                <span class="ml-3 text-gray-600">Promotional marketing tools</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription History -->
        <div class="mt-8">
            <h2 class="mb-4 text-xl font-bold text-gray-800">Subscription History</h2>
            
            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Plan
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Start Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    End Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($subscriptionHistory as $subscription)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                        {{ ucfirst(str_replace('_', ' ', $subscription->plan)) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                        {{ is_string($subscription->starts_at) ? $subscription->starts_at : ($subscription->starts_at ? $subscription->starts_at->format('M d, Y') : 'Not started') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                        {{ is_string($subscription->expires_at) ? $subscription->expires_at : ($subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'Not set') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                        ₱{{ number_format($subscription->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        @if($subscription->status === 'active')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                                Active
                                            </span>
                                        @elseif($subscription->status === 'expired')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                                                Expired
                                            </span>
                                        @elseif($subscription->status === 'pending')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-sm text-center text-gray-500">
                                        No subscription history found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-distributor-layout>