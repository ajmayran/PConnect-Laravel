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
                            <path id="burger-icon" class="block" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center">
                <!-- Cart -->
                <div class="relative">
                    <a href="{{ route('retailers.cart.index') }}"
                        class="p-2 text-gray-500 hover:text-gray-700 hover:cursor-pointer">
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
                        class="absolute right-0 z-[100] hidden mt-2 bg-white rounded-lg shadow-xl w-80">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-800">Notifications</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div class="p-4">
                                <p class="text-sm">Your order #1234 has been shipped!</p>
                                <p class="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="p-4 text-center border-t border-gray-200">
                            <button onclick="openNotificationModal()"
                                class="text-sm text-green-600 hover:text-green-700">See All Notifications</button>
                        </div>
                    </div>
                </div>

                <!-- Add the modal for all notifications -->
                <div id="allNotificationsModal" class="fixed inset-0 z-[101] hidden overflow-y-auto">
                    <div
                        class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true"
                            onclick="closeNotificationModal()">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>

                        <!-- Modal panel -->
                        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                            onclick="event.stopPropagation()">
                            <div class="absolute top-0 right-0 pt-4 pr-4">
                                <button onclick="closeNotificationModal()" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Close</span>
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="sm:flex sm:items-start">
                                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">All Notifications</h3>
                                    <div class="mt-4 divide-y divide-gray-200 max-h-[60vh] overflow-y-auto">
                                        <!-- Notification Items -->
                                        @for ($i = 1; $i <= 3; $i++)
                                            <div class="py-4">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0 pt-0.5">
                                                        <svg class="w-5 h-5 text-green-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 w-0 ml-3">
                                                        <p class="text-sm font-medium text-gray-900">Order
                                                            #{{ 1234 + $i }} Update</p>
                                                        <p class="mt-1 text-sm text-gray-500">Your order has been
                                                            shipped!</p>
                                                        <p class="mt-1 text-xs text-gray-400">2 hours ago</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
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

                    function openNotificationModal() {
                        document.getElementById('allNotificationsModal').classList.remove('hidden');
                        document.getElementById('notificationsPopup').classList.add('hidden');
                    }

                    function closeNotificationModal() {
                        document.getElementById('allNotificationsModal').classList.add('hidden');
                    }

                    // Update the existing click event listener
                    document.addEventListener('click', function(event) {
                        if (!event.target.closest('#notificationsPopup') &&
                            !event.target.closest('#allNotificationsModal') &&
                            !event.target.closest('button')) {
                            closeAll();
                            closeNotificationModal();
                        }
                    });

                    // Add escape key listener for modal
                    document.addEventListener('keydown', function(event) {
                        if (event.key === 'Escape') {
                            closeNotificationModal();
                        }
                    });

                    // Close modal when clicking outside
                    document.getElementById('allNotificationsModal').addEventListener('click', function(event) {
                        if (event.target === this) {
                            closeNotificationModal();
                        }
                    });

                    // Close on escape key
                    document.addEventListener('keydown', function(event) {
                        if (event.key === 'Escape') {
                            closeNotificationModal();
                        }
                    });
                </script>

                <!-- Profile Section with Dropdown - Hidden on mobile -->
                <div class="relative ml-4 hidden sm:block">
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

                            <x-dropdown-link :href="route('retailers.tickets.create')">
                                {{ __('Create Ticket') }}
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
        <div class="absolute right-0 top-0 h-full w-3/4 max-w-xs bg-white shadow-xl transform transition-transform duration-300 ease-in-out">
            <!-- User Profile Section -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <img class="object-cover w-12 h-12 border-2 border-green-500 rounded-full"
                         src="{{ Auth::user()->retailerProfile && Auth::user()->retailerProfile->profile_picture ? asset('storage/' . Auth::user()->retailerProfile->profile_picture) : asset('img/default-profile.png') }}"
                         alt="Profile">
                    <div class="ml-3">
                        <p class="text-base font-semibold text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                        <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <a href="{{ route('retailers.profile.edit') }}" class="block mt-3 py-2 px-4 text-center text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                    View Profile
                </a>
            </div>
            
            <!-- Navigation Links -->
            <div class="py-2 px-4">
                <a href="{{ route('retailers.dashboard') }}" 
                   class="flex items-center py-3 border-b border-gray-200 {{ request()->routeIs('retailers.dashboard') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    HOME
                </a>
                <a href="{{ route('retailers.all-distributor') }}"
                   class="flex items-center py-3 border-b border-gray-200 {{ request()->routeIs('retailers.all-distributor') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    DISTRIBUTORS
                </a>
                <a href="{{ route('retailers.all-product') }}"
                   class="flex items-center py-3 border-b border-gray-200 {{ request()->routeIs('retailers.all-product') ? 'text-green-600 font-semibold' : 'text-gray-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    PRODUCTS
                </a>
            </div>
            
            <!-- Footer Actions -->
            <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-4 text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Bar - Hidden on mobile -->
    <div class="bg-gray-800 hidden sm:block">
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

</nav>

<script>
    // Mobile menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileBackdrop = document.getElementById('mobile-backdrop');
        const burgerIcon = document.getElementById('burger-icon');
        const closeIcon = document.getElementById('close-icon');
        
        function toggleMobileMenu() {
            mobileMenu.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
            
            // Toggle icons
            burgerIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
            
            // Animate the panel
            const panel = mobileMenu.querySelector('div:not(#mobile-backdrop)');
            if (mobileMenu.classList.contains('hidden')) {
                panel.classList.add('translate-x-full');
            } else {
                panel.classList.remove('translate-x-full');
            }
        }
        
        mobileMenuButton.addEventListener('click', toggleMobileMenu);
        mobileBackdrop.addEventListener('click', toggleMobileMenu);
        
        // Close mobile menu when changing routes
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (!mobileMenu.classList.contains('hidden')) {
                    toggleMobileMenu();
                }
            });
        });
        
        // Add this to prevent conflicts with search overlay in dashboard
        if (typeof toggleOverlay === 'function') {
            const originalToggleOverlay = toggleOverlay;
            toggleOverlay = function(show) {
                if (mobileMenu.classList.contains('hidden')) {
                    originalToggleOverlay(show);
                }
            };
        }
    });
    
    // Existing notification scripts
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

    function openNotificationModal() {
        document.getElementById('allNotificationsModal').classList.remove('hidden');
        document.getElementById('notificationsPopup').classList.add('hidden');
    }

    function closeNotificationModal() {
        document.getElementById('allNotificationsModal').classList.add('hidden');
    }

    // Update the existing click event listener
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#notificationsPopup') &&
            !event.target.closest('#allNotificationsModal') &&
            !event.target.closest('button')) {
            closeAll();
            closeNotificationModal();
        }
    });

    // Add escape key listener for modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeNotificationModal();
        }
    });

    // Close modal when clicking outside
    document.getElementById('allNotificationsModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeNotificationModal();
        }
    });

    // Close on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeNotificationModal();
        }
    });
</script>
