<x-distributor-layout>
    <div class="container max-w-6xl p-4 mx-auto overflow-y-auto">
        <h1 class="mb-6 text-2xl font-bold">Add New Product</h1>
        
        <a href="{{ route('distributors.products.index') }}" class="inline-block px-4 py-2 mb-4 text-sm font-medium text-gray-700 hover:text-green-400">← Back to Products</a>
        
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
                                    required>
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
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-md" required></textarea>
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
                        <input type="text" name="brand" class="w-full px-3 py-2 border rounded-md" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">SKU</label>
                        <input type="text" name="sku" class="w-full px-3 py-2 border rounded-md" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">Weight (in kg)</label>
                        <input type="number" name="weight" step="0.01" class="w-full px-3 py-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">Tags</label>
                        <input type="text" name="tags" class="w-full px-3 py-2 border rounded-md"
                            placeholder="Comma separated tags">
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
                        <label class="block mb-2 text-sm font-medium">Stock Quantity</label>
                        <input type="number" name="stock_quantity" class="w-full px-3 py-2 border rounded-md"
                            min="1" max="9999" oninput="this.value = this.value > 9999 ? 9999 : this.value" 
                            required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium">Minimum Purchase Quantity</label>
                        <input type="number" name="minimum_purchase_qty" class="w-full px-3 py-2 border rounded-md"
                            required>
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
                let partialSaveTriggered = false;
                const basicInfo = document.getElementById('basicInfo');
                const specifications = document.getElementById('specifications');
                const salesInfo = document.getElementById('salesInfo');
                const productForm = document.getElementById('productForm');

                // Image preview functionality
                const imageInput = document.querySelector('input[name="image"]');
                const imagePreview = document.getElementById('imagePreview');
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

                // Remove image preview
                document.getElementById('removeImage').addEventListener('click', function() {
                    imageInput.value = "";
                    imagePreview.querySelector('img').src = "";
                    imagePreview.classList.add('hidden');
                });

                function showError(message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: message.replace(/\n/g, '<br>'),
                        confirmButtonColor: '#3085d6'
                    });
                }

                function validateBasicInfo() {
                    const productName = document.querySelector('input[name="product_name"]').value.trim();
                    const category = document.querySelector('select[name="category_id"]').value;
                    const description = document.querySelector('textarea[name="description"]').value.trim();
                    const image = imageInput.files[0];

                    let errors = [];
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
                    let errors = [];
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
                    const stockQuantity = document.querySelector('input[name="stock_quantity"]').value;
                    const minPurchase = document.querySelector('input[name="minimum_purchase_qty"]').value;
                    let errors = [];
                    if (!price || price <= 0) errors.push('Valid price is required');
                    if (!stockQuantity || stockQuantity <= 0) errors.push('Valid stock quantity is required');
                    if (!minPurchase || minPurchase <= 0) errors.push('Valid minimum purchase quantity is required');

                    if (errors.length) {
                        showError(errors.join('\n'));
                        return false;
                    }
                    return true;
                }

                // Navigation events between cards
                document.getElementById('nextToSpecs').addEventListener('click', function() {
                    if (validateBasicInfo()) {
                        basicInfo.classList.add('hidden');
                        specifications.classList.remove('hidden');
                    }
                });

                document.getElementById('backToBasic').addEventListener('click', function() {
                    specifications.classList.add('hidden');
                    basicInfo.classList.remove('hidden');
                });

                document.getElementById('nextToSales').addEventListener('click', function() {
                    if (validateSpecifications()) {
                        specifications.classList.add('hidden');
                        salesInfo.classList.remove('hidden');
                        Swal.fire({
                            icon: 'success',
                            title: 'Specifications Saved',
                            text: 'Please complete the sales information',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });

                document.getElementById('backToSpecs').addEventListener('click', function() {
                    salesInfo.classList.add('hidden');
                    specifications.classList.remove('hidden');
                });

                // Form submission handling
                productForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Add hidden input for partial save if triggered
                    if (partialSaveTriggered) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'save_product';
                        hiddenInput.value = '1';
                        this.appendChild(hiddenInput);
                    }

                    if (!validateBasicInfo() || !validateSpecifications()) {
                        return;
                    }

                    if (partialSaveTriggered) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Product Saved',
                            text: 'Product saved successfully (partial save).',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            this.submit();
                        });
                    } else {
                        if (!validateSalesInfo()) {
                            return;
                        }
                        Swal.fire({
                            title: 'Saving Product',
                            text: 'Please wait while we save your product...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        this.submit();
                    }
                });

                // Update the partial save button click handler
                document.getElementById('partialSave').addEventListener('click', function() {
                    partialSaveTriggered = true;
                    productForm.dispatchEvent(new Event('submit'));
                });
            });
        </script>
    @endpush
</x-distributor-layout>
