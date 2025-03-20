<x-app-layout>
    <div class="flex min-h-screen">
        {{-- Include the admin sidebar --}}
        <div class="w-64 bg-green-200">
            @include('components.admin-sidebar')
        </div>

        {{-- Main content area --}}
        <div class="flex-1 p-6 bg-gray-50">
            <h1 class="text-2xl font-bold mb-4 text-gray-800">Rejected Distributors</h1>

            {{-- Rejected Distributors Table --}}
            <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-md">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">ID</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">First Name</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Middle Name</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Last Name</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Email</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Reason for Rejection</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Submitted Information</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rejectedDistributors as $distributor)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $distributor->id }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $distributor->first_name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $distributor->middle_name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $distributor->last_name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $distributor->email }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">
                                    {{ $distributor->rejection_reason ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    <a href="{{ route('admin.viewInformation', $distributor->id) }}" class="px-4 py-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-700">View Information</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-2 text-center text-sm text-gray-500">
                                    No rejected distributors found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $rejectedDistributors->links() }}
            </div>
        </div>
    </div>
        <!-- Include Admin Dashboard Scripts -->
        @vite(['resources/js/admin_dash.js'])
</x-app-layout>
