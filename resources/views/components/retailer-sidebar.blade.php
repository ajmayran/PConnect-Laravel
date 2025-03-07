<div class="container flex gap-2 mx-auto">
    <div class="w-1/4 h-screen pt-5 bg-white shadow-md sm:rounded-lg ">
        <h2 class="p-4 mb-4 text-xl font-bold text-center">Account</h2>

        <ul class="text-center tab-list space-y">
            <li
                class="p-2 cursor-pointer tab-item hover:bg-green-200 {{ request()->routeIs('retailers.profile.edit') ? 'bg-gray-200 p-2 m-2 rounded-lg' : '' }}">
                <a href="{{ route('retailers.profile.edit') }}">Profile</a>
            </li>
            <li
                class="p-2 m-2 cursor-pointer tab-item hover:bg-green-200 {{ request()->routeIs('retailers.profile.my-purchase') ? 'bg-gray-200 p-2 m-2 rounded-lg' : '' }}">
                <a href="{{ route('retailers.profile.my-purchase') }}">My Purchase</a>
            </li>
            <li class="p-2 m-2 cursor-pointer tab-item hover:bg-green-200">Notifications</li>
            <li
                class="p-2 m-2 cursor-pointer tab-item hover:bg-green-200 {{ request()->routeIs('retailers.messages.*') ? 'bg-gray-200 p-2 m-2 rounded-lg' : '' }}">
                <a href="{{ route('retailers.messages.index') }}" class="flex items-center justify-center">
                    Messages
                    <span id="unread-message-badge"
                        class="inline-flex items-center justify-center hidden px-2 py-1 ml-2 text-xs font-bold leading-none text-white bg-red-500 rounded-full"></span>
                </a>
            </li>
            <li
                class="p-2 m-2 cursor-pointer tab-item hover:bg-green-200 {{ request()->routeIs('retailers.profile.settings') ? 'bg-gray-200 p-2 m-2 rounded-lg' : '' }}">
                <a href="{{ route('retailers.profile.settings') }}">Settings</a>
            </li>
            <li class="p-2 m-2 font-bold text-red-500 cursor-pointer tab-item hover:bg-green-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log Out</button>
                </form>
            </li>
        </ul>
    </div>
