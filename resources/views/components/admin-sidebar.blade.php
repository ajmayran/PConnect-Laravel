<div id="admin-sidebar"
    class="fixed top-0 left-0 z-50 w-64 h-full p-4 transition-transform duration-300 -translate-x-full sidebar-menu md:translate-x-0"
    style="background-color: #abebc6;">
    <div class="flex items-center justify-between pb-4 border-b border-b-gray-800">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center">
            <img src="\img\Pconnect Logo.png" alt="Logo" class="object-cover w-8 h-8">
            <span class="ml-3 text-lg font-bold">PConnect</span>
        </a>
        <button id="close-sidebar" class="p-1 text-gray-700 rounded-md md:hidden hover:bg-green-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <ul class="mt-4">
        <li class="mb-1 group active">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-2 text-white bg-green-600 rounded-md">
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
                    class="ml-auto transition-transform duration-300 ease-in-out group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('admin.allProducts') }}"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">All
                        Products</a>
                </li>
                {{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\components\admin-sidebar.blade.php --}}
                <li class="mb-1 group">
                    <a href="{{ route('categories.index') }}"
                        class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white">
                        <iconify-icon icon="mdi:folder" class="mr-3 text-xl"></iconify-icon>
                        <span class="text-sm">Manage Categories</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="mb-1 group">
            <a href="#"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-green-400 group-[.selected]:text-gray-100 sidebar-dropdown-toggle">
                <iconify-icon icon="tdesign:undertake-transaction" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Transactions</span>
                <iconify-icon icon="mdi:keyboard-arrow-right"
                    class="ml-auto transition-transform duration-300 ease-in-out group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="#"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Transaction
                        Subscription</a>
                </li>
            </ul>
        </li>
        <li class="mb-1 group">
            <a href="#"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-green-400 group-[.selected]:text-gray-100 sidebar-dropdown-toggle">
                <iconify-icon icon="mdi:users" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Retailers</span>
                <iconify-icon icon="mdi:keyboard-arrow-right"
                    class="ml-auto transition-transform duration-300 ease-in-out group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('admin.allRetailers') }}"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">All
                        Retailers</a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('admin.retailers.credentials') }}"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Credentials</a>
                </li>
            </ul>
        </li>
        <li class="mb-1 group">
            <a href="#"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-green-400 group-[.selected]:text-gray-100 sidebar-dropdown-toggle">
                <iconify-icon icon="mdi:truck" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Distributors</span>
                <iconify-icon icon="mdi:keyboard-arrow-right"
                    class="ml-auto transition-transform duration-300 ease-in-out group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('admin.pendingDistributors') }}"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Pending
                        Distributor</a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('admin.approvedDistributors') }}"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Manage
                        Distributors</a>
                </li>
                <li class="mb-4">
                    <a href="{{ route('admin.rejectedDistributors') }}"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Rejected
                        Distributors</a>
                </li>
            </ul>
        </li>
        <li class="mb-1 group">
            <a href="#"
                class="flex items-center py-2 px-4 hover:bg-green-500 hover:text-gray-100 rounded-md group-[.active]:bg-green-600 group-[.active]:text-white group-[.selected]:bg-green-400 group-[.selected]:text-gray-100 sidebar-dropdown-toggle">
                <iconify-icon icon="bx:support" class="mr-3 text-xl"></iconify-icon>
                <span class="text-sm">Support</span>
                <iconify-icon icon="mdi:keyboard-arrow-right"
                    class="ml-auto transition-transform duration-300 ease-in-out group-[.selected]:rotate-90"></iconify-icon>
            </a>
            <ul class="pl-7 mt-2 hidden group-[.selected]:block">
                <li class="mb-4">
                    <a href="{{ route('admin.tickets.index') }}"
                        class="text-sm flex items-center hover:text-gray-100 before:contents-[''] before:w-1 before:h-1 before:rounded-full before:bg-gray-300 before:mr-3">Pending
                        Tickets</a>
                </li>
                <li class="mb-1 group">
                    <a href="{{ route('admin.tickets.resolved') }}"
                        class="flex items-center px-4 py-2 rounded-md hover:bg-green-500 hover:text-gray-100">
                        <span class="text-sm">Resolved Tickets</span>
                    </a>
                </li>
                <li class="mb-1 group">
                    <a href="{{ route('admin.tickets.rejected') }}"
                        class="flex items-center px-4 py-2 rounded-md hover:bg-green-500 hover:text-gray-100">
                        <span class="text-sm">Rejected Tickets</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="mt-20 mb-1 group">
            <a href="{{ route('admin.settings') }}"
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
<div id="sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-black/50 md:hidden"></div>
