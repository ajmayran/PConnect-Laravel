@php
    use Illuminate\Support\Facades\Storage;
@endphp


<x-app-layout>
    {{-- <p>Number of Products: {{ $products->count() }}</p> --}}
    <div class="container px-4 py-8 mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Your Products</h1>
            <a href="{{ route('distributors.products.create') }}"
                class="px-4 py-2 font-bold text-white transition duration-200 bg-green-500 rounded-lg hover:bg-green-600">
                Add New Product
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">ID
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Image
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Product</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Description</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Price
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Stock
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Min.Purchase</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Category</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $product->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($product->image && Storage::disk('public')->exists($product->image))
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->product_name }}"
                                        class="object-cover w-16 h-16 rounded-lg shadow">
                                @else
                                    <img src="{{ asset('img/default-product.jpg') }}" alt="Default Product Image"
                                        class="object-cover w-20 h-16 rounded-lg shadow">
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                {{ $product->product_name }}
                            </td>
                            <td class="max-w-xs px-6 py-4 text-sm text-gray-500 truncate">
                                {{ $product->description }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                ₱ {{ number_format($product->price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $product->stock_quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $product->minimum_purchase_qty }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $categories->firstWhere('id', $product->category_id)->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 space-x-2 text-sm font-medium whitespace-nowrap">
                                {{-- <a href="{{ route('distributors.products.show', $product->id) }}"
                                    class="inline-flex items-center px-3 py-1 text-sm font-medium text-white transition duration-200 bg-blue-500 rounded-md hover:bg-blue-600">
                                    View
                                </a> --}}
                                <a href="{{ route('distributors.products.edit', $product->id) }}"
                                    class="inline-flex items-center px-3 py-1 text-sm font-medium text-white transition duration-200 bg-yellow-500 rounded-md hover:bg-yellow-600">
                                    Edit
                                </a>
                                <form action="{{ route('distributors.products.destroy', $product->id) }}"
                                    method="POST" class="inline-block" id="delete-form-{{ $product->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="confirmDelete('{{ $product->id }}', '{{ $product->product_name }}')"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-white transition duration-200 bg-red-500 rounded-md hover:bg-red-600">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @push('scripts')
        <script>
            function confirmDelete(productId, productName) {
                Swal.fire({
                    title: 'Delete Product?',
                    html: `Are you sure you want to delete <strong>${productName}</strong>?<br>This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="mr-2 fas fa-trash-alt"></i>Yes, delete it!',
                    cancelButtonText: '<i class="mr-2 fas fa-times"></i>Cancel',
                    reverseButtons: true,
                    buttonsStyling: true,
                    customClass: {
                        confirmButton: 'swal2-confirm bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded mr-2',
                        cancelButton: 'swal2-cancel bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded',
                        title: 'text-xl font-bold text-gray-800',
                        popup: 'rounded-lg shadow-xl border-2 border-gray-100'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait while we delete the product.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the form
                        document.getElementById('delete-form-' + productId).submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
