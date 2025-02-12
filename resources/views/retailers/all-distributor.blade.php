<x-app-layout>
    <div class="min-h-screen bg-gray-100">
        <x-retailer-topnav />

        <section class="py-16 bg-gray-50 mt-10 mb-24">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Our Trusted Distributors</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse ($distributors as $distributor)
                        <x-card-all-distributor route="{{ route('retailers.distributor-page', $distributor->id) }}"
                            imagepath="{{ asset('storage/distributors/jacob.png') }}" distributor_name="Jacob Trading"
                            distributor_desc="lorem ipsum" address="Guiwan">
                        </x-card-all-distributor>
                        <x-card-all-distributor route="{{ route('retailers.distributor-page', $distributor->id) }}"
                            imagepath="{{ asset('storage/distributors/jacob.png') }}" distributor_name="Jacob Trading"
                            distributor_desc="lorem ipsum" address="Guiwan">
                        </x-card-all-distributor>
                        <x-card-all-distributor route="{{ route('retailers.distributor-page', $distributor->id) }}"
                            imagepath="{{ asset('storage/distributors/jacob.png') }}" distributor_name="Jacob Trading"
                            distributor_desc="lorem ipsum" address="Guiwan">
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
