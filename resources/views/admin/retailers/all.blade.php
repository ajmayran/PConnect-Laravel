
<x-app-layout>
  <div class="flex">
    <!-- Include the admin sidebar -->
    @include('components.admin-sidebar')
      <!-- Main content area -->
        <div class="flex-1 p-4 ml-64">

        <div class="container px-4 py-8 mx-auto">
            <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 bg-gray-800">
                  <h1 class="text-2xl font-bold text-white">All Retailers</h1>
                </div>
                <div class="p-6">
                  <!-- Responsive table container -->
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                          <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">User ID</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Profile Picture</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Store Name</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Permit</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Address</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Phone Number</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions</th>
                          </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                          @foreach($retailerProfiles as $retailerProfile)
                            <tr class="hover:bg-gray-50">
                              <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $retailerProfile->user->id }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                  @if ($retailerProfile->profile_picture)
                                      <img src="{{ asset('storage/' . $retailerProfile->profile_picture) }}" alt="Profile Picture" class="w-16 h-16 cursor-pointer" onclick="openModal('{{ asset('storage/' . $retailerProfile->profile_picture) }}')">
                                  @else
                                      N/A
                                  @endif
                              </td>
                              <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $retailerProfile->business_name }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $retailerProfile->user->email }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                  @php
                                      $permit = $retailerProfile->user->credentials->first(function ($credential) {
                                          return str_contains($credential->file_path, 'credentials/permit/');
                                      });
                                  @endphp
                                  @if ($permit)
                                      <img src="{{ asset('storage/' . $permit->file_path) }}" alt="Permit" class="w-16 h-16 cursor-pointer" onclick="openModal('{{ asset('storage/' . $permit->file_path) }}')">
                                  @else
                                      <span class="text-gray-500">No Permit uploaded</span>
                                  @endif
                              </td>
                              <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                  {{ $retailerProfile->street ?? 'N/A' }}, {{ $retailerProfile->barangay_name ?? 'N/A' }}
                              </td>
                              <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $retailerProfile->phone }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                  <button 
                                      type="button" 
                                      onclick="confirmRejectRetailer({{ $retailerProfile->user->id }}, '{{ $retailerProfile->business_name }}')"
                                      class="px-3 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700">
                                      Reject
                                  </button>
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
  
  <!-- Hidden form for rejection -->
  <form id="rejectRetailerForm" method="POST" style="display: none;">
      @csrf
      <input type="hidden" name="rejection_reason" value="Invalid credentials. Please reupload your business permit/credentials.">
  </form>
  
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
    
    function confirmRejectRetailer(userId, businessName) {
        Swal.fire({
            title: 'Reject Retailer?',
            html: `Are you sure you want to reject <strong>${businessName}</strong>?<br><br>
                  <p>Reason: Invalid credentials. Please reupload your business permit/credentials.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, reject retailer',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Set the form action
                const form = document.getElementById('rejectRetailerForm');
                form.action = `/admin/retailers/${userId}/reject-credentials`;
                
                // Submit form
                Swal.fire({
                    title: 'Processing...',
                    text: 'Rejecting retailer credentials',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        form.submit();
                    }
                });
            }
        });
    }
  </script>
  
  <!-- Include Admin Dashboard Scripts -->
  @vite(['resources/js/admin_dash.js'])
</x-app-layout>