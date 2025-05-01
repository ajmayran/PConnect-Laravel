<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.userId = {{ auth()->id() }};
        window.pusherAppKey = "{{ env('PUSHER_APP_KEY') }}";
        window.pusherAppCluster = "{{ env('PUSHER_APP_CLUSTER') }}";
    </script>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>{{ $retailer->first_name }} {{ $retailer->last_name }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/iconify-icon/dist/iconify-icon.min.js"></script>
</head>

<body class="bg-gray-100 font-lexend">
    <x-dist_navbar />

    <div class="container max-w-6xl px-4 py-6 mx-auto mt-2">
        <!-- Back button -->
        <div class="flex items-center mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center w-10 h-10 mr-3 text-gray-600 transition-colors rounded-full hover:bg-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="sr-only">Back</span>
            </a>
            <a href="{{ route('distributors.dashboard') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
            </a>
        </div>

        <!-- Retailer Profile Header -->
        <div class="overflow-hidden bg-white rounded-lg shadow-md">
            <div class="p-6">
                <div class="flex flex-col items-start gap-6 md:flex-row md:items-center">
                    <div class="flex-shrink-0">
                        @if ($retailer->retailerProfile && $retailer->retailerProfile->profile_picture)
                            <img src="{{ asset('storage/' . $retailer->retailerProfile->profile_picture) }}"
                                alt="{{ $retailer->first_name }}" class="object-cover w-32 h-32 rounded-full">
                        @else
                            <div class="flex items-center justify-center w-32 h-32 bg-gray-200 rounded-full">
                                <span
                                    class="text-3xl font-medium text-gray-700">{{ $retailer->first_name[0] }}{{ $retailer->last_name[0] }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800">{{ $retailer->first_name }}
                                {{ $retailer->last_name }}</h1>
                                @if ($retailer->retailerProfile && $retailer->retailerProfile->business_name)
                                    <p class="text-lg text-gray-600">{{ $retailer->retailerProfile->business_name }}</p>
                                @endif
                            
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">Retailer</span>
                                    @if($retailer->is_verified)
                                        <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">Verified Account</span>
                                    @endif
                                    @if($retailer->is_blocked)
                                        <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">Blocked</span>
                                    @endif
                                </div>

                                <div class="mt-4 space-y-2 text-sm text-gray-600">
                                    <p class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ $retailer->email }}
                                    </p> 
                                    @if ($retailer->retailerProfile && ($retailer->retailerProfile->barangay || $retailer->retailerProfile->street))
                                        <p class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            @if ($retailer->retailerProfile->barangay_name)
                                                {{ $retailer->retailerProfile->barangay_name }}
                                            @endif
                                            @if ($retailer->retailerProfile->street)
                                                {{ $retailer->retailerProfile->barangay_name ? ', ' : '' }}{{ $retailer->retailerProfile->street }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mt-4 sm:mt-0">
                                <a href="{{ route('distributors.messages.show', $retailer->id) }}"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-md hover:bg-green-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    Message
                                </a>

                                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                                    <button @click="open = !open"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                        Actions
                                    </button>

                                    <div x-show="open" 
                                        class="absolute right-0 z-10 w-48 py-1 mt-2 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <button @click="$dispatch('open-modal', 'report-retailer-modal'); open = false"
                                            class="flex w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-red-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Report Retailer
                                        </button>
                                        
                                        <form action="{{ route('distributors.retailers.block', $retailer->id) }}"
                                            id="blockRetailerForm" method="POST" class="w-full">
                                            @csrf
                                            <button type="button" onclick="confirmBlock()"
                                                class="flex w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-5 h-5 mr-2 {{ $retailer->is_blocked ? 'text-green-500' : 'text-red-500' }}" 
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                {{ $retailer->is_blocked ? 'Unblock Retailer' : 'Block Retailer' }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('distributors.messages.block', $retailer->id) }}"
                                            id="blockMessagesForm" method="POST" class="w-full">
                                            @csrf
                                            <input type="hidden" name="reason" id="messageBlockReason" value="Blocked by distributor">
                                            <button type="button" onclick="confirmMessageBlock()"
                                                class="flex w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-5 h-5 mr-2 text-orange-500" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                Block Messages
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 mt-8 sm:grid-cols-2 lg:grid-cols-3">
            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-700">Account Status</h3>
                        <span class="{{ $retailer->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ ucfirst($retailer->status) }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        Member since: {{ $retailer->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-700">Communication</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('distributors.messages.show', $retailer->id) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:underline">
                            View Message History
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-700">Business Details</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="mt-2 space-y-1 text-sm text-gray-600">
                        @if($retailer->retailerProfile && $retailer->retailerProfile->business_name)
                            <p><span class="font-medium">Business:</span> {{ $retailer->retailerProfile->business_name }}</p>
                        @else
                            <p><span class="font-medium">Business:</span> Not provided</p>
                        @endif
                        
                        @if($retailer->retailerProfile && $retailer->retailerProfile->business_type)
                            <p><span class="font-medium">Type:</span> {{ ucfirst($retailer->retailerProfile->business_type) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Statistics Cards -->
        <div class="mt-8">
            <h2 class="mb-4 text-xl font-bold text-gray-800">Account Statistics</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="p-4 transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Orders Placed</p>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $orderStats['total'] }}</h3>
                        </div>
                        <div class="p-3 text-white bg-blue-500 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-4 transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completed Orders</p>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $orderStats['completed'] }}</h3>
                        </div>
                        <div class="p-3 text-white bg-green-500 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-4 transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Processing Orders</p>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $orderStats['processing'] }}</h3>
                        </div>
                        <div class="p-3 text-white bg-yellow-500 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-4 transition-all bg-white rounded-lg shadow-sm hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Spent</p>
                            <h3 class="text-2xl font-bold text-gray-900">₱{{ number_format($orderStats['totalSpent'], 2) }}</h3>
                        </div>
                        <div class="p-3 text-white bg-purple-500 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Retailer Modal -->
    <div id="report-retailer-modal" x-data="{ show: false }"
        x-on:open-modal.window="$event.detail == 'report-retailer-modal' ? show = true : null"
        x-on:keydown.escape.window="show = false" x-show="show"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50" x-cloak>
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl md:w-1/2 sm:w-2/3" @click.away="show = false">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Report Retailer</h2>
                <button @click="show = false"
                    class="p-1 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('distributors.retailers.report', $retailer->id) }}" method="POST"
                id="reportRetailerForm">
                @csrf
                <div class="p-4 space-y-4">
                    <div>
                        <label for="report_reason" class="block mb-2 text-sm font-medium text-gray-700">Reason for
                            reporting</label>
                        <select id="report_reason" name="reason"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a reason</option>
                            <option value="inappropriate_behavior">Inappropriate Behavior</option>
                            <option value="fraud">Fraud or Scam</option>
                            <option value="fake_profile">Fake Profile</option>
                            <option value="payment_issues">Payment Issues</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="report_details" class="block mb-2 text-sm font-medium text-gray-700">Additional
                            Details</label>
                        <textarea id="report_details" name="details" rows="4"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Please provide more details about your report"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t rounded-b-lg bg-gray-50">
                    <button type="button" @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmReport()"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function confirmBlock() {
            const actionText = "{{ $retailer->is_blocked ? 'unblock' : 'block' }}";
            const retailerName = "{{ $retailer->first_name }} {{ $retailer->last_name }}";

            Swal.fire({
                title: `Confirm ${actionText}`,
                html: `Are you sure you want to ${actionText} <strong>${retailerName}</strong>?<br><br>
               ${actionText === 'block' ? 'This retailer will no longer be able to place orders with you.' : 
               'This will allow the retailer to place orders with you again.'}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: actionText === 'block' ? '#d33' : '#3085d6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: `Yes, ${actionText} retailer`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('blockRetailerForm').submit();
                }
            });
        }
        
        function confirmMessageBlock() {
            const retailerName = "{{ $retailer->first_name }} {{ $retailer->last_name }}";
            
            Swal.fire({
                title: 'Block Messages',
                html: `Are you sure you want to block messages from <strong>${retailerName}</strong>?<br><br>
                You will no longer receive messages from this retailer.`,
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Reason for blocking (optional)',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Block Messages',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        document.getElementById('messageBlockReason').value = result.value;
                    }
                    document.getElementById('blockMessagesForm').submit();
                }
            });
        }

        function confirmReport() {
            const reason = document.getElementById('report_reason').value;
            const details = document.getElementById('report_details').value;

            if (!reason) {
                Swal.fire({
                    icon: 'error',
                    title: 'Required Field Missing',
                    text: 'Please select a reason for reporting this retailer',
                });
                return;
            }

            Swal.fire({
                title: 'Report Retailer',
                html: `Are you sure you want to report <strong>{{ $retailer->first_name }} {{ $retailer->last_name }}</strong>?<br><br>
               This report will be reviewed by our team.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, submit report',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reportRetailerForm').submit();
                }
            });
        }
    </script>
</body>
</html>