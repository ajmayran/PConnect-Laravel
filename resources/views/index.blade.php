<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>PConnect</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    <style>
        .circle-button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: gray;
            cursor: pointer;
        }

        .circle-button.active {
            background-color: white;
        }

        .tab-item.active {
            border-bottom: 2px solid rgb(38, 113, 38);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/index.js'])
</head>

<body class="bg-gray-100">
    <!-- Top Navigation - Hidden on mobile -->
    <nav
        class="flex-col items-center justify-between hidden w-full px-4 py-2 bg-white shadow-sm sm:flex min-h-20 md:px-20 md:py-4">
        <div class="flex flex-wrap items-center justify-between w-full">
            <!-- Left Links (Social, etc) -->
            <div class="flex flex-wrap items-center mb-2 sm:mb-0">
                <a href="./user/distributor/dist_login.php"
                    class="pb-1 font-sans text-sm text-gray-800 hover:text-green-500">Distributor Centre</a><span
                    class="px-2 font-light text-gray-500 opacity-50 md:px-4">|</span>
                <a href="./user/distributor/dist_registration.php"
                    class="pb-1 font-sans text-sm text-gray-800 hover:text-green-500">Register Now</a><span
                    class="px-2 font-light text-gray-500 opacity-50 md:px-4">|</span>
                <span class="pb-1 font-sans text-sm text-gray-800">Follow us on</span>
                <a href="https://www.facebook.com/profile.php?id=61567370446187"
                    class="pb-1 ml-2 text-blue-500 hover:text-gray-500"><svg xmlns="http://www.w3.org/2000/svg"
                        class="text-xl" width="1em" height="1em" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M20.9 2H3.1A1.1 1.1 0 0 0 2 3.1v17.8A1.1 1.1 0 0 0 3.1 22h9.58v-7.75h-2.6v-3h2.6V9a3.64 3.64 0 0 1 3.88-4a20 20 0 0 1 2.33.12v2.7H17.3c-1.26 0-1.5.6-1.5 1.47v1.93h3l-.39 3H15.8V22h5.1a1.1 1.1 0 0 0 1.1-1.1V3.1A1.1 1.1 0 0 0 20.9 2" />
                    </svg></a>
                <a href="#" class="pb-1 mt-2 ml-1 text-gray-800 align-middle hover:text-green-500"><iconify-icon
                        icon="mdi:instagram" class="text-xl"></iconify-icon></a>
            </div>

            <!-- Right Links (Help, Login, etc) -->
            <div class="flex flex-wrap items-center">
                <div class="flex items-center">
                    <a href="./support-index.php"
                        class="font-sans text-sm text-gray-800 hover:text-green-500"><iconify-icon
                            icon="material-symbols:help"
                            class="pb-1 text-lg text-green-500 align-middle"></iconify-icon>Help</a><span
                        class="px-2 font-light text-gray-500 opacity-50 md:px-4">|</span>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('login') }}"
                        class="font-sans text-sm text-gray-800 hover:text-green-500"><iconify-icon
                            icon="mdi:notifications"
                            class="pb-1 text-lg text-green-500 align-middle"></iconify-icon>Notifications</a><span
                        class="px-2 font-light text-gray-500 opacity-50 md:px-4">|</span>
                </div>
                <a href="#" id="signUpModalBtn"
                    class="pb-1 font-sans text-sm text-gray-800 hover:text-green-500">Signup</a><span
                    class="px-2 font-light text-gray-500 opacity-50 md:px-4">|</span>
                <a href="{{ route('login') }}"
                    class="pb-1 font-sans text-sm text-gray-800 hover:text-green-500">Login</a>
            </div>
        </div>

        <!-- Search Bar & Logo -->
        <div class="relative flex flex-wrap items-center w-full px-2 mt-4 md:px-10">
            <div class="flex items-center mr-4 md:mr-10">
                <img src="{{ asset('img/Pconnect Logo.png') }}" alt="PC Connect Logo" class="h-8 mr-2 md:h-10">
                <span class="text-xl font-semibold text-black-700 md:text-2xl">PConnect</span>
            </div>
            <div class="flex flex-1 mt-2 md:mt-0">
                <input type="text" placeholder="Search for items..."
                    class="flex-1 px-3 py-2 bg-gray-200 border border-gray-300 rounded-tl-lg rounded-bl-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                <button
                    class="px-3 py-2 font-bold text-white bg-green-500 rounded-tr-lg rounded-br-lg hover:bg-green-600 md:px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="pb-1 text-xl align-middle md:text-2xl" width="1.1em"
                        height="1.1em" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5A6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5S14 7.01 14 9.5S11.99 14 9.5 14" />
                    </svg>
                </button>
            </div>
            <a href="{{ route('login') }}" class="py-2 ml-4 align-middle md:ml-10">
                <iconify-icon icon="mdi:cart"
                    class="text-2xl text-green-500 hover:text-green-600 md:text-3xl"></iconify-icon>
            </a>
        </div>
    </nav>

    <!-- Mobile Header -->
    <div class="flex flex-col sm:hidden">
        <!-- Top Mobile Header -->
        <div class="flex items-center justify-between px-4 py-2 bg-green-500">
            <div class="flex items-center">
                <img src="{{ asset('img/Pconnect Logo.png') }}" alt="PC Connect Logo" class="h-8 mr-2">
                <span class="text-xl font-semibold text-white">PConnect</span>
            </div>
            <div class="flex items-center">
                <!-- Added Login Link -->
                <a href="{{ route('login') }}" class="mr-3 text-white hover:text-green-200">
                    <span class="text-sm font-medium">Login</span>
                </a>
                <!-- Changed to Signup text instead of just icon -->
                <a href="#" id="mobileSignupBtn" class="mr-4 text-white hover:text-green-200">
                    <span class="text-sm font-medium">Signup</span>
                </a>
                <a href="{{ route('login') }}">
                    <iconify-icon icon="mdi:cart" class="text-2xl text-white hover:text-green-200"></iconify-icon>
                </a>
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="flex px-4 py-2 bg-green-500">
            <input type="text" placeholder="Search for items..."
                class="flex-1 px-3 py-2 bg-white border-0 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-green-400">
            <button class="px-3 py-2 font-bold text-white bg-green-600 rounded-r-lg">
                <iconify-icon icon="mdi:magnify" class="text-xl"></iconify-icon>
            </button>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="flex items-center justify-between bg-white shadow-sm">
        <div class="w-full px-4 py-2 text-sm text-white bg-gray-900 md:text-base md:px-20">
            <ul class="flex justify-center space-x-4 overflow-x-auto md:space-x-20">
                <li class="whitespace-nowrap hover:text-green-500"><a href="./index.php">HOME</a></li>
                <li class="whitespace-nowrap hover:text-green-500"><a href="{{ route('login') }}">DISTRIBUTORS</a>
                </li>
                <li class="whitespace-nowrap hover:text-green-500"><a href="{{ route('login') }}">PRODUCTS</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero/Slider Section -->
    <section>
        <div class="relative">
            <!-- Design 1 -->
            <div class="relative flex items-center justify-center w-full h-[60vh] section active">
                <img src="{{ asset('img/hero1.png') }}" class="absolute inset-0 object-cover w-full h-full"
                    alt="Promotional Image">
            </div>

            <!-- Design 2 -->
            <div class="relative flex items-center justify-center w-full h-[60vh] section hidden bg-green-500">
                <img src="{{ asset('img/hero3.png') }}" class="absolute inset-0 object-cover w-full h-full"
                    alt="Promotional Image">
            </div>

            <!-- Design 3 -->
            <div class="relative flex items-center justify-center w-full h-[60vh] section hidden bg-red-500">
                <img src="{{ asset('img/hero2.png') }}" class="absolute inset-0 object-cover w-full h-full"
                    alt="Promotional Image">
            </div>

            <!-- Design 4 -->
            <div class="relative flex items-center justify-center w-full h-[60vh] section hidden bg-red-500">
                <img src="{{ asset('img/hero4.png') }}" class="absolute inset-0 object-cover w-full h-full"
                    alt="Promotional Image">
            </div>

            <!-- Navigation Circles -->
            <div class="absolute flex space-x-2 transform -translate-x-1/2 bottom-4 left-1/2">
                <button class="circle-button active" data-index="0"></button>
                <button class="circle-button" data-index="1"></button>
                <button class="circle-button" data-index="2"></button>
                <button class="circle-button" data-index="3"></button>
            </div>
        </div>
    </section>

    <!-- Distributors Section -->
    <section class="py-6 bg-gray-200 md:py-8">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold md:text-2xl">Explore Distributors</h2>
                <div class="items-center hidden md:flex">
                    <i class="mr-2 fa-solid fa-angle-left"></i>
                    <button
                        class="px-3 py-1 mr-2 text-sm font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300 md:px-4 md:py-2">
                        All
                    </button>
                    <button
                        class="px-3 py-1 mr-2 text-sm font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300 md:px-4 md:py-2">
                        Ready To Cook
                    </button>
                    <button
                        class="px-3 py-1 mr-2 text-sm font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300 md:px-4 md:py-2">
                        Frozen Products
                    </button>
                    <button
                        class="px-3 py-1 mr-2 text-sm font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300 md:px-4 md:py-2">
                        Beverages
                    </button>
                    <button
                        class="px-3 py-1 text-sm font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300 md:px-4 md:py-2">
                        Dairy Products
                    </button>
                    <button
                        class="px-3 py-1 text-sm font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300 md:px-4 md:py-2">
                        Sauces & Condiments
                    </button>
                    <i class="ml-2 fa-solid fa-angle-right"></i>
                </div>
            </div>

            <!-- Mobile Category Selector -->
            <div class="flex pb-3 overflow-x-auto md:hidden">
                <button
                    class="px-3 py-1 mr-2 text-sm font-bold text-gray-700 bg-gray-200 rounded whitespace-nowrap">All</button>
                <button
                    class="px-3 py-1 mr-2 text-sm font-bold text-gray-700 bg-gray-200 rounded whitespace-nowrap">Ready
                    To Cook</button>
                <button
                    class="px-3 py-1 mr-2 text-sm font-bold text-gray-700 bg-gray-200 rounded whitespace-nowrap">Frozen
                    Products</button>
                <button
                    class="px-3 py-1 mr-2 text-sm font-bold text-gray-700 bg-gray-200 rounded whitespace-nowrap">Beverages</button>
                <button class="px-3 py-1 text-sm font-bold text-gray-700 bg-gray-200 rounded whitespace-nowrap">Dairy
                    Products</button>
                <button class="px-3 py-1 text-sm font-bold text-gray-700 bg-gray-200 rounded whitespace-nowrap">Sauces
                    & Condiments</button>
            </div>

            <div class="grid grid-cols-2 gap-2 py-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 md:gap-4 md:py-10">
                <a href="{{ route('login') }}" class="flex flex-col items-center p-3 bg-gray-100 rounded-lg md:p-6">
                    <img src="{{ asset('img/Distrubutors/alaska.png') }}" alt="Distributor 1"
                        class="w-3/4 mb-2 md:mb-4">
                    <h3 class="text-base font-bold text-center md:text-lg">Jacob Trading</h3>
                    <p class="text-sm text-center">10 Items</p>
                </a>
                <a href="{{ route('login') }}" class="flex flex-col items-center p-3 bg-gray-100 rounded-lg md:p-6">
                    <img src="{{ asset('img/Distrubutors/ph.png') }}" alt="Distributor 2"
                        class="w-3/4 mb-2 md:mb-4">
                    <h3 class="text-base font-bold text-center md:text-lg">Reachwell</h3>
                    <p class="text-sm text-center">20 Items</p>
                </a>
                <a href="./auth/login.php" class="flex flex-col items-center p-3 bg-gray-100 rounded-lg md:p-6">
                    <img src="{{ asset('img/Distrubutors/gm.png') }}" alt="Distributor 3"
                        class="w-3/4 mb-2 md:mb-4">
                    <h3 class="text-base font-bold text-center md:text-lg">Glenmark Trading</h3>
                    <p class="text-sm text-center">15 Items</p>
                </a>
                <a href="{{ route('login') }}" class="flex flex-col items-center p-3 bg-gray-100 rounded-lg md:p-6">
                    <img src="{{ asset('img/Distrubutors/bass.png') }}" alt="Distributor 4"
                        class="w-3/4 mb-2 md:mb-4">
                    <h3 class="text-base font-bold text-center md:text-lg">Boss Jim Grocery</h3>
                    <p class="text-sm text-center">10 Items</p>
                </a>
                <a href="{{ route('login') }}" class="flex flex-col items-center p-3 bg-gray-100 rounded-lg md:p-6">
                    <img src="{{ asset('img/Distrubutors/primus.png') }}" alt="Distributor 5"
                        class="w-3/4 mb-2 md:mb-4">
                    <h3 class="text-base font-bold text-center md:text-lg">Primus</h3>
                    <p class="text-sm text-center">22 Items</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5 bg-gray-200">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold md:text-2xl">Popular Products</h2>
            </div>
            <div class="grid grid-cols-2 gap-2 py-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 md:gap-4 md:py-10">
                <a href="{{ route('login') }}"
                    class="p-3 text-center bg-white border-2 border-gray-100 rounded-lg shadow-md product md:p-6">
                    <div class="flex justify-center">
                        <img src="{{ asset('img/Products/magnolia-c-tocino.png') }}" alt="Product Image"
                            class="w-24 h-16 mb-2 md:h-20 md:mb-4 md:w-30">
                    </div>
                    <div class="p-2 md:p-4">
                        <h3 class="text-base font-normal md:text-lg">Chicken Tocino Templados</h3>
                        <p class="text-xs text-gray-500 md:text-sm">By Boss jim grocery</p>
                    </div>
                    <div class="flex items-center justify-center mt-2 md:mt-4">
                        <span class="font-sans text-lg md:text-xl">₱230.00</span>
                    </div>
                </a>
                <a href="{{ route('login') }}"
                    class="p-3 text-center bg-white border-2 border-gray-100 rounded-lg shadow-md product md:p-6">
                    <div class="flex justify-center">
                        <img src="{{ asset('img/Products/magnolia-spicy-fc.png') }}" alt="Product Image"
                            class="w-16 h-16 mb-2 rounded-lg md:w-20 md:h-20 md:mb-4">
                    </div>
                    <div class="p-2 md:p-4">
                        <h3 class="text-base font-normal md:text-lg">Fried Chicken Spicy</h3>
                        <p class="text-xs text-gray-500 md:text-sm">By Boss jim grocery</p>
                    </div>
                    <div class="flex items-center justify-center mt-2 md:mt-4">
                        <span class="font-sans text-lg md:text-xl">₱270.00</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Registration Modal -->
    <div id="signUpModal"
        class="fixed inset-0 z-50 flex items-center justify-center transition-opacity duration-300 bg-black bg-opacity-50 opacity-0 pointer-events-none">
        <div id="modalContent"
            class="w-full max-w-sm p-6 mx-4 transition-all duration-300 transform scale-95 bg-white rounded-lg shadow-xl md:max-w-md">
            <h2 class="mb-4 text-xl font-bold text-center">Register As</h2>
            <div class="flex space-x-4">
                <a href="{{ route('register.retailer') }}"
                    class="w-1/2 px-4 py-2 text-center text-white transition-colors duration-500 bg-green-500 rounded hover:bg-green-700">
                    Retailer
                </a>
                <a href="{{ route('register.distributor') }}"
                    class="w-1/2 px-4 py-2 text-center text-white transition-colors duration-500 bg-blue-500 rounded hover:bg-blue-700">
                    Distributor
                </a>
            </div>
            <button id="closeSignUpModal" class="block mx-auto mt-4 text-red-500">
                Cancel
            </button>
        </div>
    </div>

    <!-- Include Footer -->
    @include('components.footer')

    <script>
        // Modal functionality
        const signUpModal = document.getElementById('signUpModal');
        const modalContent = document.getElementById('modalContent');
        const signUpModalBtn = document.getElementById('signUpModalBtn');
        const closeSignUpModal = document.getElementById('closeSignUpModal');
        const mobileSignupBtn = document.getElementById('mobileSignupBtn');

        // Function to show the modal with transition
        function showModal() {
            signUpModal.classList.remove('pointer-events-none');
            signUpModal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }

        // Function to hide the modal with transition
        function hideModal() {
            signUpModal.classList.add('opacity-0');
            modalContent.classList.add('scale-95');
            // Disable clicks after the transition ends
            setTimeout(() => {
                signUpModal.classList.add('pointer-events-none');
            }, 300);
        }

        // Desktop signup button
        if (signUpModalBtn) {
            signUpModalBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showModal();
            });
        }


        // Mobile signup button
        if (mobileSignupBtn) {
            mobileSignupBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showModal();
            });
        }

        // Close modal button
        if (closeSignUpModal) {
            closeSignUpModal.addEventListener('click', function() {
                hideModal();
            });
        }
    </script>
</body>

</html>
