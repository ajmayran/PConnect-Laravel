<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 ml-64 p-4">
            @if (session('error'))
                <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="container mx-auto py-8">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 bg-gray-800 rounded-t-lg">
                        <h1 class="text-2xl font-bold text-white">Approved Distributors</h1>
                    </div>

                    <div class="px-6 py-4">
                        <table class="w-full table-auto text-left text-sm text-gray-700">
                            <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-600">
                                <tr>
                                    <th class="py-3 px-4">ID</th>
                                    <th class="py-3 px-4">First Name</th>
                                    <th class="py-3 px-4">Last Name</th>
                                    <th class="py-3 px-4">Email</th>
                                    <th class="py-3 px-4">Company Name</th>
                                    <th class="py-3 px-4">Address</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($approvedDistributors as $distributor)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4">{{ $distributor->id }}</td>
                                        <td class="py-3 px-4">{{ $distributor->first_name }}</td>
                                        <td class="py-3 px-4">{{ $distributor->last_name }}</td>
                                        <td class="py-3 px-4">{{ $distributor->email }}</td>
                                        <td class="py-3 px-4">{{ $distributor->distributor->company_name ?? 'N/A' }}</td>
                                        <td class="py-3 px-4">
                                            @if ($distributor->distributor)
                                                @if ($distributor->distributor->barangay)
                                                    {{ $distributor->distributor->getBarangayNameAttribute() }}
                                                    @if ($distributor->distributor->street)
                                                        , {{ $distributor->distributor->street }}
                                                    @endif
                                                @else
                                                    {{ $distributor->distributor->company_address ?? 'N/A' }}
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            @if ($distributor->distributor)
                                                <a href="{{ route('admin.distributorProducts', $distributor->distributor->id) }}"
                                                   class="text-blue-600 hover:text-blue-900 font-medium">
                                                    View Products
                                                </a>
                                            @else
                                                <span class="text-gray-500">No Products</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <!-- Include Admin Dashboard Scripts -->
            @vite(['resources/js/admin_dash.js'])
</x-app-layout>