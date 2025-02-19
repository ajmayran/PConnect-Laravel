{{-- filepath: /c:/Users/nunez/Documents/PConnect-Laravel/resources/views/admin/products/pending.blade.php --}}
<x-app-layout>
    @if (session('success'))
        <div class="relative px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    <div class="container px-4 py-8 mx-auto">
        <div class="overflow-hidden bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 bg-gray-800">
                <h1 class="text-2xl font-bold text-white">Pending Distributors</h1>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    First Name
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Last Name
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingDistributors as $distributor)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->first_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->last_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->email }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        <a href="{{ route('admin.distributorProducts', $distributor->id) }}" class="font-medium text-blue-600 hover:text-blue-900">
                                            View Products
                                        </a>
                                        <form action="{{ route('admin.acceptDistributor', $distributor->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 font-medium text-white bg-green-600 rounded hover:bg-green-700">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.declineDistributor', $distributor->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="text" name="reason" placeholder="Reason for rejection" required class="border rounded px-2 py-1">
                                            <button type="submit" class="px-4 py-2 font-medium text-white bg-red-600 rounded hover:bg-red-700">
                                                Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>