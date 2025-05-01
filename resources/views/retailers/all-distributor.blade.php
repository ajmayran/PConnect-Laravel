<x-app-layout>
    <div class="min-h-screen bg-gray-200">
        <x-retailer-topnav />

        <div class="relative z-50">
            <form action="{{ route('retailers.search') }}" method="GET" class="max-w-2xl p-2 mx-auto sm:p-4">
                <div class="flex gap-0">
                    <div class="relative w-full">
                        <input type="search" name="query"
                            class="z-20 block w-full p-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg sm:p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Search distributors..." required value="{{ request('query') }}" />
                        <button type="submit"
                            class="absolute top-0 h-full p-2 text-sm font-medium text-white bg-green-500 border border-green-500 rounded-r-lg sm:p-3 end-0 hover:bg-green-600 focus:ring-2 focus:outline-none focus:ring-green-300">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                            <span class="sr-only">Search</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <section class="py-8 mt-4 mb-12 bg-gray-200 sm:py-16 sm:mt-10 sm:mb-24">
            <div class="container px-4 mx-auto sm:px-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-center text-gray-800 sm:mb-10 sm:text-3xl">
                        Our Trusted Distributors
                    </h2>
                    <!-- Checkbox to filter following distributors -->
                    <div class="flex items-center">
                        <input type="checkbox" id="showFollowing" class="mr-2" onchange="filterFollowing()">
                        <label for="showFollowing" class="text-sm text-gray-700">Show Only Following</label>
                    </div>
                </div>

                <div id="distributorGrid"
                    class="grid grid-cols-1 gap-4 sm:gap-6 md:gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($distributors as $distributor)
                        <div
                            class="relative transition-all duration-300 transform hover:scale-105 distributor-card {{ $distributor->is_following ? 'following' : '' }}">
                            <!-- Following Badge -->
                            @if ($distributor->is_following)
                                <span
                                    class="absolute px-2 py-1 text-xs font-bold text-white bg-green-500 rounded-full top-2 right-2">
                                    Following
                                </span>
                            @endif

                            <x-card-all-distributor route="{{ route('retailers.distributor-page', $distributor->id) }}"
                                imagepath="{{ $distributor->company_profile_image
                                    ? asset('storage/' . $distributor->company_profile_image)
                                    : asset('img/default-distributor.jpg') }}"
                                distributor_name="{{ $distributor->company_name }}"
                                distributor_desc="{{ $distributor->description }}" address="{{ $distributor->street }}">
                            </x-card-all-distributor>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500 col-span-full">
                            No distributors found.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
    <x-footer />

    <script>
        function filterFollowing() {
            const showFollowing = document.getElementById('showFollowing').checked;
            const cards = document.querySelectorAll('.distributor-card');

            cards.forEach(card => {
                console.log(card.classList); // Debugging: Check the classes applied to each card
                if (showFollowing) {
                    if (!card.classList.contains('following')) {
                        card.classList.add('hidden');
                    }
                } else {
                    card.classList.remove('hidden');
                }
            });
        }
    </script>
</x-app-layout>
