<?php
$currentPage = basename($_SERVER['PHP_SELF']); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distributors Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/Pconnect Logo.png')}}">
    <script src="https://unpkg.com/iconify-icon/dist/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }

        .scroll-hide::-webkit-scrollbar {
            display: none;
        }

        .sidebar-menu .group.active a {
            background-color: #27AE60;
            color: white;
            border-radius: 5px;
        }

        .sidebar-menu .group.active .icon {
            color: white;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    @include('components.header')
    <div class="flex">
        @include('components.dist_sidebar')
        
        <main class="w-3/4 p-8 overflow-y-auto" style="max-height:100vh;">
            <h1 class="mb-4 text-2xl font-semibold">Overview</h1>

            <!-- Overview Section -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <!-- Product Sales -->
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="healthicons:money-bag" class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Product Sales</h3>
                    <canvas id="productSalesChart" class="mt-4"></canvas>
                </div>

                <!-- Add to Cart -->
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="mdi:cart" class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Add to Cart</h3>
                    <canvas id="addToCartChart" class="mt-4"></canvas>
                </div>

                <!-- Checkout -->
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="material-symbols:shopping-cart-checkout" class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Checkout</h3>
                    <canvas id="checkoutChart" class="mt-4"></canvas>
                </div>
            </div>


            <div class="grid grid-cols-1 gap-4 mt-6 lg:grid-cols-3">
                <!-- Most Selling Products -->
                <div class="col-span-1 p-4 bg-white rounded-lg shadow-md lg:col-span-2">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="mdi:fire" class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Most Selling Products</h3>
                    <table class="w-full mt-4 text-sm table-auto">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 text-left">Product</th>
                                <th class="py-2 text-left"></th>
                                <th class="py-2 text-left">Category</th>
                                <th class="py-2 text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="py-2"><img src="../../resources/img/Products/rtc-fried-chicken.png" alt="" class="w-16 h-16 rounded"></td>
                                <td class="py-2 text-[12px] font-light">Magnolia Ready to Cook Fried Chicken</td>
                                <td class="py-2 text-[12px] font-light">Frozen Products</td>
                                <td class="py-2 text-right text-[12px] font-light">₱200</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2"><img src="../../resources/img/Products/Chicken-lumpia-shanghai-mix.png" alt="" class="ml-1 rounded h-14 w-14"></td>
                                <td class="py-2 text-[12px] font-light">Magnolia Ready to Cook Chicken Lumpia Shanghai Mix</td>
                                <td class="py-2 text-[12px] font-light">Frozen Products</td>
                                <td class="py-2 text-right text-[12px] font-light">₱200</td>
                            </tr>
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>

                <!-- Chart Orders -->
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="lets-icons:order-fill" class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Orders</h3>
                    <canvas id="chartOrders"></canvas>
                </div>
            </div>

            <!-- Income Section -->
            <div class="grid grid-cols-1 gap-4 mt-6 lg:grid-cols-3">
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="tdesign:money" class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Income</h3>
                    <canvas id="incomeChart"></canvas>
                </div>

                <!-- Trending Products -->
                <div class="col-span-1 p-4 bg-white rounded-lg shadow-md lg:col-span-2">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="mdi:fire" class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Trending Products</h3>
                    <table class="w-full mt-4 text-sm table-auto">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">#</th>
                                <th class="py-2 text-left">Product</th>
                                <th class="py-2 text-left"></th>
                                <th class="py-2 text-left">Category</th>
                                <th class="py-2 text-right">Likes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="px-2 py-2">1</td>
                                <td class="py-2"><img src="../../resources/img/Products/rtc-chicken-siomai.png" alt="" class="w-16 h-16 rounded"></td>
                                <td class="py-2 text-[12px] font-light">Ready to Cook Chicken Siomai</td>
                                <td class="py-2 text-[12px] font-light">Frozen Products</td>
                                <td class="py-2 text-right text-[12px] font-light">314</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2">2</td>
                                <td class="py-2"><img src="../../resources/img/Products/rtc-chicken-lumpia.png" alt="" class="w-16 h-16 rounded"></td>
                                <td class="py-2 text-[12px] font-light">Ready to Cook Chicken Lumpia</td>
                                <td class="py-2 text-[12px] font-light">Frozen Products</td>
                                <td class="py-2 text-right text-[12px] font-light">290</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    @include('components.footer')
    <script>
        //Notif and account 
        document.getElementById('notificationButton').addEventListener('click', function() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('hidden');
        });

        document.getElementById('accountButton').addEventListener('click', function() {
            const popper = document.getElementById('accountPopper');
            popper.classList.toggle('hidden');
        });


        window.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const popper = document.getElementById('accountPopper');

            if (!event.target.closest('#notificationButton')) {
                dropdown.classList.add('hidden');
            }
            if (!event.target.closest('#accountButton')) {
                popper.classList.add('hidden');
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/tailwind/dist_dashboard.js') }}"></script>
</body>

</html>