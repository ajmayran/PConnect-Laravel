<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-2 mx-auto">
            <h1 class="mb-6 text-3xl font-bold text-gray-800">My Products</h1>
            <div class="flex items-center justify-end mb-6 space-x-4">
                <div class="flex items-center space-x-4">
                    <button onclick="openModal('priceModal')"
                        class="px-4 py-2 font-bold text-white transition duration-200 bg-blue-500 rounded-lg hover:bg-blue-600">
                        Product Prices
                    </button>
                </div>
                <div class="space-x-2">
                    <a href="{{ route('distributors.products.create') }}"
                        class="px-4 py-2 font-bold text-white transition duration-200 bg-green-500 rounded-lg hover:bg-green-600">
                        Add New Product
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-4 alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Products Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($products as $product)
                    <div class="overflow-hidden bg-white rounded-lg shadow-md">
                        <div class="relative">
                            @if ($product->image && Storage::disk('public')->exists($product->image))
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}"
                                    class="object-cover w-full h-48">
                            @else
                                <img src="{{ asset('img/default-product.jpg') }}" alt="Default Product Image"
                                    class="object-cover w-full h-48">
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $product->product_name }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ $categories->firstWhere('id', $product->category_id)->name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500">Min: {{ $product->minimum_purchase_qty }}</p>
                            <div class="flex mt-4 space-x-4">
                                <button onclick="openEditModal({{ $product->id }})"
                                    class="text-blue-500 hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.07a2.5 2.5 0 113.536 3.536L7 21H3v-4L16.732 3.464z" />
                                    </svg>
                                </button>
                                <form action="{{ route('distributors.products.destroy', $product->id) }}"
                                    method="POST" id="delete-form-{{ $product->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="confirmDelete('{{ $product->id }}', '{{ $product->product_name }}')"
                                        class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Price Modal -->
    <div id="priceModal"
        class="fixed inset-0 z-40 flex items-center justify-center hidden overflow-auto bg-black bg-opacity-50">
        <div class="w-full max-w-4xl max-h-screen p-6 overflow-y-auto bg-white rounded-lg shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold">Edit Product Prices</h2>
                <button onclick="closeModal('priceModal')"
                    class="px-4 py-2 text-white bg-gray-500 rounded-md hover:bg-gray-600">
                    Close
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Image</th>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Product</th>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Price</th>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Last Update</th>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-4 py-2">
                                    @if ($product->image && Storage::disk('public')->exists($product->image))
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->product_name }}"
                                            class="object-cover w-12 h-12 rounded-lg">
                                    @else
                                        <img src="{{ asset('img/default-product.jpg') }}" alt="Default Product Image"
                                            class="object-cover w-12 h-12 rounded-lg">
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-800">
                                    {{ $product->product_name }}
                                </td>
                                <td class="px-4 py-2">
                                    <form action="{{ route('distributors.products.updatePrice', $product->id) }}"
                                        method="POST" class="flex items-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="price" step="0.01"
                                            value="{{ $product->price }}" class="w-24 px-2 py-1 border rounded-md"
                                            required>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $product->price_updated_at ? \Carbon\Carbon::parse($product->price_updated_at)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-4 py-2">
                                    <button type="submit"
                                        class="px-3 py-1 ml-2 text-sm text-white bg-green-500 rounded-md hover:bg-green-600">
                                        Update
                                    </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative w-full max-w-4xl p-6 bg-white rounded-lg">
                <h2 class="mb-4 text-xl font-bold">Edit Product</h2>

                <form id="editProductForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="product_id" id="edit_product_id">
                    <!-- Tab Navigation -->
                    <div class="mb-4 border-b">
                        <div class="flex space-x-4">
                            <button type="button" data-tab="basicInfo"
                                class="px-4 py-2 text-blue-600 border-b-2 border-blue-600 tab-button">Basic
                                Info</button>
                            <button type="button" data-tab="specifications"
                                class="px-4 py-2 tab-button">Specifications</button>
                            <button type="button" data-tab="sales" class="px-4 py-2 tab-button">Sales</button>
                        </div>
                    </div>

                    <!-- Basic Info Tab -->
                    <div id="basicInfoTab" class="tab-content">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium">Product Image</label>
                                <div
                                    class="relative flex items-center justify-center border-2 border-dashed rounded-lg h-44">
                                    <img id="currentImage" src="" alt="Current product image"
                                        class="object-contain w-full h-full">
                                    <input type="file" name="image"
                                        class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium">Product Name</label>
                                    <input type="text" name="product_name"
                                        class="w-full px-3 py-2 border rounded-md" required>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium">Category</label>
                                    <select name="category_id" class="w-full px-3 py-2 border rounded-md" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium">Description</label>
                                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-md" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Specifications Tab -->
                    <div id="specificationsTab" class="hidden tab-content">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium">Brand</label>
                                <input type="text" name="brand" class="w-full px-3 py-2 border rounded-md"
                                    required>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium">SKU</label>
                                <input type="text" name="sku" class="w-full px-3 py-2 border rounded-md"
                                    required>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium">Weight (in kg)</label>
                                <input type="number" name="weight" step="0.01"
                                    class="w-full px-3 py-2 border rounded-md">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium">Tags</label>
                                <input type="text" name="tags" class="w-full px-3 py-2 border rounded-md"
                                    placeholder="Comma separated tags">
                            </div>
                        </div>
                    </div>

                    <!-- Sales Tab -->
                    <div id="salesTab" class="hidden tab-content">
                        <div>
                            <label class="block mb-2 text-sm font-medium">Minimum Purchase Quantity</label>
                            <input type="number" name="minimum_purchase_qty"
                                class="w-full px-3 py-2 border rounded-md" required>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end mt-6 space-x-2">
                        <button type="button" id="closeEditModal"
                            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">Update
                            Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const editModal = document.getElementById('editProductModal');
                const editForm = document.getElementById('editProductForm');
                const tabButtons = document.querySelectorAll('.tab-button');
                const tabContents = document.querySelectorAll('.tab-content');

                // Tab switching functionality
                tabButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const tabName = button.dataset.tab;

                        // Update button states
                        tabButtons.forEach(btn => {
                            btn.classList.remove('text-blue-600', 'border-b-2',
                                'border-blue-600');
                        });
                        button.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

                        // Show selected tab content
                        tabContents.forEach(content => {
                            content.classList.add('hidden');
                        });
                        document.getElementById(`${tabName}Tab`).classList.remove('hidden');
                    });
                });

                // Open modal function
                window.openEditModal = function(productId) {
                    // Fetch product data
                    fetch(`/products/${productId}/edit`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(product => {
                            // Update form action URL
                            editForm.action = `/products/${productId}`;
                            document.getElementById('edit_product_id').value = productId;

                            // Populate form fields
                            document.querySelector('input[name="product_name"]').value = product.product_name;
                            document.querySelector('textarea[name="description"]').value = product.description;
                            document.querySelector('select[name="category_id"]').value = product.category_id;
                            document.querySelector('input[name="brand"]').value = product.brand;
                            document.querySelector('input[name="sku"]').value = product.sku;
                            document.querySelector('input[name="weight"]').value = product.weight || '';
                            document.querySelector('input[name="tags"]').value = product.tags || '';
                            document.querySelector('input[name="minimum_purchase_qty"]').value = product
                                .minimum_purchase_qty;

                            // Update image preview if exists
                            const currentImage = document.getElementById('currentImage');
                            if (product.image) {
                                currentImage.src = `/storage/${product.image}`;
                                currentImage.classList.remove('hidden');
                            }

                            // Show modal
                            editModal.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to load product details'
                            });
                        });
                }

                // Close modal
                document.getElementById('closeEditModal').addEventListener('click', () => {
                    editModal.classList.add('hidden');
                });

                // Handle form submission
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    formData.append('_method', 'PUT'); // Add this line for PUT method

                    fetch(this.action, {
                            method: 'POST', // Keep this as POST since FormData handles the PUT method
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Product updated successfully',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error('Update failed');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update product'
                            });
                        });
                });
            });

            function openModal(id) {
                document.getElementById(id).classList.remove('hidden');
            }

            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');
            }

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
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait while we delete the product.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        document.getElementById('delete-form-' + productId).submit();
                    }
                });
            }
        </script>
    @endpush
</x-distributor-layout>
