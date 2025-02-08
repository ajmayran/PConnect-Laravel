<x-app-layout>

    <x-dashboard-nav />

    <!-- Distributors Section -->
    <section class="py-8 mb-6 rounded-lg shadow-sm bg-gray-50">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="mr-4 text-2xl font-bold text-gray-800">Explore Distributors</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 py-10 md:grid-cols-5">
                <!-- Sample Distributor Cards -->
                <x-card-distributor distributor_name="Jacob Distribution" imagepath="storage/distributors/jacob.png" route="{{route('distributor.show')}}"></x-card-distributor>
                <x-card-distributor distributor_name="Primus Distributor" imagepath="storage/distributors/primus.png" route="null"></x-card-distributor>
                <x-card-distributor distributor_name="Glenmark Trading" imagepath="storage/distributors/glenmark.png" route="null"></x-card-distributor>
                
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5 bg-white">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between">
                <h2 class="mr-4 text-2xl font-bold">Popular Products</h2>
            </div>
            <div class="flex flex-wrap gap-4 py-10">
                <!-- Sample Product Cards -->
                <x-card-product imagepath="storage/products/rtc-chicken-bbq.png" product_name="Chicken BBQ" dist_name="Zambasulta" min_purchase_qty="5" stocks_remaining="300" price="250"></x-card-product>
                <x-card-product imagepath="img/softdrinks/coke_bottle.jpg" product_name="Coke mismo (12 pieces)" dist_name="Glenmark Trading" min_purchase_qty="10" stocks_remaining="90" price="130"></x-card-product>
                <x-card-product imagepath="storage/products/rtc-chicken-tocino.png" product_name="Chicken Tocino" dist_name="Zambasulta" min_purchase_qty="5" stocks_remaining="170" price="215"></x-card-product>
                <x-card-product imagepath="img/shampoo/pal3.jpg" product_name="Palmolive Coconut" dist_name="Primus" min_purchase_qty="10" stocks_remaining="160" price="120"></x-card-product>
            </div>
        </div>
    </section>
    <x-footer />
</x-app-layout>
