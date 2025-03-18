<x-app-layout>
    <x-dashboard-nav />

    <!-- Search Section - Made more compact on mobile -->
    <div class="relative z-20">
        <form action="{{ route('retailers.search') }}" method="GET" class="max-w-2xl p-2 mx-auto sm:p-4" id="searchForm">
            <div class="flex gap-0">
                <div class="relative w-full">
                    <input type="search" id="search-dropdown" name="query"
                        class="z-20 block w-full p-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg sm:p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Search products or distributors..." required value="{{ request('query') }}" />
                    <button type="submit"
                        class="absolute top-0 h-full p-2 text-sm font-medium text-white bg-green-500 border border-green-500 rounded-r-lg sm:p-3 end-0 hover:bg-green-600 focus:ring-2 focus:outline-none focus:ring-green-300">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                        <span class="sr-only">Search</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="searchOverlay" class="fixed inset-0 z-10 hidden transition-opacity bg-black bg-opacity-50"></div>

    <div id="mainContent">
        <!-- Distributors Section - Improved grid responsiveness -->
        <section class="py-4 bg-gray-200 sm:py-8">
            <div class="container px-2 mx-auto sm:px-4">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">Explore Distributors</h2>
                    <a href="{{ route('retailers.all-distributor') }}"
                        class="relative flex items-center text-sm font-medium text-green-600 sm:text-base hover:text-green-700 group">
                        <span class="mr-1">View All</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-0 h-4 transition-all duration-300 opacity-0 group-hover:opacity-100 group-hover:w-4"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 bg-green-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @forelse ($distributors as $distributor)
                        <a href="{{ route('retailers.distributor-page', $distributor->id) }}"
                            class="relative flex flex-col items-center p-3 transition-all duration-300 bg-white border border-gray-100 shadow-md sm:p-6 rounded-xl group active:scale-95 hover:shadow-xl active:shadow-inner touch-manipulation"
                            ontouchstart="">

                            @if ($distributor->is_blocked)
                                <div class="absolute top-2 right-2">
                                    <span class="flex items-center justify-center w-6 h-6 bg-red-500 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    </span>
                                </div>
                            @endif

                            <img class="w-16 h-16 mb-2 transition-transform duration-300 rounded-full shadow-md sm:w-24 sm:h-24 sm:mb-4 group-hover:scale-110 group-active:scale-105 {{ $distributor->is_blocked ? 'opacity-60' : '' }}"
                                src="{{ $distributor->company_profile_image ? asset('storage/' . $distributor->company_profile_image) : asset('img/default-distributor.jpg') }}"
                                alt="Distributor {{ $distributor->user?->name ?? 'Unknown' }}">
                            <h3
                                class="text-sm font-bold text-center text-gray-800 sm:text-lg group-hover:text-green-600 group-active:text-green-700 {{ $distributor->is_blocked ? 'opacity-60' : '' }}">
                                {{ $distributor->company_name }}
                            </h3>
                        </a>
                    @empty
                        <div class="text-center text-gray-500 col-span-full">
                            No distributors found. Explore more distributors to find partners for your business.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section class="py-4 bg-gray-200 sm:py-8">
            <div class="container px-2 mx-auto sm:px-4">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">Popular Products</h2>

                    @if (isset($hasBlockedDistributors) && $hasBlockedDistributors)
                        <div class="px-3 py-1 text-xs text-yellow-800 bg-yellow-100 rounded-full">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Some products are hidden
                            </span>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-6 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @forelse ($products as $product)
                        <a href="{{ route('retailers.products.show', $product->id) }}"
                            class="relative flex flex-col overflow-hidden transition-all duration-300 bg-white rounded-lg shadow-md group hover:shadow-xl">
                            <div class="relative w-full pt-[100%] overflow-hidden bg-gray-100">
                                <img class="absolute inset-0 object-contain w-full h-full p-2 transition-transform duration-300 group-hover:scale-110"
                                    src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
                                    alt="{{ $product->product_name }}"
                                    onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                            </div>
                            <div class="flex flex-col flex-grow p-2 sm:p-4">
                                <h3 class="mb-1 text-sm font-bold text-gray-800 sm:mb-2 sm:text-lg line-clamp-2">
                                    {{ $product->product_name }}
                                </h3>
                                <p class="mt-1 text-xs text-gray-500 sm:text-sm line-clamp-1">
                                    {{ $product->distributor->company_name }}
                                </p>
                                <div class="flex items-center justify-between pt-2 mt-auto sm:pt-4">
                                    <span
                                        class="text-sm font-bold text-green-600 sm:text-lg">₱{{ number_format($product->price, 2) }}</span>
                                    <span class="text-xs text-gray-500 sm:text-sm">View Details →</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-gray-500 col-span-full">
                            No products available at the moment. Check back later for new products.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    <!-- Pagination Section - Improved mobile layout -->
    <div class="container p-2 mx-auto sm:p-4">
        <div class="mt-4 sm:mt-8">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <!-- Pagination Results Info -->
                <div class="order-2 text-xs text-gray-600 sm:text-sm sm:order-1">
                    {!! __('Showing :first to :last of :total results', [
                        'first' => $products->firstItem() ?? 0,
                        'last' => $products->lastItem() ?? 0,
                        'total' => $products->total(),
                    ]) !!}
                </div>
                <!-- Pagination Links -->
                <div class="order-1 w-full sm:order-2 sm:w-auto">
                    {{ $products->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('search-dropdown');
                const searchOverlay = document.getElementById('searchOverlay');
                const mainContent = document.getElementById('mainContent');
                const mobileMenu = document.getElementById('mobile-menu');

                function toggleOverlay(show) {
                    // Don't show search overlay if mobile menu is open
                    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                        return;
                    }

                    if (show) {
                        searchOverlay.classList.remove('hidden');
                        mainContent.classList.add('opacity-30');
                        mainContent.classList.add('transition-opacity');
                        mainContent.classList.add('duration-300');
                    } else {
                        if (document.activeElement !== searchInput) {
                            searchOverlay.classList.add('hidden');
                            mainContent.classList.remove('opacity-30');
                        }
                    }
                }

                // Make toggleOverlay available globally
                window.toggleOverlay = toggleOverlay;

                // Focus event
                searchInput.addEventListener('focus', () => {
                    toggleOverlay(true);
                });

                // Blur event
                searchInput.addEventListener('blur', () => {
                    setTimeout(() => toggleOverlay(false), 100);
                });

                // Form submission
                document.getElementById('searchForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    const query = searchInput.value.trim();
                    if (query) {
                        const searchUrl = "{{ route('retailers.search') }}";
                        window.location.href = `${searchUrl}?query=${encodeURIComponent(query)}`;
                    }
                });

                // Close overlay on click outside
                searchOverlay.addEventListener('click', () => {
                    searchInput.blur();
                    toggleOverlay(false);
                });
            });
        </script>
    @endpush

    <x-footer />
</x-app-layout>
