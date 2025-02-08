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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center">
                <!-- Cart -->
                <div class="relative">
                    <a href="{{route('cart.show')}}" class="p-2 text-gray-500 hover:text-gray-700 hover:cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </a>
                </div>

                <!-- Notifications -->

                <div class="relative ml-4">
                    <button onclick="toggleNotifications()" class="p-2 text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                    <!-- Notifications Popup -->
                    <div id="notificationsPopup"
                        class="absolute right-0 z-50 hidden mt-2 bg-white rounded-lg shadow-xl w-80">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-800">Notifications</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div class="p-4">
                                <p class="text-sm">Your order #1234 has been shipped!</p>
                                <p class="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                    </div>
                </div>



                <script>
                    function toggleNotifications() {
                        const popup = document.getElementById('notificationsPopup');
                        const backdrop = document.getElementById('backdrop');
                        popup.classList.toggle('hidden');
                        backdrop.classList.toggle('hidden');
                    }

                    function closeAll() {
                        document.getElementById('notificationsPopup').classList.add('hidden');
                        document.getElementById('backdrop').classList.add('hidden');
                    }

                    // Close popups when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!event.target.closest('#notificationsPopup') &&
                            !event.target.closest('button') &&
                            !document.getElementById('notificationsPopup').classList.contains('hidden')) {
                            closeAll();
                        }
                    });
                </script>

                <!-- Profile Section with Dropdown -->
                <div class="relative ml-4">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center cursor-pointer">
                                <img class="object-cover w-8 h-8 rounded-full"
                                    src="{{ asset('img/products/rtc-chicken-bbq.png') }}" alt="Profile">
                                <span class="ml-2 text-sm text-gray-700">{{ Auth::user()->first_name }}</span>
                                <div class="ms-1">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="bg-gray-800">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-center space-x-8">
                <!-- Changed links to spans or buttons -->
                <a href="{{route('retailers.dashboard')}}" class="px-3 py-2 text-white cursor-pointer hover:text-green-400">HOME</a>
                <span class="px-3 py-2 text-white cursor-pointer hover:text-green-400">DISTRIBUTORS</span>
                <span class="px-3 py-2 text-white cursor-pointer hover:text-green-400">PRODUCTS</span>
            </div>
        </div>
    </div>
</nav>
