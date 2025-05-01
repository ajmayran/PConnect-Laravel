<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 p-4 ml-64">
            @if (session('success'))
                <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <div class="container px-4 py-8 mx-auto">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="px-6 py-4 bg-gray-800">
                        <h1 class="text-2xl font-bold text-white">Pending Distributors</h1>
                    </div>
                    <div class="p-6">
                        @if($pendingDistributors->isEmpty())
                            <div class="py-6 text-center">
                                <p class="text-lg text-gray-500">No pending distributor applications found.</p>
                            </div>
                        @else
                            {{-- Responsive table container --}}
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">User ID</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">BIR Form</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">SEC Document</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pendingDistributors as $distributor)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->id }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                    {{ $distributor->first_name }} {{ $distributor->last_name }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->email }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                    @php
                                                        // Get the first credential (BIR form)
                                                        $birForm = $distributor->credentials->first();
                                                    @endphp
                                                    @if ($birForm)
                                                        <img src="{{ asset('storage/' . $birForm->file_path) }}" alt="BIR Form" class="w-16 h-16 cursor-pointer" onclick="openModal('{{ asset('storage/' . $birForm->file_path) }}', 'bir_form', {{ $distributor->id }}, {{ $birForm->id }})">
                                                    @else
                                                        <span class="text-gray-500">No BIR Form uploaded</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                    @php
                                                        // Get the second credential (SEC document)
                                                        $secDocument = $distributor->credentials->skip(1)->first();
                                                    @endphp
                                                    @if ($secDocument)
                                                        <img src="{{ asset('storage/' . $secDocument->file_path) }}" alt="SEC Document" class="w-16 h-16 cursor-pointer" onclick="openModal('{{ asset('storage/' . $secDocument->file_path) }}', 'sec_document', {{ $distributor->id }}, {{ $secDocument->id }})">
                                                    @else
                                                        <span class="text-gray-500">No SEC Document uploaded</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                                    <form action="{{ route('admin.acceptDistributor', $distributor->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 font-medium text-white bg-green-500 rounded hover:bg-green-700">Accept</button>
                                                    </form>
                                                
                                                    {{-- Reject Button --}}
                                                    <button type="button" onclick="openReasonModal('{{ $distributor->id }}')" class="px-4 py-2 font-medium text-white bg-red-500 rounded hover:bg-red-700">Reject</button>
                                                </td>
                                            </tr>

                                            {{-- Rejection Reason Modal --}}
                                            <div id="reasonModal-{{ $distributor->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
                                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                    <div class="fixed inset-0 transition-opacity">
                                                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                                    </div>
                                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
                                                    <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                        <form action="{{ route('admin.declineDistributor', $distributor->id) }}" method="POST">
                                                            @csrf
                                                            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                                                                <h3 class="text-lg font-medium leading-6 text-gray-900">Reason for Rejection</h3>
                                                                <div class="mt-2">
                                                                    <textarea name="reason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required placeholder="Please provide a reason for rejecting this application"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-500 border border-transparent rounded-md shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Submit</button>
                                                                <button type="button" onclick="closeReasonModal('{{ $distributor->id }}')" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
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
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <img id="modalImage" src="" alt="Document" class="w-full h-auto">
                </div>
                <div id="modalButtons" class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeModal()" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium rounded-md shadow-sm border sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm bg-red-600 text-white border-red-700 hover:bg-white hover:text-red-700 hover:border-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">Close</button>            </div>
        </div>
    </div>

    <script>
        function openModal(imageSrc, credentialType, distributorId, credentialId) {
            // Set the image source
            document.getElementById('modalImage').src = imageSrc;

            // Clear existing buttons
            const modalButtons = document.getElementById('modalButtons');
            modalButtons.innerHTML = '';

            // Add the appropriate download button
            if (credentialType === 'bir_form') {
                modalButtons.innerHTML = `
                    <a href="/admin/distributors/${distributorId}/credentials/${credentialId}/download" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-white bg-blue-500 border border-transparent rounded-md shadow-sm hover:bg-blue-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Download BIR Form</a>
                `;
            } else if (credentialType === 'sec_document') {
                modalButtons.innerHTML = `
                    <a href="/admin/distributors/${distributorId}/credentials/${credentialId}/download" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-white bg-blue-500 border border-transparent rounded-md shadow-sm hover:bg-blue-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Download SEC Document</a>
                `;
            }

            // Show the modal
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

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