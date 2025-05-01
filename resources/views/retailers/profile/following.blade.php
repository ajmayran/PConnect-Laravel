<x-app-layout>
    <x-dashboard-nav />
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Following') }}
        </h2>
    </x-slot>

    <div class="flex py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <x-retailer-sidebar :user="Auth::user()" /> <!-- Retailer Sidebar -->

        <div class="flex-1 space-y-6 lg:pl-8">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Following</h1>
                <div>
                    <span class="text-sm text-gray-500">View and manage the distributors you are following.</span>
                </div>
            </div>

            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-900">
                        {{ __('Distributors You Follow') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-800">
                        {{ __('Here is the list of distributors you are currently following.') }}
                    </p>
                </header>

                @if ($followedDistributors->isEmpty())
                    <div class="p-4 mt-4 text-sm text-gray-500 bg-gray-100 border border-gray-300 rounded-md">
                        You are not following any distributors yet.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($followedDistributors as $follower)
                            <div class="p-4 bg-white border border-gray-300 rounded-lg shadow">
                                <img src="{{ $follower->distributor->company_profile_image ? Storage::url($follower->distributor->company_profile_image) : asset('images/default-placeholder.png') }}"
                                    alt="{{ $follower->distributor->company_name }}"
                                    class="object-cover w-full h-32 mb-4 rounded-md">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    {{ $follower->distributor->company_name }}</h3>
                                <a href="{{ route('retailers.distributor-page', $follower->distributor->id) }}"
                                    class="text-sm text-blue-500 hover:underline">View Distributor</a>
                                <button onclick="confirmUnfollow({{ $follower->distributor->id }})"
                                    class="px-4 py-2 mt-2 text-white bg-red-500 rounded-lg hover:bg-red-600">
                                    Unfollow
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function confirmUnfollow(distributorId) {
            Swal.fire({
                title: 'Unfollow Distributor?',
                text: "Are you sure you want to unfollow this distributor?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, unfollow'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('retailers.distributors.follow') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                distributor_id: distributorId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Unfollowed!', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error!', 'Unable to unfollow the distributor.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        });
                }
            });
        }
    </script>
</x-app-layout>
<x-footer />
