<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-2 mx-auto">
            <h1 class="mb-6 text-3xl font-bold text-gray-800">My Products</h1>

            <!-- Search Bar -->
            <div class="flex items-center justify-between mb-6">
                <div class="w-full md:w-1/2 lg:w-1/3">
                    <form action="{{ route('distributors.products.index') }}" method="GET">
                        <div class="relative flex">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search products..."
                                class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="flex items-center justify-end space-x-4">
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

            @if (request('search'))
                <div class="mb-4">
                    <div class="flex items-center">
                        <p class="text-gray-600">Search results for: <span
                                class="font-bold">"{{ request('search') }}"</span></p>
                        <a href="{{ route('distributors.products.index') }}"
                            class="ml-3 text-sm text-blue-500 hover:underline">
                            Clear search
                        </a>
                    </div>
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
            <!-- Pagination Links -->
            <div class="flex justify-end mt-6 ">
                {{ $products->withQueryString()->links() }}
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

            <!-- Search Bar for Price Modal -->
            <div class="mb-4">
                <div class="relative flex">
                    <input type="text" id="priceModalSearch" placeholder="Search products..."
                        class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <button type="button" onclick="searchPriceModal()"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="priceModalSearchResults" class="hidden mb-2">
                <div class="flex items-center">
                    <p class="text-gray-600">Search results for: <span id="priceModalSearchTerm"
                            class="font-bold"></span></p>
                    <button onclick="clearPriceModalSearch()" class="ml-3 text-sm text-blue-500 hover:underline">
                        Clear search
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Image</th>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Product</th>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Price</th>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Last Update
                            </th>
                            <th class="px-4 py-2 text-sm font-medium text-left text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="priceModalTableBody" class="divide-y divide-gray-200">
                        <!-- Products will be loaded via JavaScript -->
                    </tbody>
                </table>

                <!-- Pagination controls -->
                <div class="flex items-center justify-between mt-4">
                    <div class="text-sm text-gray-700">
                        Showing <span id="priceModalCurrentRange">0-0</span> of <span id="priceModalTotal">0</span>
                        products
                    </div>
                    <div class="flex space-x-2">
                        <button id="priceModalPrevPage" onclick="changePriceModalPage('prev')"
                            class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                            disabled>
                            Previous
                        </button>
                        <span id="priceModalCurrentPage"
                            class="px-3 py-1 text-sm text-white bg-blue-500 rounded">1</span>
                        <button id="priceModalNextPage" onclick="changePriceModalPage('next')"
                            class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50">
                            Next
                        </button>
                    </div>
                </div>
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

            let priceModalProducts = [];
            let filteredProducts = [];
            const priceModalItemsPerPage = 5;
            let priceModalCurrentPage = 1;
            let priceModalSearchQuery = '';

            function openModal(id) {
                document.getElementById(id).classList.remove('hidden');

                if (id === 'priceModal') {
                    // Load products when price modal is opened
                    fetchProductsForPriceModal();
                }
            }

            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');

                // Reset search and pagination when modal is closed if it's the price modal
                if (id === 'priceModal') {
                    document.getElementById('priceModalSearch').value = '';
                    priceModalSearchQuery = '';
                    priceModalCurrentPage = 1;
                    document.getElementById('priceModalSearchResults').classList.add('hidden');
                }
            }

            function fetchProductsForPriceModal() {
                // Show loading state
                document.getElementById('priceModalTableBody').innerHTML =
                    '<tr><td colspan="5" class="px-4 py-3 text-center">Loading products...</td></tr>';

                fetch('/products/list')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        priceModalProducts = data;
                        filteredProducts = [...priceModalProducts];
                        updatePriceModalTable();
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        document.getElementById('priceModalTableBody').innerHTML =
                            '<tr><td colspan="5" class="px-4 py-3 text-center text-red-500">Failed to load products: ' +
                            error.message + '</td></tr>';
                    });
            }

            function searchPriceModal() {
                const searchInput = document.getElementById('priceModalSearch');
                priceModalSearchQuery = searchInput.value.trim().toLowerCase();

                if (priceModalSearchQuery) {
                    filteredProducts = priceModalProducts.filter(product =>
                        product.product_name.toLowerCase().includes(priceModalSearchQuery)
                    );

                    document.getElementById('priceModalSearchTerm').textContent = `"${priceModalSearchQuery}"`;
                    document.getElementById('priceModalSearchResults').classList.remove('hidden');
                } else {
                    filteredProducts = [...priceModalProducts];
                    document.getElementById('priceModalSearchResults').classList.add('hidden');
                }

                priceModalCurrentPage = 1;
                updatePriceModalTable();
            }

            function clearPriceModalSearch() {
                document.getElementById('priceModalSearch').value = '';
                priceModalSearchQuery = '';
                filteredProducts = [...priceModalProducts];
                document.getElementById('priceModalSearchResults').classList.add('hidden');
                priceModalCurrentPage = 1;
                updatePriceModalTable();
            }

            function changePriceModalPage(direction) {
                if (direction === 'prev' && priceModalCurrentPage > 1) {
                    priceModalCurrentPage--;
                } else if (direction === 'next' && priceModalCurrentPage < Math.ceil(filteredProducts.length /
                        priceModalItemsPerPage)) {
                    priceModalCurrentPage++;
                }

                updatePriceModalTable();
            }

            function updatePriceModalTable() {
                const tableBody = document.getElementById('priceModalTableBody');
                tableBody.innerHTML = '';

                const startIdx = (priceModalCurrentPage - 1) * priceModalItemsPerPage;
                const endIdx = Math.min(startIdx + priceModalItemsPerPage, filteredProducts.length);

                // Update pagination info
                document.getElementById('priceModalCurrentRange').textContent = filteredProducts.length > 0 ?
                    `${startIdx + 1}-${endIdx}` : '0-0';
                document.getElementById('priceModalTotal').textContent = filteredProducts.length;
                document.getElementById('priceModalCurrentPage').textContent = priceModalCurrentPage;

                // Enable/disable pagination buttons
                document.getElementById('priceModalPrevPage').disabled = priceModalCurrentPage === 1;
                document.getElementById('priceModalNextPage').disabled = priceModalCurrentPage >= Math.ceil(filteredProducts
                    .length / priceModalItemsPerPage);

                if (filteredProducts.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="px-4 py-2 text-center">No products found</td></tr>';
                    return;
                }

                // Generate table rows
                for (let i = startIdx; i < endIdx; i++) {
                    const product = filteredProducts[i];
                    const row = document.createElement('tr');

                    // Image cell
                    const imageCell = document.createElement('td');
                    imageCell.className = 'px-4 py-2';
                    imageCell.innerHTML = `
                    <img src="${product.image_url || '/img/default-product.jpg'}" alt="${product.product_name}"
                    class="object-cover w-12 h-12 rounded-lg">
                `;

                    // Name cell
                    const nameCell = document.createElement('td');
                    nameCell.className = 'px-4 py-2 text-sm text-gray-800';
                    nameCell.textContent = product.product_name;

                    // Price cell with form
                    const priceCell = document.createElement('td');
                    priceCell.className = 'px-4 py-2';
                    priceCell.innerHTML = `
                    <form action="/products/${product.id}/update-price" method="POST" class="flex items-center price-update-form">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="number" name="price" step="0.01" value="${product.price}" 
                            class="w-24 px-2 py-1 border rounded-md" required>
                    </form>
                `;

                    // Last update cell
                    const updateCell = document.createElement('td');
                    updateCell.className = 'px-4 py-2 text-sm text-gray-600';
                    updateCell.textContent = product.price_updated_at || 'N/A';

                    // Actions cell
                    const actionsCell = document.createElement('td');
                    actionsCell.className = 'px-4 py-2';
                    actionsCell.innerHTML = `
                    <button type="button" onclick="submitPriceForm(this)" data-product-id="${product.id}"
                        class="px-3 py-1 ml-2 text-sm text-white bg-green-500 rounded-md hover:bg-green-600">
                        Update
                    </button>
                `;

                    // Append cells to row
                    row.appendChild(imageCell);
                    row.appendChild(nameCell);
                    row.appendChild(priceCell);
                    row.appendChild(updateCell);
                    row.appendChild(actionsCell);

                    // Append row to table
                    tableBody.appendChild(row);
                }
            }

            function submitPriceForm(button) {
                const productId = button.getAttribute('data-product-id');
                console.log('Updating product ID:', productId);

                const row = button.closest('tr');
                const form = row.querySelector('.price-update-form');

                if (!form) {
                    console.error('Form not found');
                    return;
                }

                const formData = new FormData(form);
                const price = formData.get('price');
                console.log('Price to update:', price);

                // Show processing state
                button.disabled = true;
                button.textContent = 'Updating...';

                // Debug the form action
                console.log('Form action:', form.action);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        // Clone the response for debugging
                        return response.clone().text().then(text => {
                            console.log('Raw response:', text);
                            try {
                                return response.json();
                            } catch (e) {
                                throw new Error('Invalid JSON response from server');
                            }
                        });
                    })
                    .then(data => {
                        console.log('Parsed response data:', data);

                        if (data && (data.success || data.last_updated)) {
                            // Update the product in the arrays
                            const productIndex = priceModalProducts.findIndex(p => p.id == productId);
                            if (productIndex !== -1) {
                                priceModalProducts[productIndex].price = price;
                                priceModalProducts[productIndex].price_updated_at = data.last_updated;
                            }

                            const filteredIndex = filteredProducts.findIndex(p => p.id == productId);
                            if (filteredIndex !== -1) {
                                filteredProducts[filteredIndex].price = price;
                                filteredProducts[filteredIndex].price_updated_at = data.last_updated;
                            }

                            // Update the row with new data
                            const updateCell = row.querySelector('td:nth-child(4)');
                            if (updateCell) {
                                updateCell.textContent = data.last_updated;
                            }

                            // Show success state
                            button.textContent = 'Updated';
                            button.classList.remove('bg-green-500', 'hover:bg-green-600');
                            button.classList.add('bg-blue-500');

                            setTimeout(() => {
                                button.textContent = 'Update';
                                button.classList.add('bg-green-500', 'hover:bg-green-600');
                                button.classList.remove('bg-blue-500');
                                button.disabled = false;
                            }, 1500);
                        } else {
                            throw new Error(data?.message || 'Failed to update price: Unknown error');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating price:', error);
                        button.textContent = 'Failed';
                        button.classList.remove('bg-green-500', 'hover:bg-green-600');
                        button.classList.add('bg-red-500');

                        setTimeout(() => {
                            button.textContent = 'Update';
                            button.classList.add('bg-green-500', 'hover:bg-green-600');
                            button.classList.remove('bg-red-500');
                            button.disabled = false;
                        }, 1500);
                    });
            }

            // Initialize price modal search with Enter key support
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('priceModalSearch');
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchPriceModal();
                    }
                });
            });
        </script>
    @endpush
</x-distributor-layout>
