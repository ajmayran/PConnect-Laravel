
<x-app-layout>
    <div class="flex">
        <!-- Include the admin sidebar -->
        @include('components.admin-sidebar')
        
        <!-- Main content area -->
        <div class="flex-1 p-4 ml-64">

            <div class="container px-4 py-8 mx-auto">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="px-6 py-4 bg-gray-800">
                        <h1 class="text-2xl font-bold text-white">Retailer Credentials</h1>
                    </div>
                    <div class="p-6">
                        <!-- Responsive table container -->
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Review retailer business permits and approve or reject them</p>
                        </div>
                        
                        @if ($retailers->isEmpty())
                            <div class="p-4 text-center text-gray-500">
                                No retailer credentials require attention at this time.
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Retailer</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Credentials</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($retailers as $retailer)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                    {{ $retailer->first_name }} {{ $retailer->last_name }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                    {{ $retailer->email }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                    @if ($retailer->status === 'pending')
                                                        <span class="px-2 py-1 text-xs text-yellow-800 bg-yellow-100 rounded-full">Pending</span>
                                                    @elseif($retailer->status === 'rejected')
                                                        <span class="px-2 py-1 text-xs text-red-800 bg-red-100 rounded-full">Rejected</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                    @foreach ($retailer->credentials as $credential)
                                                        <img src="{{ asset('storage/' . $credential->file_path) }}" 
                                                             alt="Credential" 
                                                             class="w-16 h-16 cursor-pointer" 
                                                             onclick="openModal('{{ asset('storage/' . $credential->file_path) }}')">
                                                    @endforeach
                                                    
                                                    @if($retailer->credentials->isEmpty())
                                                        <span class="text-gray-500">No documents</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 space-x-2 text-sm text-gray-900 whitespace-nowrap">
                                                    <form action="{{ route('admin.retailers.approve-credentials', $retailer->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 text-xs text-white bg-green-600 rounded hover:bg-green-700">
                                                            Approve
                                                        </button>
                                                    </form>

                                                    <button onclick="openRejectModal('{{ $retailer->id }}')" class="px-3 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700">
                                                        Reject
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $retailers->links() }}
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

    <!-- Rejection Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                    Reject Retailer Credentials
                                </h3>
                                <div class="w-full mt-4">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Reason for rejection</label>
                                    <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                        class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        required></textarea>
                                    <p class="mt-1 text-xs text-gray-500">This reason will be visible to the retailer.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="inline-flex justify-center px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Reject
                        </button>
                        <button type="button" onclick="closeRejectModal()" class="inline-flex justify-center px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Display SweetAlert messages for session notifications
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        function openModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function openRejectModal(retailerId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            
            form.action = `{{ url('admin/retailers') }}/${retailerId}/reject-credentials`;
            modal.classList.remove('hidden');
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Add submit handler to the reject form to show processing message
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            Swal.fire({
                title: 'Processing...',
                text: 'Rejecting retailer credentials',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    </script>
    
    <!-- Include Admin Dashboard Scripts -->
    @vite(['resources/js/admin_dash.js'])
</x-app-layout>