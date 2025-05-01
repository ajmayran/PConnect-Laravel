<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <h1 class="text-2xl font-bold text-gray-800">Followers</h1>

        <div class="mt-4 overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Retailer</th>
                        <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($followers as $follower)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex items-center">
                                    <img class="w-10 h-10 mr-3 rounded-full"
                                        src="{{ $follower->retailer->retailerProfile->profile_picture ? asset('storage/' . $follower->retailer->retailerProfile->profile_picture) : asset('img/default-profile.png') }}"
                                        alt="{{ $follower->retailer->first_name }} {{ $follower->retailer->last_name }}">
                                    <span>{{ $follower->retailer->first_name }} {{ $follower->retailer->last_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $follower->retailer->email }}
                            </td>
                            <td class="px-6 py-4 space-x-1 text-sm text-right">
                                <a href="{{ route('distributors.retailers.show', $follower->retailer->id) }}" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                                    View Profile
                                </a>
                                <button type="button" onclick="confirmRemoveFollower({{ $follower->id }})" class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600">
                                    Remove
                                </button>
                                <form id="remove-follower-form-{{ $follower->id }}" action="{{ route('distributors.followers.remove', $follower->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No followers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $followers->links() }}
        </div>
    </div>

    <script>
        function confirmRemoveFollower(followerId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to undo this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`remove-follower-form-${followerId}`).submit();
                }
            });
        }
    </script>
</x-distributor-layout>