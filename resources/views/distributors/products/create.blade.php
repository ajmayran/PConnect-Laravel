<x-distributor-layout>
    <div class="container max-w-6xl p-4 mx-auto overflow-y-auto">
        <h1 class="mb-6 text-2xl font-bold">Add New Product</h1>

        <a href="{{ route('distributors.products.index') }}"
            class="inline-block px-4 py-2 mb-4 text-sm font-medium text-gray-700 hover:text-green-400">← Back to
            Products</a>

        <form id="productForm" action="{{ route('distributors.products.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="distributor_id" value="{{ auth()->user()->id }}">

            <!-- Basic Information Card -->
            <div id="basicInfo" class="p-6 mb-6 bg-white rounded-lg shadow-md">
                <h2 class="mb-4 text-lg font-semibold">Basic Information</h2>
                <div class="space-y-4">
                    <!-- Image Upload -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium">Product Images</label>
                            <div class="space-y-4">
                                <div
                                    class="relative flex items-center justify-center w-full h-64 border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50">
                                    <!-- Image preview container -->
                                    <div id="imagePreview" class="absolute inset-0 hidden w-full h-full">
                                        <img src="" alt="Preview"
                                            class="object-contain w-full h-full rounded-lg">
                                        <button type="button" id="removeImage"
                                            class="absolute p-1 text-white bg-red-500 rounded-full top-2 right-2 hover:bg-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- Upload placeholder -->
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500">Click to upload product image</p>
                                    </div>
                                    <input type="file" name="image"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*"
                                        required />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium">Product Name</label>
                                <input type="text" name="product_name" class="w-full px-3 py-2 border rounded-md"
                                    oninput="capitalizeFirst(this)" required>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium">Category</label>
                                <select name="category_id" id="category_id" class="w-full px-3 py-2 border rounded-md"
                                    required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-md" oninput="capitalizeFirst(this)"
                                    required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-right">
                    <button type="button" id="nextToSpecs"
                        class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">Next:
                        Specifications</button>
                </div>
            </div>

            <!-- Specifications Card (Initially Hidden) -->
            <div id="specifications" class="hidden p-6 mb-6 bg-white rounded-lg shadow-md">
                <h2 class="mb-4 text-lg font-semibold">Product Specifications</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium">Brand</label>
                        <input type="text" name="brand" class="w-full px-3 py-2 border rounded-md"
                            oninput="capitalizeFirst(this)" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">SKU</label>
                        <input type="text" name="sku" class="w-full px-3 py-2 border rounded-md"
                            oninput="capitalizeFirst(this)" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">Weight (in kg)</label>
                        <input type="number" name="weight" step="0.01" class="w-full px-3 py-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">Tags</label>
                        <input type="text" name="tags" class="w-full px-3 py-2 border rounded-md"
                            placeholder="Comma separated tags" oninput="capitalizeCommaSeparated(this)">
                    </div>
                </div>
                <div class="flex justify-between mt-4">
                    <button type="button" id="backToBasic"
                        class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">Back</button>
                    <div class="space-x-2">
                        <!-- Partial Save Button -->
                        <button type="submit" id="partialSave"
                            class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">Save
                            Product</button>
                        <!-- Navigation to Sales Information -->
                        <button type="button" id="nextToSales"
                            class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">Next: Sales
                            Information</button>
                    </div>
                </div>
            </div>

            <!-- Sales Information Card (Initially Hidden) -->
            <div id="salesInfo" class="hidden p-6 mb-6 bg-white rounded-lg shadow-md">
                <h2 class="mb-4 text-lg font-semibold">Sales Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium">Price (₱)</label>
                        <input type="number" name="price" step="0.01"
                            class="w-full px-3 py-2 border rounded-md" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">Minimum Purchase Quantity</label>
                        <input type="number" name="minimum_purchase_qty" class="w-full px-3 py-2 border rounded-md"
                            required>
                    </div>

                    <!-- Stock Quantity Field - will be shown or hidden based on category -->
                    <div id="stockQuantityField">
                        <label class="block mb-2 text-sm font-medium">Stock Quantity</label>
                        <input type="number" name="stock_quantity" class="w-full px-3 py-2 border rounded-md"
                            min="0" required>
                    </div>
                    <!-- Batch Info Message - will be shown or hidden based on category -->
                    <div id="batchInfoMessage" class="hidden col-span-2">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-gray-600">The Stock of this Category will be managed through batch
                                inventory after
                                product creation.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-4">
                    <button type="button" id="backToSpecs"
                        class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">Back</button>
                    <button type="submit" id="completeSave"
                        class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">Save Product</button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // DOM Elements
                const productForm = document.getElementById('productForm');
                const categorySelect = document.getElementById('category_id');
                const stockQuantityField = document.getElementById('stockQuantityField');
                const batchInfoMessage = document.getElementById('batchInfoMessage');
                const stockQuantityInput = document.querySelector('input[name="stock_quantity"]');
                const imageInput = document.querySelector('input[name="image"]');
                const imagePreview = document.getElementById('imagePreview');
                let partialSaveTriggered = false;

                // Batch categories definition
                const batchCategories = [
                    'Ready To Cook',
                    'Beverages',
                    'Instant Products',
                    'Snacks',
                    'Sauces & Condiments',
                    'Juices & Concentrates',
                    'Powdered Products',
                    'Frozen Products',
                    'Dairy Products'
                ];

                function isBatchCategory() {
                    if (!categorySelect) return false;
                    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                    if (!selectedOption || !selectedOption.text) return false;
                    return batchCategories.includes(selectedOption.text);
                }

                function updateCategoryBasedUI() {
                    if (!stockQuantityField || !batchInfoMessage || !stockQuantityInput) return;
                    if (isBatchCategory()) {
                        stockQuantityField.classList.add('hidden');
                        batchInfoMessage.classList.remove('hidden');
                        stockQuantityInput.value = '0';
                        stockQuantityInput.removeAttribute('required');
                    } else {
                        stockQuantityField.classList.remove('hidden');
                        batchInfoMessage.classList.add('hidden');
                        stockQuantityInput.setAttribute('required', 'required');
                        if (stockQuantityInput.value === '0') stockQuantityInput.value = '';
                    }
                }

                function showError(message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot Proceed',
                        html: message.replace(/\n/g, '<br>'),
                        confirmButtonColor: '#3085d6'
                    });
                }

                function validateBasicInfo() {
                    const productName = document.querySelector('input[name="product_name"]').value.trim();
                    const category = categorySelect.value;
                    const description = document.querySelector('textarea[name="description"]').value.trim();
                    const image = imageInput.files[0];
                    const errors = [];
                    if (!productName) errors.push('Product name is required');
                    if (!category) errors.push('Category is required');
                    if (!description) errors.push('Description is required');
                    if (!image) errors.push('Product image is required');
                    if (errors.length) {
                        showError(errors.join('\n'));
                        return false;
                    }
                    return true;
                }

                function validateSpecifications() {
                    const brand = document.querySelector('input[name="brand"]').value.trim();
                    const sku = document.querySelector('input[name="sku"]').value.trim();
                    const errors = [];
                    if (!brand) errors.push('Brand is required');
                    if (!sku) errors.push('SKU is required');
                    if (errors.length) {
                        showError(errors.join('\n'));
                        return false;
                    }
                    return true;
                }

                function validateSalesInfo() {
                    const price = document.querySelector('input[name="price"]').value;
                    const minPurchase = document.querySelector('input[name="minimum_purchase_qty"]').value;
                    const errors = [];
                    if (!price || price <= 0) errors.push('Valid price is required');
                    if (!minPurchase || minPurchase <= 0) errors.push('Valid minimum purchase quantity is required');
                    if (!isBatchCategory()) {
                        const stockQty = stockQuantityInput.value;
                        if (!stockQty || stockQty <= 0) errors.push(
                            'Stock quantity must be greater than 0 for non-batch products');
                    }
                    if (errors.length) {
                        showError(errors.join('\n'));
                        return false;
                    }
                    return true;
                }

                // Image preview
                if (imageInput) {
                    imageInput.addEventListener('change', function(e) {
                        if (e.target.files && e.target.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                imagePreview.querySelector('img').src = e.target.result;
                                imagePreview.classList.remove('hidden');
                            }
                            reader.readAsDataURL(e.target.files[0]);
                        }
                    });
                }
                document.getElementById('removeImage').addEventListener('click', function() {
                    imageInput.value = "";
                    imagePreview.querySelector('img').src = "";
                    imagePreview.classList.add('hidden');
                });

                // Category change handler
                if (categorySelect) categorySelect.addEventListener('change', updateCategoryBasedUI);

                // Navigation buttons
                document.getElementById('nextToSpecs').addEventListener('click', function() {
                    if (validateBasicInfo()) {
                        document.getElementById('basicInfo').classList.add('hidden');
                        document.getElementById('specifications').classList.remove('hidden');
                    }
                });
                document.getElementById('backToBasic').addEventListener('click', function() {
                    document.getElementById('specifications').classList.add('hidden');
                    document.getElementById('basicInfo').classList.remove('hidden');
                });
                document.getElementById('nextToSales').addEventListener('click', function() {
                    if (validateSpecifications()) {
                        document.getElementById('specifications').classList.add('hidden');
                        document.getElementById('salesInfo').classList.remove('hidden');
                        updateCategoryBasedUI();
                    }
                });
                document.getElementById('backToSpecs').addEventListener('click', function() {
                    document.getElementById('salesInfo').classList.add('hidden');
                    document.getElementById('specifications').classList.remove('hidden');
                });

                // Save buttons
                document.getElementById('completeSave').addEventListener('click', function(e) {
                    e.preventDefault();
                    partialSaveTriggered = false;
                    if (isBatchCategory()) stockQuantityInput.value = '0';
                    if (validateSalesInfo()) {
                        // Remove any partial save marker
                        const existingHiddenInput = document.querySelector('input[name="save_product"]');
                        if (existingHiddenInput) existingHiddenInput.remove();
                        productForm.submit();
                    }
                });

                document.getElementById('partialSave').addEventListener('click', function(e) {
                    e.preventDefault();
                    partialSaveTriggered = true;
                    if (validateBasicInfo() && validateSpecifications()) {
                        if (isBatchCategory()) {
                            stockQuantityInput.value = '0';
                        } else if (!stockQuantityInput.value || stockQuantityInput.value < 0) {
                            stockQuantityInput.value = '0';
                        }
                        let hiddenInput = document.querySelector('input[name="save_product"]');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'save_product';
                            hiddenInput.value = '1';
                            productForm.appendChild(hiddenInput);
                        }
                        productForm.submit();
                    }
                });

                // Initial UI setup
                updateCategoryBasedUI();
            });
        </script>
    @endpush
</x-distributor-layout>
