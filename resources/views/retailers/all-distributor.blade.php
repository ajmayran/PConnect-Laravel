<x-app-layout>
    <div class="min-h-screen bg-gray-100">
        <x-retailer-topnav />

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
