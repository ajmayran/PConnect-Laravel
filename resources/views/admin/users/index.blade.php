{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\users\index.blade.php --}}
<x-app-layout>
    <div class="flex min-h-screen">
        {{-- Include the admin sidebar --}}
        <div class="w-64 bg-green-200">
            @include('components.admin-sidebar')
        </div>

        {{-- Main content area --}}
        <div class="flex-1 p-6 bg-gray-50">
            <h1 class="text-2xl font-bold mb-4 text-gray-800">All Users</h1>

            {{-- Filter by User Type --}}
            <div class="mb-4">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition {{ !$userType ? 'font-bold' : '' }}">
                    All Users
                </a>
                <a href="{{ route('admin.users.index', ['user_type' => 'retailer']) }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition {{ $userType === 'retailer' ? 'font-bold' : '' }}">
                    Retailers
                </a>
                <a href="{{ route('admin.users.index', ['user_type' => 'distributor']) }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition {{ $userType === 'distributor' ? 'font-bold' : '' }}">
                    Distributors
                </a>
            </div>

            {{-- Users Table --}}
            <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-md">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">ID</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Name</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Email</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">User Type</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $user->id }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $user->name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $user->email }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst($user->user_type) }}</td>
                                <td class="px-4 py-2">
                                    @if ($user->user_type === 'retailer')
                                        <a href="{{ $navigationLinks['retailer'] }}" 
                                           class="text-blue-500 hover:underline">View Retailers</a>
                                    @elseif ($user->user_type === 'distributor')
                                        <a href="{{ $navigationLinks['distributor'] }}" 
                                           class="text-blue-500 hover:underline">Manage Distributors</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
        <!-- Include Admin Dashboard Scripts -->
        @vite(['resources/js/admin_dash.js'])
</x-app-layout>