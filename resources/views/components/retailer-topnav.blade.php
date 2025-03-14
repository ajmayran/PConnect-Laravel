<x-profile-completion-alert :user="Auth::user()" />

<nav class="bg-white border-b border-gray-100 shadow-sm">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Top Navigation Bar -->
        <div class="flex justify-between h-16">
            <!-- Left Side -->
            <div class="flex items-center">
                <!-- Logo - Hidden on mobile -->
                <div class="flex items-center flex-shrink-0 hidden sm:block">
                    <div class="flex items-center">
                        <img class="w-auto h-10" src="{{ asset('img/Pconnect Logo.png') }}" alt="PConnect">
                    </div>
                </div>

                <!-- Mobile Menu Button - Visible only on mobile -->
                <div class="sm:hidden">
                    <button id="mobile-menu-button" class="p-2 text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path id="burger-icon" class="block" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center">
                <!-- Cart -->
                <div class="relative">
                    <a href="{{ route('retailers.cart.index') }}" class="p-2 text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </a>
                </div>

                <!-- Messages -->
                <div class="relative ml-4">
                    <button onclick="toggleMessagesDropdown()" class="p-2 text-gray-500 hover:text-gray-700">
                        <div class="relative w-6 h-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <div id="topnav-unread-message-badge"
                                class="absolute flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full -top-1 -right-1">
                            </div>
                        </div>
                    </button>

                    <!-- Messages Dropdown -->
                    <div id="messagesDropdown"
                        class="absolute z-[100] hidden mt-2 origin-top-right bg-white rounded-lg shadow-xl w-80 border overflow-hidden max-h-[480px] sm:right-0 
                           sm:absolute right-0 md:right-0 lg:right-0">
                        <div class="p-3 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-800">Messages</h3>
                            </div>
                        </div>

                        <!-- Messages Content -->
                        <div class="overflow-y-auto divide-y divide-gray-200 max-h-80" id="messages-preview-container">
                            <div class="p-3 text-sm text-center text-gray-500">
                                Loading messages...
                            </div>
                        </div>

                        <div class="p-3 text-center border-t border-gray-200">
                            <a href="{{ route('retailers.messages.index') }}"
                                class="text-sm text-green-600 hover:text-green-700">
                                See All Messages
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="relative ml-4">
                    <button onclick="toggleNotifications()" class="p-2 text-gray-500 hover:text-gray-700">
                        <div class="relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span id="notification-badge"
                                class="absolute flex items-center justify-center hidden w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full -top-1 -right-1">
                            </span>
                        </div>
                    </button>

                    <!-- Notifications Popup -->
                    <div id="notificationsPopup"
                        class="absolute z-[100] hidden mt-2 origin-top-right bg-white rounded-lg shadow-xl w-80 border overflow-hidden max-h-[480px] sm:right-0 
                           sm:absolute right-0 md:right-0 lg:right-0">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-800">Notifications</h3>
                            <button id="mark-all-notifications-read" class="text-xs text-blue-600 hover:text-blue-800">
                                Mark all as read
                            </button>
                        </div>
                        <div class="overflow-y-auto divide-y divide-gray-200 max-h-80"
                            id="notifications-preview-container">
                            <div class="p-3 text-sm text-center text-gray-500">
                                Loading notifications...
                            </div>
                        </div>
                        <div class="p-4 text-center border-t border-gray-200">
                            <a href="{{ route('retailers.notifications.index') }}"
                                class="text-sm text-green-600 hover:text-green-700">See All Notifications</a>
                        </div>
                    </div>
                </div>

                <!-- Profile Section with Dropdown - Hidden on mobile -->
                <div class="relative hidden ml-4 sm:block">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center cursor-pointer">
                                <img class="object-cover w-8 h-8 border rounded-full"
                                    src="{{ Auth::user()->retailerProfile && Auth::user()->retailerProfile->profile_picture ? asset('storage/' . Auth::user()->retailerProfile->profile_picture) : asset('img/default-profile.png') }}"
                                    alt="Profile">
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
                            <x-dropdown-link :href="route('retailers.profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                @csrf
                                <x-dropdown-link :href="route('logout')" class="block w-full"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Panel - Hidden by default -->
    <div id="mobile-menu" class="fixed inset-0 z-[150] hidden">
        <!-- Backdrop -->
        <div id="mobile-backdrop" class="absolute inset-0 bg-black opacity-50"></div>

        <!-- Menu Panel -->
        <div
            class="absolute top-0 right-0 w-4/5 h-full max-w-xs overflow-y-auto transition-transform duration-300 ease-in-out transform bg-white shadow-xl">
            <!-- User Profile Section -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <img class="object-cover w-12 h-12 border-2 border-green-500 rounded-full"
                        src="{{ Auth::user()->retailerProfile && Auth::user()->retailerProfile->profile_picture ? asset('storage/' . Auth::user()->retailerProfile->profile_picture) : asset('img/default-profile.png') }}"
                        alt="Profile">
                    <div class="ml-3">
                        <p class="text-base font-semibold text-gray-800">{{ Auth::user()->first_name }}
                            {{ Auth::user()->last_name }}</p>
                        <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Navigation -->
            <div class="px-4 py-2 border-b border-gray-200">
                <h3 class="py-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Main Navigation</h3>
                <a href="{{ route('retailers.dashboard') }}"
                    class="flex items-center py-3 {{ request()->routeIs('retailers.dashboard') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    HOME
                </a>
                <a href="{{ route('retailers.all-distributor') }}"
                    class="flex items-center py-3 {{ request()->routeIs('retailers.all-distributor') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    DISTRIBUTORS
                </a>
                <a href="{{ route('retailers.all-product') }}"
                    class="flex items-center py-3 {{ request()->routeIs('retailers.all-product') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    PRODUCTS
                </a>
                <a href="{{ route('retailers.cart.index') }}"
                    class="flex items-center py-3 {{ request()->routeIs('retailers.cart.*') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    CART
                </a>
            </div>

            <!-- Account Menu (from sidebar) -->
            <div class="px-4 py-2">
                <h3 class="py-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Account</h3>

                <a href="{{ route('retailers.profile.edit') }}"
                    class="flex items-center py-3 {{ request()->routeIs('retailers.profile.edit') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                </a>

                <a href="{{ route('retailers.profile.my-purchase') }}"
                    class="flex items-center py-3 {{ request()->routeIs('retailers.profile.my-purchase') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                        </path>
                    </svg>
                    My Purchase
                </a>

                <a href="{{ route('retailers.notifications.index') }}"
                    class="flex items-center py-3 {{ request()->routeIs('retailers.notifications.*') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    Notifications
                    <span id="mobile-notification-badge"
                        class="inline-flex items-center justify-center hidden px-2 py-1 ml-2 text-xs font-bold leading-none text-white bg-red-500 rounded-full"></span>
                </a>

                <a href="{{ route('retailers.messages.index') }}"
                    class="flex items-center py-3 {{ request()->routeIs('retailers.messages.*') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                    Messages
                    <span id="mobile-unread-message-badge"
                        class="inline-flex items-center justify-center hidden px-2 py-1 ml-2 text-xs font-bold leading-none text-white bg-red-500 rounded-full"></span>
                </a>

                <a href="{{ route('retailers.profile.settings') }}"
                    class="flex items-center py-3 {{ request()->routeIs('retailers.profile.settings') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </a>
            </div>

            <!-- Footer Actions -->
            <div class="px-4 py-2 mt-6 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full py-3 text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span class="font-medium">Log Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Bar - Hidden on mobile -->
    <div class="hidden bg-gray-800 sm:block">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-center space-x-8">
                <a href="{{ route('retailers.dashboard') }}"
                    class="px-3 py-2 cursor-pointer {{ request()->routeIs('retailers.dashboard') ? 'text-green-400 font-bold' : 'text-white hover:text-green-400' }}">
                    HOME
                </a>
                <a href="{{ route('retailers.all-distributor') }}"
                    class="px-3 py-2 cursor-pointer {{ request()->routeIs('retailers.all-distributor') ? 'text-green-400 font-bold' : 'text-white hover:text-green-400' }}">
                    DISTRIBUTORS
                </a>
                <a href="{{ route('retailers.all-product') }}"
                    class="px-3 py-2 cursor-pointer {{ request()->routeIs('retailers.all-product') ? 'text-green-400 font-bold' : 'text-white hover:text-green-400' }}">
                    PRODUCTS
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Specific Overlays -->
    <div id="mobile-message-overlay" class="fixed inset-0 z-[200] hidden bg-white overflow-hidden">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold">Messages</h2>
                <button onclick="closeMobileMessageOverlay()" class="p-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="mobile-messages-container" class="flex-1 overflow-y-auto divide-y divide-gray-200">
                <div class="p-3 text-center">Loading messages...</div>
            </div>
            <div class="p-4 border-t">
                <a href="{{ route('retailers.messages.index') }}"
                    class="block w-full py-2 text-center text-white bg-green-600 rounded-md">
                    See All Messages
                </a>
            </div>
        </div>
    </div>

    <div id="mobile-notifications-overlay" class="fixed inset-0 z-[200] hidden bg-white overflow-hidden">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold">Notifications</h2>
                <button onclick="closeMobileNotificationOverlay()" class="p-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="mobile-notifications-container" class="flex-1 overflow-y-auto divide-y divide-gray-200">
                <div class="p-3 text-center">Loading notifications...</div>
            </div>
            <div class="p-4 border-t">
                <a href="{{ route('retailers.notifications.index') }}"
                    class="block w-full py-2 text-center text-white bg-green-600 rounded-md">
                    See All Notifications
                </a>
            </div>
        </div>
    </div>
</nav>
<script src="{{ asset('js/retailernotif-utils.js') }}"></script>
<script src="{{ asset('js/message-utils.js') }}"></script>
<script>
    window.userId = {{ Auth::id() }};
    window.pusherAppKey = "{{ env('PUSHER_APP_KEY') }}";
    window.pusherAppCluster = "{{ env('PUSHER_APP_CLUSTER') }}";

    // Global tracking variable for active dropdown
    let activeDropdown = null;

    // Close any open dropdown
    function closeAllDropdowns() {
        // Close notifications dropdown
        const notificationsPopup = document.getElementById('notificationsPopup');
        if (notificationsPopup) {
            notificationsPopup.classList.add('hidden');
        }

        // Close messages dropdown
        const messagesDropdown = document.getElementById('messagesDropdown');
        if (messagesDropdown) {
            messagesDropdown.classList.add('hidden');
        }

        // Close profile dropdown
        const profileDropdown = document.querySelector('.dropdown-menu');
        if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
            profileDropdown.classList.add('hidden');
        }

        // Hide any backdrops
        const backdrop = document.getElementById('backdrop');
        if (backdrop) {
            backdrop.classList.add('hidden');
        }

        // Reset tracking variable
        activeDropdown = null;
    }

    // Replace toggleNotifications function:
    function toggleNotifications() {
        const isMobile = window.innerWidth < 640; // sm breakpoint is 640px

        if (isMobile) {
            // Mobile: Show full-page overlay
            document.getElementById('mobile-notifications-overlay').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Load notifications into mobile container
            const container = document.getElementById('mobile-notifications-container');
            container.innerHTML = '<div class="p-3 text-center">Loading notifications...</div>';

            fetch('/retailers/notifications/latest')
                .then(response => response.json())
                .then(data => {
                    renderNotifications(data, container, true);
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    container.innerHTML =
                        '<div class="p-3 text-center text-red-500">Failed to load notifications</div>';
                });
        } else {
            // Desktop: Show dropdown
            const popup = document.getElementById('notificationsPopup');

            // If notifications are already open, just close them
            if (activeDropdown === 'notifications') {
                popup.classList.add('hidden');
                activeDropdown = null;
                return;
            }

            // Close any open dropdowns
            closeAllDropdowns();

            // Show notifications
            popup.classList.remove('hidden');
            activeDropdown = 'notifications';

            // Load content
            fetchNotificationsPreview();
        }
    }

    // Replace toggleMessagesDropdown function:
    function toggleMessagesDropdown() {
        const isMobile = window.innerWidth < 640; // sm breakpoint is 640px

        if (isMobile) {
            // Mobile: Show full-page overlay
            document.getElementById('mobile-message-overlay').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Load messages into mobile container
            const container = document.getElementById('mobile-messages-container');
            container.innerHTML = '<div class="p-3 text-center">Loading messages...</div>';

            fetch('/retailers/messages/preview')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    renderMessages(data, container, true);
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    container.innerHTML = '<div class="p-3 text-center text-red-500">Failed to load messages</div>';
                });
        } else {
            // Desktop: Show dropdown
            const dropdown = document.getElementById('messagesDropdown');

            // If messages are already open, just close them
            if (activeDropdown === 'messages') {
                dropdown.classList.add('hidden');
                activeDropdown = null;
                return;
            }

            // Close any open dropdowns
            closeAllDropdowns();

            // Show messages
            dropdown.classList.remove('hidden');
            activeDropdown = 'messages';

            // Load content
            fetchMessagePreviews();
        }
    }

    // Add these new functions:
    function closeMobileMessageOverlay() {
        document.getElementById('mobile-message-overlay').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function closeMobileNotificationOverlay() {
        document.getElementById('mobile-notifications-overlay').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Helper functions to render content
    function renderMessages(data, container, isMobile) {
        if (!container) return;

        if (data.success) {
            if (data.messages.length === 0) {
                container.innerHTML = '<div class="p-3 text-center text-gray-500">No messages yet</div>';
                return;
            }

            let html = '';
            data.messages.forEach(message => {
                const messageDate = new Date(message.created_at);
                const timeAgo = formatTimeAgo(messageDate);
                const unreadClass = message.is_read ? '' : 'font-semibold bg-blue-50';

                html += `
            <a href="/retailers/messages?distributor=${message.sender_id}" class="block hover:bg-gray-50 ${unreadClass}">
                <div class="flex items-start p-3">
                    <img src="${message.sender_image || '/img/default-profile.png'}" alt="${message.sender_name}" 
                        class="flex-shrink-0 object-cover w-10 h-10 mr-3 border rounded-full">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            ${message.sender_name}
                        </p>
                        <p class="text-sm text-gray-500 truncate">
                            ${message.message}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">
                            ${timeAgo}
                        </p>
                    </div>
                    ${!message.is_read ? '<div class="flex-shrink-0 w-2 h-2 ml-2 bg-blue-600 rounded-full"></div>' : ''}
                </div>
            </a>`;
            });

            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="p-3 text-center text-gray-500">Error loading messages</div>';
        }
    }

    function renderNotifications(data, container, isMobile) {
        if (!container) return;

        if (data.success) {
            if (data.notifications.length === 0) {
                container.innerHTML = '<div class="p-3 text-center text-gray-500">No notifications yet</div>';
                return;
            }

            let html = '';
            data.notifications.forEach(notification => {
                const notificationDate = new Date(notification.created_at);
                const timeAgo = formatTimeAgo(notificationDate);
                const isUnread = !notification.read_at;
                const bgClass = isUnread ? 'bg-blue-50' : '';

                html += `
            <div class="p-3 ${bgClass} border-b border-gray-200">
                <div class="flex items-start">
                    ${isUnread ? '<div class="flex-shrink-0 mr-2"><span class="block w-2 h-2 bg-blue-600 rounded-full"></span></div>' : ''}
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">${notification.data.title || 'Notification'}</p>
                        <p class="mt-1 text-xs text-gray-500">${notification.data.message || ''}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-400">${timeAgo}</span>
                            ${isUnread ? `<button onclick="markAsRead(${notification.id}, event)" class="text-xs text-blue-600 hover:text-blue-800">Mark as read</button>` : ''}
                        </div>
                    </div>
                </div>
            </div>`;
            });

            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="p-3 text-center text-gray-500">Error loading notifications</div>';
        }
    }

    function fetchNotificationsPreview() {
        const container = document.getElementById('notifications-preview-container');
        if (!container) return;

        container.innerHTML = '<div class="p-3 text-sm text-center text-gray-500">Loading notifications...</div>';

        fetch('/retailers/notifications/latest')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                renderNotifications(data, container, false);
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                container.innerHTML =
                    '<div class="p-3 text-sm text-center text-gray-500">Error loading notifications</div>';
            });
    }

    // Simplified fetch function
    function fetchMessagePreviews() {
        const container = document.getElementById('messages-preview-container');
        if (!container) return;

        container.innerHTML = '<div class="p-3 text-sm text-center text-gray-500">Loading messages...</div>';

        fetch('/retailers/messages/preview')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                renderMessages(data, container, false);
            })
            .catch(error => {
                console.error('Error fetching message previews:', error);
                container.innerHTML =
                    '<div class="p-3 text-sm text-center text-gray-500">Error loading messages</div>';
            });
    }



    // Handle notification modal
    function openNotificationModal() {
        document.getElementById('allNotificationsModal').classList.remove('hidden');
        document.getElementById('notificationsPopup').classList.add('hidden');
    }

    function closeNotificationModal() {
        document.getElementById('allNotificationsModal').classList.add('hidden');
    }

    // Mobile menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Setup mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileBackdrop = document.getElementById('mobile-backdrop');
        const burgerIcon = document.getElementById('burger-icon');
        const closeIcon = document.getElementById('close-icon');

        if (mobileMenuButton && mobileMenu) {
            function toggleMobileMenu() {
                mobileMenu.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');

                // Toggle icons
                if (burgerIcon && closeIcon) {
                    burgerIcon.classList.toggle('hidden');
                    closeIcon.classList.toggle('hidden');
                }

                // Animate the panel
                const panel = mobileMenu.querySelector('div:not(#mobile-backdrop)');
                if (panel) {
                    if (mobileMenu.classList.contains('hidden')) {
                        panel.classList.add('translate-x-full');
                    } else {
                        panel.classList.remove('translate-x-full');
                    }
                }
            }

            mobileMenuButton.addEventListener('click', toggleMobileMenu);

            if (mobileBackdrop) {
                mobileBackdrop.addEventListener('click', toggleMobileMenu);
            }

            // Close mobile menu when changing routes
            const mobileMenuLinks = mobileMenu.querySelectorAll('a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (!mobileMenu.classList.contains('hidden')) {
                        toggleMobileMenu();
                    }
                });
            });
        }

        // Set up profile dropdown click handler
        const profileButton = document.querySelector('.relative.hidden.ml-4.sm\\:block button');
        if (profileButton) {
            const originalClick = profileButton.onclick;

            profileButton.addEventListener('click', function(e) {
                // Don't close if we're clicking inside the dropdown
                if (e.target.closest('.dropdown-menu')) {
                    return;
                }

                // Close other dropdowns
                const notificationsPopup = document.getElementById('notificationsPopup');
                if (notificationsPopup && !notificationsPopup.classList.contains('hidden')) {
                    notificationsPopup.classList.add('hidden');
                }

                const messagesDropdown = document.getElementById('messagesDropdown');
                if (messagesDropdown && !messagesDropdown.classList.contains('hidden')) {
                    messagesDropdown.classList.add('hidden');
                }

                // Let the original handler run to toggle profile dropdown
                if (originalClick) {
                    originalClick.call(this, e);
                }
            });
        }

        // Add click handler for notification modal
        const notificationModal = document.getElementById('allNotificationsModal');
        if (notificationModal) {
            notificationModal.addEventListener('click', function(event) {
                if (event.target === this) {
                    closeNotificationModal();
                }
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        // Don't close if we're clicking a toggle button
        if (event.target.closest('button[onclick^="toggle"]')) {
            return;
        }

        // Don't close if clicking inside an open dropdown
        if (
            activeDropdown === 'notifications' && event.target.closest('#notificationsPopup') ||
            activeDropdown === 'messages' && event.target.closest('#messagesDropdown')
        ) {
            return;
        }

        // Otherwise close all dropdowns
        closeAllDropdowns();
    });

    // Close dropdowns on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAllDropdowns();
            closeNotificationModal();
        }
    });

    function fetchMessagePreviews() {
        const container = document.getElementById('messages-preview-container');
        if (!container) return;

        // Show loading state
        container.innerHTML = '<div class="p-3 text-sm text-center text-gray-500">Loading messages...</div>';

        // Determine if we're in mobile view
        const isMobile = window.innerWidth < 640; // sm breakpoint is 640px

        fetch('/retailers/messages/preview')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.messages.length === 0) {
                        container.innerHTML =
                            '<div class="p-3 text-sm text-center text-gray-500">No messages yet</div>';
                        return;
                    }

                    let html = '';
                    data.messages.forEach(message => {
                        const messageDate = new Date(message.created_at);
                        const timeAgo = formatTimeAgo(messageDate);
                        const unreadClass = message.is_read ? '' : 'font-semibold bg-blue-50';

                        if (isMobile) {
                            // Full width style for mobile
                            html += `
                        <a href="/retailers/messages?distributor=${message.sender_id}" class="block hover:bg-gray-50 ${unreadClass}">
                            <div class="flex items-start w-full p-3">
                                <img src="${message.sender_image || '/img/default-profile.png'}" alt="${message.sender_name}" 
                                    class="flex-shrink-0 object-cover w-10 h-10 mr-3 border rounded-full">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        ${message.sender_name}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        ${message.message}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        ${timeAgo}
                                    </p>
                                </div>
                                ${!message.is_read ? '<div class="flex-shrink-0 w-2 h-2 ml-2 bg-blue-600 rounded-full"></div>' : ''}
                            </div>
                        </a>`;
                        } else {
                            // Desktop style (like it was before)
                            html += `
                        <a href="/retailers/messages?distributor=${message.sender_id}" class="block hover:bg-gray-50 ${unreadClass}">
                            <div class="flex items-center p-3">
                                <img src="${message.sender_image || '/img/default-profile.png'}" alt="${message.sender_name}" 
                                    class="flex-shrink-0 object-cover w-10 h-10 mr-3 border rounded-full">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        ${message.sender_name}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        ${message.message}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        ${timeAgo}
                                    </p>
                                </div>
                                ${!message.is_read ? '<div class="flex-shrink-0 w-2 h-2 ml-2 bg-blue-600 rounded-full"></div>' : ''}
                            </div>
                        </a>`;
                        }
                    });

                    container.innerHTML = html;
                } else {
                    container.innerHTML =
                        '<div class="p-3 text-sm text-center text-gray-500">Error loading messages</div>';
                }
            })
            .catch(error => {
                console.error('Error fetching message previews:', error);
                container.innerHTML =
                    '<div class="p-3 text-sm text-center text-gray-500">Error loading messages</div>';
            });
    }

    // Helper function for time formatting
    function formatTimeAgo(date) {
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
        const diffMinutes = Math.floor(diffTime / (1000 * 60));

        if (diffDays > 0) {
            return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        } else if (diffHours > 0) {
            return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        } else if (diffMinutes > 0) {
            return `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''} ago`;
        } else {
            return 'Just now';
        }
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 640 && activeDropdown) { // sm breakpoint
            // Reset body overflow when resizing to desktop with open dropdown
            document.body.classList.remove('overflow-hidden');

            // If dropdown is open, refresh its content for the new layout
            if (activeDropdown === 'messages') {
                fetchMessagePreviews();
            } else if (activeDropdown === 'notifications') {
                fetchNotificationsPreview();
            }
        }
    });
</script>
