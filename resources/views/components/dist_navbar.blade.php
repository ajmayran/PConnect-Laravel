<nav id="navbar" class="sticky top-0 w-full transition-transform duration-300 bg-white border-b border-gray-200">
    <!-- Your existing navbar HTML -->
    <div class="px-4 py-3 lg:px-6">
        <div class="flex items-center justify-between">
            <!-- Left side -->
            <div class="flex items-center">
                <!-- Sidebar Toggle -->
                <button type="button" onclick="toggleSidebar()"
                    class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 lg:hidden">
                    <i class="text-xl bi bi-filter-left"></i>
                </button>
            </div>

            <!-- Right side -->
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <div class="relative">
                    <button id="notification-btn" type="button"
                        class="relative p-2 text-gray-600 rounded-lg hover:bg-gray-100">
                        <div class="relative">
                            <i class="text-xl bi bi-bell"></i>
                            <span id="notification-badge"
                                class="absolute flex items-center justify-center hidden w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full -top-1 -right-1">
                            </span>
                        </div>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div id="notificationsPopup"
                        class="absolute z-[100] mt-2 origin-top-right bg-white rounded-lg shadow-xl w-80 border overflow-hidden max-h-[480px] right-0 hidden">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-800">Notifications</h3>
                            <button id="mark-all-notifications-read" class="text-xs text-blue-600 hover:text-blue-800">
                                Mark all as read
                            </button>
                        </div>
                        <div class="overflow-y-auto divide-y divide-gray-200 max-h-96"
                            id="notifications-preview-container">
                            <div class="p-3 text-sm text-center text-gray-500">
                                Loading notifications...
                            </div>
                        </div>
                        <div class="p-2 text-center border-t border-gray-200">
                            <a href="{{ route('distributors.notifications.index') }}"
                                class="text-sm text-blue-600 hover:text-blue-800">
                                View all notifications
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profile-btn"
                        class="flex items-center gap-2 p-2 text-gray-600 rounded-lg hover:bg-gray-100">
                        <img class="object-cover w-8 h-8 rounded-full"
                            src="{{ Auth::user()->distributor->company_profile_image ? asset('storage/' . Auth::user()->distributor->company_profile_image) : asset('img/default-distributor.jpg') }}"
                            alt="{{ Auth::user()->distributor->company_name }}"
                            onerror="this.src='{{ asset('img/default-distributor.jpg') }}'">
                        <span class="hidden text-sm font-medium lg:block">
                            {{ Auth::user()->distributor->company_name }}
                        </span>
                        <i class="text-sm bi bi-chevron-down"></i>
                    </button>

                    <!-- Dropdown menu -->
                    <div id="profile-dropdown"
                        class="absolute right-0 hidden w-48 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5"
                        style="z-index: 1500;>
                        <div class="py-1">
                            <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-200">
                                <p class="font-medium">{{ Auth::user()->distributor->company_name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('distributors.profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Company Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<script>
    let lastScrollY = window.scrollY;
    const navbar = document.getElementById('navbar');

    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        // Show/hide navbar based on scroll direction
        if (currentScrollY > lastScrollY && currentScrollY > 80) {
            // Scrolling down & past navbar height - hide
            navbar.style.transform = 'translateY(-100%)';
        } else {
            // Scrolling up - show
            navbar.style.transform = 'translateY(0)';
        }

        lastScrollY = currentScrollY;
    });

    // Initialize notification system
    document.addEventListener('DOMContentLoaded', function() {
        // Load initial notification count
        fetchUnreadNotificationCount();

        // Set up notification preview
        const notificationBtn = document.getElementById('notification-btn');
        const notificationsPopup = document.getElementById('notificationsPopup');

        if (notificationBtn) {
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent event bubbling
                notificationsPopup.classList.toggle('hidden');
                fetchNotificationsPreview();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationsPopup.contains(e.target) && e.target !== notificationBtn) {
                notificationsPopup.classList.add('hidden');
            }
        });

        // Setup Pusher for real-time notifications
        setupPusherNotifications();
    });

    const profileBtn = document.getElementById('profile-btn');
    const profileDropdown = document.getElementById('profile-dropdown'); // Changed to match HTML ID

    if (profileBtn) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });
    }

    // Close profile dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!profileDropdown.contains(e.target) && e.target !== profileBtn) {
            profileDropdown.classList.add('hidden');
        }
    });
</script>
