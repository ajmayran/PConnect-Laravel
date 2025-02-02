<nav class="bg-white border-b border-gray-100 shadow-sm">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Top Navigation Bar -->
        <div class="flex justify-between h-16">
            <!-- Left Side -->
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <div class="flex items-center"> <!-- Changed to div since route might not be ready -->
                        <img class="w-auto h-10" src="{{ asset('img/pconnect/Pconnect-Logo.png') }}" alt="PConnect">
                    </div>
                </div>

                <!-- Search -->
                <div class="flex items-center ml-6">
                    <select class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-l">
                        <option value="all">All Categories</option>
                        <option value="all">Toiletries</option>
                        <option value="all">Canned food</option>
                        <!-- Add more categories -->
                    </select>
                    <input type="text" placeholder="Search for items..." 
                           class="px-4 py-2 border-gray-300 w-96 border-y focus:ring-green-500 focus:border-green-500">
                    <button class="px-6 py-2 text-white bg-green-500 rounded-r hover:bg-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center">
                <!-- Cart - Using button instead of link -->
                <button class="p-2 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </button>

                <!-- Notifications -->
                <div class="relative ml-4" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                </div>

                <!-- Profile Section -->
                <div class="relative ml-4">
                    <div class="flex items-center cursor-pointer">
                        <img class="object-cover w-8 h-8 rounded-full" src="{{ asset('storage/products/rtc-chicken-bbq.png') }}" alt="Profile">
                        <span class="ml-2 text-sm text-gray-700">Retailer user</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="bg-gray-800">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-center space-x-8">
                <!-- Changed links to spans or buttons -->
                <span class="px-3 py-2 text-white cursor-pointer hover:text-green-400">HOME</span>
                <span class="px-3 py-2 text-white cursor-pointer hover:text-green-400">DISTRIBUTORS</span>
                <span class="px-3 py-2 text-white cursor-pointer hover:text-green-400">PRODUCTS</span>
            </div>
        </div>
    </div>
</nav>