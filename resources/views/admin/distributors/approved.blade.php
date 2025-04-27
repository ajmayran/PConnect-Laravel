{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\distributors\approved.blade.php --}}
<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 p-4 ml-64">
            @if (session('error'))
                <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <div class="container px-4 py-8 mx-auto">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="px-6 py-4 bg-gray-800">
                        <h1 class="text-2xl font-bold text-white">Approved Distributors</h1>
                    </div>
                    <div class="p-6">
                        {{-- Responsive table container --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            ID</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            First Name</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Last Name</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Email</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Company Name</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Address</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Phone Number</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($approvedDistributors as $distributor)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                {{ $distributor->id }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                {{ $distributor->first_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                {{ $distributor->last_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                {{ $distributor->email }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                {{ $distributor->distributor->company_name }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                    @if ($distributor->distributor && $distributor->distributor->barangay)
                                                        {{ $distributor->distributor->getBarangayNameAttribute() }}
                                                        @if ($distributor->distributor->street)
                                                            , {{ $distributor->distributor->street }}
                                                        @endif
                                                    @else
                                                        {{ $distributor->distributor->company_address ?? 'N/A' }}
                                                    @endif
                                                </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                {{ $distributor->distributor->company_phone_number }}</td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                                <a href="{{ route('admin.distributorProducts', $distributor->distributor->id) }}"
                                                    class="font-medium text-blue-600 hover:text-blue-900">
                                                    View Products
                                                </a>
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
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <img id="modalImage" src="" alt="Document" class="w-full h-auto">
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal()"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-500 border border-transparent rounded-md shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
    </script>

    <!-- Include Admin Dashboard Scripts -->
    @vite(['resources/js/admin_dash.js'])
</x-app-layout>
