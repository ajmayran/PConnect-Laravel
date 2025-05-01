<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="mb-6 text-2xl font-semibold">Distributor Subscriptions</h1>
                    
                    <!-- Stats cards -->
                    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
                        <div class="p-4 bg-white border rounded-lg shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-sm font-medium text-gray-600">Total</h2>
                                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-white border rounded-lg shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-sm font-medium text-gray-600">Active</h2>
                                    <p class="text-2xl font-bold">{{ $stats['active'] }}</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-white border rounded-lg shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-sm font-medium text-gray-600">Expired/Cancelled</h2>
                                    <p class="text-2xl font-bold">{{ $stats['expired'] }}</p>
                                </div>
                                <div class="p-3 bg-red-100 rounded-full">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-white border rounded-lg shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-sm font-medium text-gray-600">Pending</h2>
                                    <p class="text-2xl font-bold">{{ $stats['pending'] }}</p>
                                </div>
                                <div class="p-3 bg-yellow-100 rounded-full">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filter tabs -->
                    <div class="flex mb-4 border-b">
                        <a href="{{ route('admin.subscriptions.index') }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $status === 'all' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            All
                        </a>
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'active']) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $status === 'active' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Active
                        </a>
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'expired']) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $status === 'expired' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Expired
                        </a>
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'pending']) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $status === 'pending' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Pending
                        </a>
                        <a href="{{ route('admin.subscriptions.index', ['status' => 'failed']) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $status === 'failed' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Failed
                        </a>
                    </div>
                    
                    <!-- Subscriptions Table -->
                    <div class="overflow-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Distributor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Plan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Amount
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Start Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Expiry Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($subscriptions as $subscription)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $subscription->distributor->company_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $subscription->distributor->user->email ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $subscription->plan_name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            ₱{{ number_format($subscription->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                   ($subscription->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($subscription->status === 'expired' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : 'Not started' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $subscription->expires_at ? $subscription->expires_at->format('Y-m-d') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="mr-2 text-indigo-600 hover:text-indigo-900">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $subscriptions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>