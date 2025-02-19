<nav id="navbar" class="sticky top-0 w-full transition-transform duration-300 bg-white border-b border-gray-200">

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
                <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100">
                    <i class="text-xl bi bi-bell"></i>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
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
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 z-50 w-48 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-200">
                                <p class="font-medium">{{ Auth::user()->distributor->company_name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ 'distributors.profile.edit' }}"
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
</script>
