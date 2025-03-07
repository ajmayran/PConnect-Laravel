<nav class="mb-8">
    <ul class="flex overflow-hidden text-sm font-medium text-center text-gray-500 divide-x divide-gray-200 rounded-lg shadow">
        <li class="w-full">
            <a href="{{ route('retailers.orders.index') }}" 
               class="inline-block w-full px-4 py-3 {{ request()->routeIs('retailers.orders.index') ? 'text-white bg-green-600 hover:bg-green-700' : 'bg-white hover:text-gray-700 hover:bg-gray-50' }}">
                Pending
            </a>
        </li>
        <li class="w-full">
            <a href="{{ route('retailers.orders.to-pay') }}"
               class="inline-block w-full px-4 py-3 {{ request()->routeIs('retailers.orders.to-pay') ? 'text-white bg-green-600 hover:bg-green-700' : 'bg-white hover:text-gray-700 hover:bg-gray-50' }}">
                To Pay
            </a>
        </li>
        <li class="w-full">
            <a href="{{ route('retailers.orders.to-receive') }}"
               class="inline-block w-full px-4 py-3 {{ request()->routeIs('retailers.orders.to-receive') ? 'text-white bg-green-600 hover:bg-green-700' : 'bg-white hover:text-gray-700 hover:bg-gray-50' }}">
                To Receive
            </a>
        </li>
        <li class="w-full">
            <a href="{{ route('retailers.orders.completed') }}"
               class="inline-block w-full px-4 py-3 {{ request()->routeIs('retailers.orders.completed') ? 'text-white bg-green-600 hover:bg-green-700' : 'bg-white hover:text-gray-700 hover:bg-gray-50' }}">
                Completed
            </a>
        </li>
        <li class="w-full">
            <a href="{{ route('retailers.orders.cancelled') }}"
               class="inline-block w-full px-4 py-3 {{ request()->routeIs('retailers.orders.cancelled') ? 'text-white bg-green-600 hover:bg-green-700' : 'bg-white hover:text-gray-700 hover:bg-gray-50' }}">
                Cancelled
            </a>
        </li>
        <li class="w-full">
            <a href="{{ route('retailers.orders.returned') }}"
               class="inline-block w-full px-4 py-3 {{ request()->routeIs('retailers.orders.returned') ? 'text-white bg-green-600 hover:bg-green-700' : 'bg-white hover:text-gray-700 hover:bg-gray-50' }}">
                Returned|Refunded
            </a>
        </li>

    </ul>
</nav>