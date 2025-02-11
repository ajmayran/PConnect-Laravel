<div class="fixed top-0 left-0 z-50 w-64 h-full p-4 transition-transform sidebar-menu" style="background-color: #abebc6;">
    <a href="#" class="flex items-center pb-4 border-b border-b-gray-800">
        <img src="\img\Pconnect Logo.png" alt="Logo" class="object-cover w-8 h-8">
        <span class="ml-3 text-lg font-bold">PConnect</span>
    </a>
    <ul class="mt-4">
        <li class="mb-1 group active">
            <a href="#" class="flex items-center px-4 py-2 text-white bg-green-600 rounded-md">
                <iconify-icon icon="mdi:home" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Dashboard</span>
            </a>
        </li>
        <li class="mb-1 group">
            <a href="#"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-green-400 group-[.selected]:text-gray-100 sidebar-dropdown-toggle">
                <iconify-icon icon="mdi:package-variant-closed" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Products</span>
                <iconify-icon icon="mdi:keyboard-arrow-right"
                    class="ml-auto group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="#"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">All
                        Products</a>
                </li>
                <li class="mb-4">
                    <a href="#"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Removed
                        Products</a>
                </li>
            </ul>
        </li>
        <li class="mb-1 group">
            <a href="#"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-green-400 group-[.selected]:text-gray-100 sidebar-dropdown-toggle">
                <iconify-icon icon="tdesign:undertake-transaction" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Transactions</span>
                <iconify-icon icon="mdi:keyboard-arrow-right"
                    class="ml-auto group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="./transactions/completedOrders.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Transaction
                        History</a>
                </li>
                <li class="mb-4">
                    <a href="./transactions/CancelledOrders.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Cancelled
                        order</a>
                </li>
            </ul>
        </li>
        <li class="mb-1 group">
            <a href="#"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-green-400 group-[.selected]:text-gray-100 sidebar-dropdown-toggle">
                <iconify-icon icon="mdi:users" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Retailers</span>
                <iconify-icon icon="mdi:keyboard-arrow-right"
                    class="ml-auto group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="./retailers/activeRetailers.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Active
                        Retailers</a>
                </li>
                <li class="mb-4">
                    <a href="./retailers/restricted.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Restricted
                        Retailers</a>
                </li>
                <li class="mb-4">
                    <a href="./retailers/banned.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Banned
                        Retailers</a>
                </li>
            </ul>
        </li>
        <li class="mb-1 group">
            <a href="#"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-green-400 group-[.selected]:text-gray-100 sidebar-dropdown-toggle">
                <iconify-icon icon="mdi:truck" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Distributors</span>
                <iconify-icon icon="mdi:keyboard-arrow-right"
                    class="ml-auto group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('admin.pendingDistributors') }}"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Pending
                        Distributor</a>
                </li>
                <li class="mb-4">
                    <a href="./distributors/activeDist.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Active
                        Distributor</a>
                </li>
                <li class="mb-4">
                    <a href="./distributors/restrictedDist.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Restricted</a>
                </li>
                <li class="mb-4">
                    <a href="./distributors/bannedDist.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Banned</a>
                </li>
            </ul>
        </li>
        <li class="mb-1 group">
            <a href="#"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-green-400 group-[.selected]:text-gray-100 sidebar-dropdown-toggle">
                <iconify-icon icon="bx:support" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Support</span>
                <iconify-icon icon="mdi:keyboard-arrow-right"
                    class="ml-auto group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="./support/tickets.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Tickets</a>
                </li>
                <li class="mb-4">
                    <a href="./support/resolved.php"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Resolved</a>
                </li>
            </ul>
        </li>
        <li class="mt-20 mb-1 group">
            <a href="./settings.php"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-gray-950 group-[.selected]:text-gray-100">
                <iconify-icon icon="mdi:settings" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Settings</span>
            </a>
        </li>

        <li class="mt-2 mb-1 group">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center w-full py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-gray-950 group-[.selected]:text-gray-100">
                    <iconify-icon icon="mdi:logout" class="mr-3 text-xl"></iconify-icon>
                    <span class="text-sm">Logout</span>
                </button>
            </form>
        </li>
    </ul>
</div>
