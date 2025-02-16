<div id="sidebar" class="fixed top-0 bottom-0 p-2 overflow-y-auto text-center transform bg-gray-900 lg:left-0">
    <!-- Sidebar content here -->
    <div class="text-xl text-gray-100">
        <div class="flex items-center px-1 py-2 mt-1">
            <img class="w-auto h-10" src="{{ asset('img/Pconnect Logo.png') }}" alt="PConnect">
            <h1 class="ml-3 font-bold text-gray-200">PConnect</h1>
            <i class="ml-auto text-2xl cursor-pointer bi bi-x" onclick="toggleSidebar()"></i>
        </div>
        <div class="my-2 bg-gray-600 h-[1px]"></div>
    </div>

    <div class="flex items-center px-4 py-1 mt-3 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.dashboard') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <a href="{{ route('distributors.dashboard') }}" class="flex items-center">
            <iconify-icon icon="mdi:home" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Dashboard</span>
        </a>
    </div>

    <div class="flex items-center px-4 py-1 mt-3 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.orders.index') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <a href="{{ route('distributors.orders.index') }}" class="flex items-center">
            <iconify-icon icon="material-symbols-light:sell" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">My Orders</span>
        </a>
    </div>

    <div class="flex items-center px-4 py-1 mt-3 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.returns.index') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <a href="{{ route('distributors.returns.index') }}" class="flex items-center">
            <iconify-icon icon="ph:key-return-fill" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Return | Refund</span>
        </a>
    </div>

    <div class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.cancellations.index') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <a href="{{ route('distributors.cancellations.index') }}" class="flex items-center">
            <iconify-icon icon="basil:cancel-solid" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Cancellation</span>
        </a>
    </div>

    <div class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.delivery.index') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <a href="{{ route('distributors.delivery.index') }}" class="flex items-center">
            <iconify-icon icon="mdi:truck-delivery" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Delivery</span>
        </a>
    </div>

    <div class="my-2 bg-gray-600 h-[1px]"></div>

    <div class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.products.index') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <a href="{{ route('distributors.products.index') }}" class="flex items-center">
            <iconify-icon icon="dashicons:products" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">My Products</span>
        </a>
    </div>

    <div class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.inventory.index') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <a href="{{ route('distributors.inventory.index') }}" class="flex items-center">
            <iconify-icon icon="ic:baseline-inventory-2" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Inventory</span>
        </a>
    </div>

    <div class="my-2 bg-gray-600 h-[1px]"></div>

    <div class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.messages.index') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <a href="{{ route('distributors.messages.index') }}" class="flex items-center">
            <iconify-icon icon="ant-design:message-filled" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Messages</span>
        </a>
    </div>

    <div class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600" onclick="dropdown()">
        <iconify-icon icon="material-symbols:block" class="text-xl icon"></iconify-icon>
        <div class="flex items-center justify-between w-full">
            <span class="ml-4 font-normal text-gray-200">Blocking</span>
            <span class="text-sm rotate-180" id="arrow">
                <i class="bi bi-chevron-down"></i>
            </span>
        </div>
    </div>
    <div class="w-4/5 mx-auto text-sm font-bold text-left text-gray-200" id="submenu">
        <h1 class="p-2 mt-1 rounded-md cursor-pointer hover:bg-green-600">Customers</h1>
        <h1 class="p-2 mt-1 rounded-md cursor-pointer hover:bg-green-600">Messages</h1>
    </div>

    <div class="my-2 bg-gray-600 h-[1px]"></div>

    <div class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.insights.index') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <a href="{{ route('distributors.insights.index') }}" class="flex items-center">
            <iconify-icon icon="gg:insights" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Business Insights</span>
        </a>
    </div>

    <div class="flex items-center px-4 py-2 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer hover:bg-green-600">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center">
                <i class="bi bi-box-arrow-in-right"></i>
                <span class="ml-4 font-normal text-gray-200">Logout</span>
            </button>
        </form>
    </div>
</div>