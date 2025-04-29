<div id="sidebar" class="fixed top-0 bottom-0 z-50 p-2 overflow-y-auto text-center transform bg-gray-900 lg:left-0">
    <!-- Sidebar content here -->
    <div class="text-xl text-gray-100">
        <div class="flex items-center px-1 py-2 mt-1">
            <img class="w-auto h-10" src="{{ asset('img/Pconnect Logo.png') }}" alt="PConnect">
            <h1 class="ml-3 font-bold text-gray-200">PConnect</h1>
            <i class="ml-auto text-2xl cursor-pointer bi bi-x" onclick="toggleSidebar()"></i>
        </div>
        <div class="my-2 bg-gray-600 h-[1px]"></div>
    </div>

    <a href="{{ route('distributors.dashboard') }}"
        class="flex items-center px-4 py-1 mt-3 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.dashboard') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="mdi:home" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Dashboard</span>
        </div>
    </a>

    <a href="{{ route('distributors.orders.index') }}"
        class="flex items-center px-4 py-1 mt-3 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.orders.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="material-symbols-light:sell" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">My Orders</span>
        </div>
    </a>

    <a href="{{ route('distributors.returns.index') }}"
        class="flex items-center px-4 py-1 mt-3 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.returns.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="ph:key-return-fill" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Return | Refund</span>
        </div>
    </a>

    <a href="{{ route('distributors.cancellations.index') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.cancellations.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="basil:cancel-solid" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Cancellation</span>
        </div>
    </a>

    <a href="{{ route('distributors.delivery.index') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.delivery.*') || request()->routeIs('distributors.trucks.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="mdi:truck-delivery" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Delivery</span>
        </div>
    </a>

    <div class="my-2 bg-gray-600 h-[1px]"></div>

    <a href="{{ route('distributors.products.index') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.products.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="dashicons:products" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">My Products</span>
        </div>
    </a>

    <a href="{{ route('distributors.inventory.index') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.inventory.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="ic:baseline-inventory-2" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Inventory</span>
        </div>
    </a>

    <div class="my-2 bg-gray-600 h-[1px]"></div>

    <a href="{{ route('distributors.messages.index') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.messages.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="ant-design:message-filled" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Messages</span>
            <span id="unread-message-badge"
                class="inline-flex items-center justify-center hidden px-2 py-1 ml-2 text-xs font-bold leading-none text-white bg-red-500 rounded-full"></span>
        </div>
    </a>

    <a href="{{ route('distributors.notifications.index') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.notifications.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="ion:notifcations" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Notifications</span>
            <span id="unread-message-badge"
                class="inline-flex items-center justify-center hidden px-2 py-1 ml-2 text-xs font-bold leading-none text-white bg-red-500 rounded-full"></span>
        </div>
    </a>



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
        <a href="{{ route('distributors.blocking.blocked-retailers') }}"
            class="block p-2 mt-1 rounded-md cursor-pointer hover:bg-green-600 {{ request()->routeIs('distributors.blocking.blocked-retailers') ? 'bg-green-600' : '' }}">Block
            Retailers</a>
        <a href="{{ route('distributors.blocking.blocked-messages') }}"
            class="block p-2 mt-1 rounded-md cursor-pointer hover:bg-green-600 {{ request()->routeIs('distributors.blocking.blocked-messages') ? 'bg-green-600' : '' }}">
            Blocked Messages
        </a>
    </div>

    <a href="{{ route('distributors.discounts.index') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.discounts.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="mdi:sale" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Discounts & Promos</span>
        </div>
    </a>

    <div class="my-2 bg-gray-600 h-[1px]"></div>

    <a href="{{ route('distributors.payments.index') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.payments.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="hugeicons:payment-02" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Payments</span>
        </div>
    </a>

    <a href="{{ route('distributors.insights.index') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.insights.index') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="gg:insights" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Business Insights</span>
        </div>
    </a>

    <a href="{{ route('distributors.subscription.show') }}"
        class="flex items-center px-4 py-1 mt-2 ml-2 text-white duration-300 rounded-md cursor-pointer {{ request()->routeIs('distributors.subscription.*') ? 'bg-green-600' : 'hover:bg-green-600' }}">
        <div class="flex items-center">
            <iconify-icon icon="mdi:crown" class="text-xl icon"></iconify-icon>
            <span class="ml-4 font-normal text-gray-200">Subscription</span>
            @if (Auth::user()->distributor->activeSubscription)
                <span class="ml-2 px-1.5 py-0.5 text-xs font-medium bg-green-600 text-white rounded-full">
                    {{ ucfirst(str_replace('_', ' ', Auth::user()->distributor->activeSubscription->plan ?? 'Free')) }}
                </span>
            @else
                <span class="ml-2 px-1.5 py-0.5 text-xs font-medium bg-yellow-500 text-white rounded-full">Free</span>
            @endif
        </div>
    </a>

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
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initial check for unread messages
        fetchUnreadMessagesCount();

        // Periodically check for new messages (every 30 seconds)
        setInterval(fetchUnreadMessagesCount, 30000);

        function fetchUnreadMessagesCount() {
            fetch('{{ route('distributors.messages.unread-count') }}')
                .then(response => response.json())
                .then(data => {
                    const unreadBadge = document.getElementById('unread-message-badge');
                    if (unreadBadge) {
                        if (data.unread_count > 0) {
                            unreadBadge.textContent = data.unread_count;
                            unreadBadge.classList.remove('hidden');
                        } else {
                            unreadBadge.classList.add('hidden');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching unread messages:', error);
                });
        }

        // Set up Pusher for real-time notifications
        const pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
            cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
            encrypted: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            }
        });

        // Subscribe to the private channel
        const channel = pusher.subscribe('private-chat.{{ Auth::id() }}');

        // Update badge when new message arrives
        channel.bind('message.sent', function(data) {
            // Increment badge immediately without waiting for the next fetch
            const unreadBadge = document.getElementById('unread-message-badge');
            if (unreadBadge) {
                const currentCount = parseInt(unreadBadge.textContent || '0');
                unreadBadge.textContent = currentCount + 1;
                unreadBadge.classList.remove('hidden');
            } else {
                // If for some reason the badge isn't found, fallback to fetching the count
                fetchUnreadMessagesCount();
            }
        });
    });
</script>
