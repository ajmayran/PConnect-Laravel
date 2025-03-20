{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\dashboard.blade.php --}}
<x-app-layout>
    <main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-50 min-h-screen transition-all main">
        <div class="fixed top-0 left-0 z-50 w-64 h-full p-4 transition-transform sidebar-menu"
            style="background-color: #abebc6;">
            <x-admin-sidebar />
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-3">
                {{-- Active Retailers --}}
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <div class="flex justify-between mb-6">
                        <div>
                            <div class="mb-1 text-2xl font-semibold">{{ $activeRetailersCount }}</div>
                            <div class="text-sm font-medium text-gray-400">Active Retailers</div>
                        </div>
                    </div>
                </div>


                {{-- Active Orders --}}
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <div class="flex justify-between mb-6">
                        <div>
                            <div class="mb-1 text-2xl font-semibold">{{ $activeOrdersCount }}</div>
                            <div class="text-sm font-medium text-gray-400">Active Orders</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                {{-- Order Statistics --}}
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <div class="flex items-start justify-between mb-4">
                        <div class="font-medium">Order Statistics</div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 lg:grid-cols-3">
                        <div class="p-4 border border-gray-200 border-dashed rounded-md">
                            <div class="flex items-center mb-0.5">
                                <div class="text-xl font-semibold">{{ $activeOrdersCount }}</div>
                            </div>
                            <span class="text-sm text-gray-400">Active</span>
                        </div>
                        <div class="p-4 border border-gray-200 border-dashed rounded-md">
                            <div class="flex items-center mb-0.5">
                                <div class="text-xl font-semibold">{{ $completedOrdersCount }}</div>
                            </div>
                            <span class="text-sm text-gray-400">Completed</span>
                        </div>
                        <div class="p-4 border border-gray-200 border-dashed rounded-md">
                            <div class="flex items-center mb-0.5">
                                <div class="text-xl font-semibold">{{ $canceledOrdersCount }}</div>
                            </div>
                            <span class="text-sm text-gray-400">Canceled</span>
                        </div>
                    </div>
                </div>

                {{-- Total Users --}}
                <div class="p-6 bg-white border border-gray-100 rounded-md shadow-md shadow-black/5">
                    <a href="{{ route('admin.users.index') }}" class="block">
                        <div class="flex justify-between mb-6">
                            <div>
                                <div class="mb-1 text-2xl font-semibold">{{ $totalUsersCount }}</div>
                                <div class="text-sm font-medium text-gray-400">Total Users</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>
</Body>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</html>
</x-app-layout>