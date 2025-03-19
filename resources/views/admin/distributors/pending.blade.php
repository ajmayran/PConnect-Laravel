{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\distributors\pending.blade.php --}}
<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 ml-64 p-4">
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
                        {{-- Responsive table container --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">User ID</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">First Name</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Middle Name</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Last Name</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">BIR Form</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">SEC Document</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingDistributors as $distributor)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->id }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->first_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->middle_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->last_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                @php
                                                    $birForm = $distributor->credentials->first(function ($credential) {
                                                        return str_contains($credential->file_path, 'credentials/bir/');
                                                    });
                                                @endphp
                                                @if ($birForm)
                                                    <img src="{{ asset('storage/' . $birForm->file_path) }}" alt="BIR Form" class="w-16 h-16 cursor-pointer" onclick="openModal('{{ asset('storage/' . $birForm->file_path) }}')">
                                                @else
                                                    <span class="text-gray-500">No BIR Form uploaded</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                @php
                                                    $secDocument = $distributor->credentials->first(function ($credential) {
                                                        return str_contains($credential->file_path, 'credentials/sec/');
                                                    });
                                                @endphp
                                                @if ($secDocument)
                                                    <img src="{{ asset('storage/' . $secDocument->file_path) }}" alt="SEC Document" class="w-16 h-16 cursor-pointer" onclick="openModal('{{ asset('storage/' . $secDocument->file_path) }}')">
                                                @else
                                                    <span class="text-gray-500">No SEC Document uploaded</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                                <form action="{{ route('admin.acceptDistributor', $distributor->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 font-medium text-white bg-green-500 rounded hover:bg-green-700">Accept</button>
                                                </form>
                                                <button type="button" onclick="openReasonModal('{{ $distributor->id }}')" class="px-4 py-2 font-medium text-white bg-red-500 rounded hover:bg-red-700">Decline</button>
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
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <img id="modalImage" src="" alt="Document" class="w-full h-auto">
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal()" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-500 border border-transparent rounded-md shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Close</button>
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
    <script>
        function openReasonModal(distributorId) {
            document.getElementById('reasonModal-' + distributorId).classList.remove('hidden');
        }

        function closeReasonModal(distributorId) {
            document.getElementById('reasonModal-' + distributorId).classList.add('hidden');
        }
    </script>

    <!-- Include Admin Dashboard Scripts -->
    @vite(['resources/js/admin_dash.js'])
</x-app-layout>