<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distributors Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/Pconnect Logo.png') }}">
    <script src="https://unpkg.com/iconify-icon/dist/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" />
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }

        #main-content {
            transition: margin-left 0.3s ease-in-out;
            width: 100%;
        }

        #sidebar {
            transition: transform 0.3s ease-in-out;
        }

        @media (max-width: 1024px) {
            #sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
            }
        }

        .lg\:ml-0 {
            margin-left: 0 !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dist_dashboard.js'])
</head>

<body class="bg-gray-100">
    <!-- Sidebar Toggle Button (Mobile) -->
    <span class="absolute text-4xl text-white cursor-pointer top-5 left-4" onclick="toggleSidebar()">
        <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
    </span>

    <!-- Sidebar -->
    <div id="sidebar"
        class="fixed top-0 bottom-0 lg:left-0 p-2 w-[300px] overflow-y-auto text-center bg-gray-900 transition-transform duration-300 ease-in-out transform">
        <div class="text-xl text-gray-100">
            <div class="flex items-center px-1 py-2 mt-1">
                <img class="w-auto h-10" src="{{ asset('img/Pconnect Logo.png') }}" alt="PConnect">
                <h1 class="ml-3 font-bold text-gray-200">PConnect</h1>
                <i class="ml-auto text-2xl cursor-pointer bi bi-x" onclick="toggleSidebar()"></i>
            </div>

            <div class="my-2 bg-gray-600 h-[1px]"></div>

        </div>
        <div
            class="flex items-center px-4 py-1 mt-3 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <a href="{{ route('distributors.dashboard') }}" class="flex items-center">
                <iconify-icon icon="mdi:home" class="text-xl icon"></iconify-icon>
                <span class="ml-4 font-normal text-gray-200 ">Dashboard</span>
            </a>
        </div>
        <div
            class="flex items-center px-4 py-1 mt-3 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <a href="{{ route('distributors.orders.index') }}" class="flex items-center">
                <iconify-icon icon="material-symbols-light:sell" class="text-xl icon"></iconify-icon>
                <span class="ml-4 font-normal text-gray-200 ">My Orders</span>
            </a>
        </div>
        <div
            class="flex items-center px-4 py-1 mt-3 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <a href="{{ route('distributors.returns.index') }}" class="flex items-center">
                <iconify-icon icon="ph:key-return-fill" class="text-xl icon"></iconify-icon>
                <span class="ml-4 font-normal text-gray-200">Return | Refund</span>
            </a>
        </div>
        <div
            class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <a href="{{ route('distributors.cancellations.index') }}" class="flex items-center">
                <iconify-icon icon="basil:cancel-solid" class="text-xl icon"></iconify-icon>
                <span class="ml-4 font-normal text-gray-200 ">Cancellation</span>
            </a>
        </div>
        <div
            class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <a href="{{ route('distributors.delivery.index') }}" class="flex items-center">
                <iconify-icon icon="mdi:truck-delivery" class="text-xl icon"></iconify-icon>
                <span class="ml-4 font-normal text-gray-200 ">Delivery</span>
            </a>
        </div>

        <div class="my-2 bg-gray-600 h-[1px]"></div>

        <div
            class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <a href="{{ route('distributors.products.index') }}" class="flex items-center">
                <iconify-icon icon="dashicons:products" class="text-xl icon"></iconify-icon>
                <span class="ml-4 font-normal text-gray-200 ">My Products</span>
            </a>
        </div>
        <div
            class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <a href="{{ route('distributors.inventory.index') }}" class="flex items-center">
                <iconify-icon icon="ic:baseline-inventory-2" class="text-xl icon"></iconify-icon>
                <span class="ml-4 font-normal text-gray-200 ">Inventory</span>
            </a>
        </div>

        <div class="my-2 bg-gray-600 h-[1px]"></div>

        <div
            class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <a href="{{ route('distributors.messages.index') }}" class="flex items-center">
                <iconify-icon icon="ant-design:message-filled" class="text-xl icon"></iconify-icon>
                <span class="ml-4 font-normal text-gray-200 ">Messages</span>
            </a>
        </div>
        <div class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600"
            onclick="dropdown()">
            <iconify-icon icon="material-symbols:block" class="text-xl icon"></iconify-icon>
            <div class="flex items-center justify-between w-full">
                <span class="ml-4 font-normal text-gray-200">Blocking</span>
                <span class="text-sm rotate-180" id="arrow">
                    <i class="bi bi-chevron-down"></i>
                </span>
            </div>
        </div>
        <div class="w-4/5 mx-auto text-sm font-bold text-left text-gray-200" id="submenu">
            <h1 class="p-2 mt-1 rounded-md cursor-pointer hover:bg-green-600">
                Customers
            </h1>
            <h1 class="p-2 mt-1 rounded-md cursor-pointer hover:bg-green-600">
                Messages
            </h1>
        </div>

        <div class="my-2 bg-gray-600 h-[1px]"></div>

        <div
            class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <a href="{{ route('distributors.insights.index') }}" class="flex items-center">
                <iconify-icon icon="gg:insights" class="text-xl icon"></iconify-icon>
                <span class="ml-4 font-normal text-gray-200 ">Business Insights</span>
            </a>
        </div>

        <div
            class="flex items-center px-4 py-2 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span class="ml-4 font-normal text-gray-200">Logout</span>
                </button>
            </form>
        </div>
    </div>
    

    <!-- Main Content -->

    <div id="main-content" class="p-4 transition-all duration-300 ease-in-out lg:ml-[300px]">
        <div class="container mx-auto">
            <span class="absolute text-4xl text-white cursor-pointer top-5 left-4 lg:hidden"
                onclick="toggleSidebar()">
                <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
            </span>
            <h1 class="mb-4 text-2xl font-semibold text-right">Overview</h1>

            <!-- Overview Section -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <!-- Product Sales -->
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="healthicons:money-bag"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Product Sales</h3>
                    <canvas id="productSalesChart" class="mt-4"></canvas>
                </div>

                <!-- Add to Cart -->
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="mdi:cart"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Add to Cart</h3>
                    <canvas id="addToCartChart" class="mt-4"></canvas>
                </div>

                <!-- Checkout -->
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="material-symbols:shopping-cart-checkout"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Checkout</h3>
                    <canvas id="checkoutChart" class="mt-4"></canvas>
                </div>
            </div>


            <div class="grid grid-cols-1 gap-4 mt-6 lg:grid-cols-3">
                <!-- Most Selling Products -->
                <div class="col-span-1 p-4 bg-white rounded-lg shadow-md lg:col-span-2">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="mdi:fire"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Most Selling Products
                    </h3>
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
                                <td class="py-2"><img src="{{ asset('img/Products/rtc-fried-chicken.png') }}"
                                        alt="" class="w-16 h-16 rounded"></td>
                                <td class="py-2 text-[12px] font-light">Magnolia Ready to Cook Fried Chicken</td>
                                <td class="py-2 text-[12px] font-light">Frozen Products</td>
                                <td class="py-2 text-right text-[12px] font-light">₱200</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2"><img
                                        src="{{ asset('img/Products/Chicken-lumpia-shanghai-mix.png') }}"
                                        alt="" class="ml-1 rounded h-14 w-14"></td>
                                <td class="py-2 text-[12px] font-light">Magnolia Ready to Cook Chicken Lumpia Shanghai
                                    Mix
                                </td>
                                <td class="py-2 text-[12px] font-light">Frozen Products</td>
                                <td class="py-2 text-right text-[12px] font-light">₱200</td>
                            </tr>
                            <!-- Rows -->
                        </tbody>
                    </table>
                </div>

                <!-- Chart Orders -->
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="lets-icons:order-fill"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Orders</h3>
                    <canvas id="chartOrders"></canvas>
                </div>
            </div>

            <!-- Income Section -->
            <div class="grid grid-cols-1 gap-4 mt-6 lg:grid-cols-3">
                <div class="p-4 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="tdesign:money"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Income</h3>
                    <canvas id="incomeChart"></canvas>
                </div>

                <!-- Trending Products -->
                <div class="col-span-1 p-4 bg-white rounded-lg shadow-md lg:col-span-2">
                    <h3 class="text-lg font-semibold"><iconify-icon icon="mdi:fire"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Trending Products</h3>
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
                                <td class="py-2"><img src="{{ asset('img/Products/rtc-chicken-siomai.png') }}"
                                        alt="" class="w-16 h-16 rounded"></td>
                                <td class="py-2 text-[12px] font-light">Ready to Cook Chicken Siomai</td>
                                <td class="py-2 text-[12px] font-light">Frozen Products</td>
                                <td class="py-2 text-right text-[12px] font-light">314</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2">2</td>
                                <td class="py-2"><img src="{{ asset('img/Products/rtc-chicken-lumpia.png') }}"
                                        alt="" class="w-16 h-16 rounded"></td>
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
    </div>
    @include('components.footer')
    <script type="text/javascript">
        function dropdown() {
            document.querySelector("#submenu").classList.toggle("hidden");
            document.querySelector("#arrow").classList.toggle("rotate-180");
        }
        dropdown();

        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("main-content");

            if (window.innerWidth >= 1024) {
                // Desktop view
                if (sidebar.classList.contains("lg:-translate-x-full")) {
                    // Show sidebar
                    sidebar.classList.remove("lg:-translate-x-full");
                    mainContent.classList.remove("lg:ml-0");
                    mainContent.classList.add("lg:ml-[300px]");
                } else {
                    // Hide sidebar
                    sidebar.classList.add("lg:-translate-x-full");
                    mainContent.classList.remove("lg:ml-[300px]");
                    mainContent.classList.add("lg:ml-0");
                }
            } else {
                // Mobile view
                if (sidebar.style.transform === "translateX(0px)") {
                    sidebar.style.transform = "translateX(-100%)";
                } else {
                    sidebar.style.transform = "translateX(0px)";
                }
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>
</body>

</html>
