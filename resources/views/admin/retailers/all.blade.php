<x-app-layout>
  <div class="flex">
    <!-- Sidebar -->
    @include('components.admin-sidebar')

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6"> {{-- Offset the content by the width of the sidebar --}}
      @if (session('success'))
        <div class="mb-4 px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
          <span class="block sm:inline">{{ session('success') }}</span>
        </div>
      @endif

      @if (session('error'))
        <div class="mb-4 px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
          <span class="block sm:inline">{{ session('error') }}</span>
        </div>
      @endif

      <div class="w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-800">
          <h1 class="text-2xl font-bold text-white">All Retailers</h1>
        </div>

        <div class="p-4 overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-900 table-auto whitespace-normal">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
              <tr>
                <th class="px-4 py-3">User ID</th>
                <th class="px-4 py-3">Profile Picture</th>
                <th class="px-4 py-3">Store Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Permit</th>
                <th class="px-4 py-3">Address</th>
                <th class="px-4 py-3">Phone Number</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($retailerProfiles as $retailerProfile)
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-3">{{ $retailerProfile->user->id }}</td>
                  <td class="px-4 py-3">
                    @if ($retailerProfile->profile_picture)
                      <img src="{{ asset('storage/' . $retailerProfile->profile_picture) }}" alt="Profile Picture" class="w-16 h-16 object-cover cursor-pointer" onclick="openModal('{{ asset('storage/' . $retailerProfile->profile_picture) }}')">
                    @else
                      N/A
                    @endif
                  </td>
                  <td class="px-4 py-3">{{ $retailerProfile->business_name }}</td>
                  <td class="px-4 py-3 break-all">{{ $retailerProfile->user->email }}</td>
                  <td class="px-4 py-3">
                    @php
                      $permit = $retailerProfile->user->credentials->first(function ($credential) {
                        return str_contains($credential->file_path, 'credentials/permit/');
                      });
                    @endphp
                    @if ($permit)
                      <img src="{{ asset('storage/' . $permit->file_path) }}" alt="Permit" class="w-16 h-16 object-cover cursor-pointer" onclick="openModal('{{ asset('storage/' . $permit->file_path) }}')">
                    @else
                      <span class="text-gray-500">No Permit uploaded</span>
                    @endif
                  </td>
                  <td class="px-4 py-3 break-words">
                    {{ $retailerProfile->street ?? 'N/A' }}, {{ $retailerProfile->barangay_name ?? 'N/A' }}
                  </td>
                  <td class="px-4 py-3 break-words">{{ $retailerProfile->phone }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
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

  @vite(['resources/js/admin_dash.js'])
</x-app-layout>
