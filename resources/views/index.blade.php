<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <nav class="flex flex-col items-center justify-between w-full px-20 py-4 bg-white shadow-sm min-h-20">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center">
                <a href="./user/distributor/dist_login.php"
                    class="pb-1 font-sans text-sm text-gray-800 hover:text-green-500">Distributor Centre</a><span
                    class="px-4 font-light text-gray-500 opacity-50">|</span>
                <a href="./user/distributor/dist_registration.php"
                    class="pb-1 font-sans text-sm text-gray-800 hover:text-green-500">Register Now</a><span
                    class="px-4 font-light text-gray-500 opacity-50">|</span>
                <span class="pb-1 font-sans text-sm text-gray-800">Follow us on</span>
                <a href="https://www.facebook.com/profile.php?id=61567370446187"
                    class="pb-1 ml-2 text-blue-500 hover:text-gray-500"><svg xmlns="http://www.w3.org/2000/svg"
                        class="text-xl" width="1em" height="1em" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M20.9 2H3.1A1.1 1.1 0 0 0 2 3.1v17.8A1.1 1.1 0 0 0 3.1 22h9.58v-7.75h-2.6v-3h2.6V9a3.64 3.64 0 0 1 3.88-4a20 20 0 0 1 2.33.12v2.7H17.3c-1.26 0-1.5.6-1.5 1.47v1.93h3l-.39 3H15.8V22h5.1a1.1 1.1 0 0 0 1.1-1.1V3.1A1.1 1.1 0 0 0 20.9 2" />
                    </svg></a>
                <a href="#" class="pb-1 mt-2 ml-1 text-gray-800 align-middle hover:text-green-500"><iconify-icon
                        icon="mdi:instagram" class="text-xl"></iconify-icon> </a>
            </div>
            <div class="flex items-center">
                <div class="flex items-center">
                    <a href="./support-index.php"
                        class="font-sans text-sm text-gray-800 hover:text-green-500"><iconify-icon
                            icon="material-symbols:help"
                            class="pb-1 text-lg text-green-500 align-middle"></iconify-icon>Help</a><span
                        class="px-4 font-light text-gray-500 opacity-50">|</span>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('login') }}"
                        class="font-sans text-sm text-gray-800 hover:text-green-500"><iconify-icon
                            icon="mdi:notifications"
                            class="pb-1 text-lg text-green-500 align-middle"></iconify-icon>Notifications</a><span
                        class="px-4 font-light text-gray-500 opacity-50">|</span>
                </div>
                <a href="{{ route('register') }}"
                    class="pb-1 font-sans text-sm text-gray-800 hover:text-green-500">Signup</a><span
                    class="px-4 font-light text-gray-500 opacity-50">|</span>
                <a href="{{ route('login') }}"
                    class="pb-1 font-sans text-sm text-gray-800 hover:text-green-500">Login</a>
            </div>
        </div>
        <div class="relative flex items-center px-10 mt-4">
            <div class="flex items-center mr-10">
                <img src="{{ asset('img/Pconnect Logo.png') }}" alt="PC Connect Logo" class="h-10 mr-2">
                <span class="text-2xl font-semibold text-black-700">PConnect</span>
            </div>
            <select
                class="px-3 py-2 mr-2 bg-gray-200 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                <option value="all">All Categories</option>
            </select>
            <input type="text" placeholder="Search for items..."
                class="flex-1 px-3 py-2 bg-gray-200 border border-gray-300 rounded-tl-lg rounded-bl-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
            <button class="px-4 py-2 font-bold text-white bg-green-500 rounded-tr-lg rounded-br-lg hover:bg-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="pb-1 text-2xl align-middle" width="1.1em" height="1.1em" viewBox="0 0 24 24"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5A6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5S14 7.01 14 9.5S11.99 14 9.5 14"/></svg>
            </button>
            <a href="{{ route('login') }}" class="py-2 ml-10 align-middle">
                <iconify-icon icon="mdi:cart" class="text-3xl text-green-500 hover:text-green-600"></iconify-icon>
            </a>
        </div>
    </nav>
    <nav class="flex items-center justify-between bg-white shadow-sm">
        <div class="w-full px-20 py-2 text-white bg-gray-900">
            <ul class="flex justify-center space-x-20 ">
                <li class=" hover:text-green-00"><a href="./index.php">HOME</a></li>
                <li class=" hover:text-green-500"><a href="{{ route('login') }}">DISTRIBUTORS</a></li>
                <li class=" hover:text-green-500"><a href="{{ route('login') }}">PRODUCTS</a></li>
                <li class=" hover:text-green-500"><a href="{{ route('login') }}">CATEGORY</a></li>
            </ul>
        </div>
    </nav>
    <section>
        <div class="relative">
            <!-- Design 1 -->
            <div class="flex flex-row items-center justify-between w-full text-white section active"
                style="background-color: #D8F1E5;">
                <div class="relative flex flex-col items-center justify-center w-1/2">
                    <img src="{{ asset('img/sec1-des1.png') }}" class="object-cover w-[550px] h-[380px] p-2"
                        alt="Background Image">
                    <div class="absolute text-white -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                        <h1 class="mt-2 ml-2 text-3xl font-bold">Welcome to Our Store</h1>
                        <p class="mb-10 text-sm">Discover amazing products just for you!</p>
                        <a href="./auth/login.php"
                            class="px-6 py-3 text-black bg-white rounded-lg hover:bg-gray-200">Shop Now &rarr;</a>
                    </div>
                </div>
                <div class="w-1/2">
                    <img src="{{ asset('img/sec1-des2.png') }}" class="object-cover w-3/4 p-2 h-3/4"></img>
                </div>
            </div>

            <!-- Design 2 -->
            <div
                class="flex flex-col items-center justify-center hidden w-full min-h-[400px] text-white bg-green-500 section">
                <h1 class="text-5xl font-bold">Exclusive Offers</h1>
                <p class="text-lg">Don't miss out on our limited-time deals!</p>
                <a href="{{ route('login') }}" class="px-6 py-3 mt-2 text-black bg-white rounded-lg hover:bg-gray-200">Shop
                    Now &rarr;</a>
            </div>

            <!-- Design 3 -->
            <div
                class="flex flex-col items-center justify-center hidden w-full min-h-[400px] text-white bg-red-500 section">
                <h1 class="text-5xl font-bold">New Arrivals</h1>
                <p class="text-lg">Check out the latest products in our collection!</p>
                <a href="{{ route('login') }}"
                    class="px-6 py-3 mt-2 text-black bg-white rounded-lg hover:bg-gray-200">Shop Now &rarr;</a>
            </div>

            <!-- Design 4 -->
            <div
                class="flex flex-col items-center justify-center hidden w-full min-h-[400px] text-white bg-purple-500 section">
                <h1 class="text-5xl font-bold">Customer Favorites</h1>
                <p class="text-lg">See what our customers love the most!</p>
                <a href="{{ route('login') }}"
                    class="px-6 py-3 mt-2 text-black bg-white rounded-lg hover:bg-gray-200">Shop Now &rarr;</a>
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

    <section class="py-8 bg-white">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between">
                <h2 class="mr-4 text-2xl font-bold">Explore Distributors</h2>
                <div class="flex items-center">
                    <i class="mr-2 fa-solid fa-angle-left"></i>
                    <button
                        class="px-4 py-2 mr-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300">All</button>
                    <button
                        class="px-4 py-2 mr-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Drinks</button>
                    <button class="px-4 py-2 mr-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Frozen
                        Products</button>
                    <button
                        class="px-4 py-2 mr-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Instant
                        Drink</button>
                    <button
                        class="px-4 py-2 font-bold text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Toiletries</button>
                    <i class="ml-2 fa-solid fa-angle-right"></i>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 py-10 md:grid-cols-5">
                <a href="{{ route('login') }}" class="flex flex-col items-center p-6 bg-gray-100 rounded-lg">
                    <img src="{{ asset('img/Distrubutors/alaska.png') }}" alt="Distributor 1" class="mb-4">
                    <h3 class="text-lg font-bold">Jacob Trading</h3>
                    <p>10 Items</p>
                </a>
                <a href="{{ route('login') }}" class="flex flex-col items-center p-6 bg-gray-100 rounded-lg">
                    <img src="{{ asset('img/Distrubutors/ph.png') }}" alt="Distributor 2" class="mb-4">
                    <h3 class="text-lg font-bold">Reachwell</h3>
                    <p>20 Items</p>
                </a>
                <a href="./auth/login.php" class="flex flex-col items-center p-6 bg-gray-100 rounded-lg">
                    <img src="{{ asset('img/Distrubutors/gm.png') }}" alt="Distributor 3" class="mb-4">
                    <h3 class="text-lg font-bold">Glenmark Trading</h3>
                    <p>15 Items</p>
                </a>
                <a href="{{ route('login') }}" class="flex flex-col items-center p-6 bg-gray-100 rounded-lg">
                    <img src="{{ asset('img/Distrubutors/bass.png') }}" alt="Distributor 4" class="mb-4">
                    <h3 class="text-lg font-bold">Boss Jim Grocery </h3>
                    <p>10 Items</p>
                </a>
                <a href="{{ route('login') }}" class="flex flex-col items-center p-6 bg-gray-100 rounded-lg">
                    <img src="{{ asset('img/Distrubutors/primus.png') }}" alt="Distributor 5" class="mb-4">
                    <h3 class="text-lg font-bold">Primus</h3>
                    <p>22 Items</p>
                </a>
            </div>
        </div>
    </section>
    <section class="py-5 bg-white">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between">
                <h2 class="mr-4 text-2xl font-bold">Popular Products</h2>
            </div>
            <div class="grid grid-cols-2 gap-4 py-10 md:grid-cols-5">
                <a href="{{ route('login') }}"
                    class="p-6 text-center bg-white border-2 border-gray-100 rounded-lg shadow-md product">
                    <div class="flex justify-center">
                        <img src="{{ asset('img/Products/rtc-chicken-tocino.png') }}" alt="Product Image"
                            class="h-20 mb-4 w-30">
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-normal">Chicken Tocino Templados</h3>
                        <p class="text-sm text-gray-500">By Magnolia</p>
                    </div>
                    <div class="flex items-center justify-center mt-4">
                        <span class="font-sans text-xl">₱250.00</span>
                    </div>
                </a>
                <a href="{{ route('login') }}"
                    class="p-6 text-center bg-white border-2 border-gray-100 rounded-lg shadow-md product">
                    <div class="flex justify-center">
                        <img src="{{ asset('img/Products/Chicken-lumpia-shanghai-mix.png') }}" alt="Product Image"
                            class="w-20 h-20 mb-4 rounded-lg ">
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-normal">Chicken Lumpia Shanghai Mix</h3>
                        <p class="text-sm text-gray-500">By Magnolia</p>
                    </div>
                    <div class="flex items-center justify-center mt-4">
                        <span class="font-sans text-xl">₱250.00</span>
                    </div>
                </a>
            </div>
        </div>
    </section>
    <script src="{{ asset('js/tailwind/user_dash.js') }}"></script>
</body>
@include('components.footer')


</html>
