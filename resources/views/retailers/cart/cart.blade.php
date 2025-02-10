<x-app-layout>
    <x-dashboard-nav />

    <!-- Back Button -->
    <div class="container mx-auto px-4 py-6">
        <a href="{{route('retailers.dashboard')}}" class="text-green-600 hover:text-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <section class="container h-screen py-6 mx-auto bg-white">
        <h2 class="mb-4 pl-10 text-3xl font-bold">Order Cart</h2>
        <table class="w-full table-auto">
            <thead class="border border-gray-100">
                <tr>
                    <th class="px-4 py-2">Product</th>
                    <th class="px-4 py-2">Product Code</th>
                    <th class="px-4 py-2">Price</th>
                    <th class="px-4 py-2">Quantity</th>
                    <th class="px-4 py-2">Total</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center">This cart is empty.</td>
                </tr>
            </tbody>
        </table>

        <section class="container flex justify-end mx-auto mt-12 bg-white border">
            <div class="flex p-6 text-center">
                <h4 class="m-2 mr-5 text-lg font-semibold text-green-500">Total Amount: ₱0.00</h4>
                <button disabled class="px-6 py-2 text-lg font-bold text-white bg-gray-400 rounded-lg">
                    Proceed to Checkout
                </button>
            </div>
        </section>
    </section>

</x-app-layout>