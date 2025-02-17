<x-app-layout>
    <div class="min-h-screen bg-gray-100">
        <x-retailer-topnav />
        
        <form class="max-w-2xl mx-auto p-4">
            <div class="relative">
                <input 
                    type="search" 
                    id="search" 
                    name="search"
                    class="block p-3 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                    placeholder="Search distributors by name..." 
                    required 
                />
                <button 
                    type="submit" 
                    class="absolute top-0 end-0 h-full p-3 text-sm font-medium text-white bg-green-500 rounded-r-lg border border-green-500 hover:bg-green-600 focus:ring-2 focus:outline-none focus:ring-green-300"
                >
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                    <span class="sr-only">Search</span>
                </button>
            </div>
        </form>

        <section class="py-16 mt-10 mb-24 bg-gray-50">
            <div class="container px-6 mx-auto">
                <h2 class="mb-10 text-3xl font-bold text-center text-gray-800">Our Trusted Distributors</h2>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($distributors as $distributor)
                        <x-card-all-distributor route="{{ route('retailers.distributor-page', $distributor->id) }}"
                            imagepath="{{ $distributor->company_profile_image ? asset('storage/' . $distributor->company_profile_image) : asset('img/default-distributor.jpg') }}" 
                            distributor_name="{{ $distributor->company_name }}"
                            distributor_desc="{{ $distributor->description }}" 
                            address="{{ $distributor->company_address }}">
                        </x-card-all-distributor>
                    @empty
                        <div class="text-center text-gray-500 col-span-full">
                            No distributors found.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
