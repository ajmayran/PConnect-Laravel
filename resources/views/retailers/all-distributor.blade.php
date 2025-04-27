<x-app-layout>
    <div class="min-h-screen bg-gray-200">
        <x-retailer-topnav />
        
        <div class="relative z-50">
            <form action="{{ route('retailers.search') }}" method="GET" 
                class="max-w-2xl p-2 mx-auto sm:p-4">
                <div class="flex gap-0">
                    <div class="relative w-full">
                        <input type="search" 
                            name="query"
                            class="z-20 block w-full p-2 sm:p-3 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Search distributors..." 
                            required 
                            value="{{ request('query') }}" />
                        <button type="submit"
                            class="absolute top-0 h-full p-2 sm:p-3 text-sm font-medium text-white bg-green-500 border border-green-500 rounded-r-lg end-0 hover:bg-green-600 focus:ring-2 focus:outline-none focus:ring-green-300">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                            <span class="sr-only">Search</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <section class="py-8 sm:py-16 mt-4 sm:mt-10 mb-12 sm:mb-24 bg-gray-200">
            <div class="container px-4 sm:px-6 mx-auto">
                <h2 class="mb-6 sm:mb-10 text-2xl sm:text-3xl font-bold text-center text-gray-800">
                    Our Trusted Distributors
                </h2>
                
                <div class="grid grid-cols-1 gap-4 sm:gap-6 md:gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($distributors as $distributor)
                        <div class="transition-all duration-300 transform hover:scale-105">
                            <x-card-all-distributor 
                                route="{{ route('retailers.distributor-page', $distributor->id) }}"
                                imagepath="{{ $distributor->company_profile_image ? 
                                    asset('storage/' . $distributor->company_profile_image) : 
                                    asset('img/default-distributor.jpg') }}" 
                                distributor_name="{{ $distributor->company_name }}"
                                distributor_desc="{{ $distributor->description }}"
                                address="{{ $distributor->street}}">
                            </x-card-all-distributor>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 col-span-full p-4">
                            No distributors found.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
    <x-footer />
</x-app-layout>
