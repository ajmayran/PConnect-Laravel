@if (session('error'))
    <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif
<x-app-layout>
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
                                    File</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($pendingDistributors as $distributor)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->id }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                        {{ $distributor->first_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                        {{ $distributor->last_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                        {{ $distributor->email }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        @if ($distributor->credential)
                                            <a href="{{ route('admin.downloadCredential', $distributor->id) }}"
                                                class="font-medium text-blue-600 hover:text-blue-900">
                                                Download File
                                            </a>
                                        @else
                                            <span class="text-gray-500">No file available</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 space-x-2 text-sm whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <form id="approve-{{ $distributor->id }}"
                                                action="{{ route('admin.acceptDistributor', $distributor->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="button"
                                                    onclick="confirmApprove('approve-{{ $distributor->id }}')"
                                                    class="px-4 py-2 font-medium text-white transition duration-150 ease-in-out bg-green-500 rounded-md hover:bg-green-600">
                                                    Approve
                                                </button>
                                            </form>
                                            <form id="decline-{{ $distributor->id }}"
                                                action="{{ route('admin.declineDistributor', $distributor->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="button"
                                                    onclick="confirmDecline('decline-{{ $distributor->id }}')"
                                                    class="px-4 py-2 font-medium text-white transition duration-150 ease-in-out bg-red-500 rounded-md hover:bg-red-600">
                                                    Decline
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmApprove(formId) {
            Swal.fire({
                title: 'Approve Distributor?',
                text: "Are you sure you want to approve this distributor?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, approve!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        function confirmDecline(formId) {
            Swal.fire({
                title: 'Decline Distributor?',
                text: "Are you sure you want to decline this distributor?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, decline!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</x-app-layout>
