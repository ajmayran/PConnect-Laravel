{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\products\distributor.blade.php --}}
<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 ml-64 p-4">
            @if (session('success'))
                <div class="relative px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <div class="container px-4 py-8 mx-auto">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="px-6 py-4 bg-gray-800">
                        <h1 class="text-2xl font-bold text-white">{{ $distributor->company_name }} Products</h1>
                    </div>
                    <div class="p-6">
                        {{-- Responsive table container --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Product ID</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Product Image</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Product Name</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Description</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Price</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($products as $product)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->id }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                @if ($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" class="w-16 h-16 cursor-pointer" onclick="openModal('{{ asset('storage/' . $product->image) }}')">
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->product_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->description }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->price }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->stock_quantity }}</td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                                <form action="{{ route('admin.removeProduct', $product->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="openReasonModal('{{ $product->id }}')" class="font-medium text-red-600 hover:text-red-900">
                                                        Remove
                                                    </button>
                                                    <div id="reasonModal-{{ $product->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
                                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                            <div class="fixed inset-0 transition-opacity">
                                                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                                            </div>
                                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
                                                            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                                                                    <label for="reason" class="block mb-2 text-sm font-medium text-gray-700">Reason for removal</label>
                                                                    <textarea id="reason" name="reason" rows="4" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                                                                </div>
                                                                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                    <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-500 border border-transparent rounded-md shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Submit</button>
                                                                    <button type="button" onclick="closeReasonModal('{{ $product->id }}')" class="inline-flex justify-center w-full px-4 py-2 mt-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <img id="modalImage" src="" alt="Product Image" class="w-full h-auto">
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal()" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-500 border border-transparent rounded-md shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function openReasonModal(productId) {
            document.getElementById('reasonModal-' + productId).classList.remove('hidden');
        }

        function closeReasonModal(productId) {
            document.getElementById('reasonModal-' + productId).classList.add('hidden');
        }
    </script>
</x-app-layout>