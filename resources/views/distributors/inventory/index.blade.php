<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <h1 class="mb-6 text-2xl md:text-3xl font-bold">Inventory Management</h1>

        <!-- Search Bar -->
        <div class="flex items-center justify-between mb-6">
            <div class="w-full md:w-1/2 lg:w-1/3">
                <form action="{{ route('distributors.inventory.index') }}" method="GET">
                    <div class="relative flex">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search products..."
                            class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <button type="submit"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            <div class="flex items-center gap-3">
                <!-- Restock Alert Toggle Switch -->
                <div class="flex items-center gap-2">
                    <label for="restockAlert" class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" id="restockAlert" class="sr-only"
                                {{ session('restock_alert_enabled', true) ? 'checked' : '' }}
                                onchange="toggleRestockAlert(this.checked)">
                            <div class="block w-10 h-6 bg-gray-300 rounded-full"></div>
                            <div class="absolute w-4 h-4 transition bg-white rounded-full dot left-1 top-1"></div>
                        </div>
                        <div class="ml-2 text-sm font-medium text-gray-700">
                            <span id="restockAlertLabel">Restock Alerts</span>
                        </div>
                    </label>
                </div>

                <a href="{{ route('distributors.inventory.history') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Inventory History
                </a>
            </div>
        </div>

        <!-- Search Results Info -->
        @if (request('search'))
            <div class="mb-4">
                <div class="flex items-center">
                    <p class="text-gray-600">Search results for: <span
                            class="font-bold">"{{ request('search') }}"</span></p>
                    <a href="{{ route('distributors.inventory.index') }}"
                        class="ml-3 text-sm text-blue-500 hover:underline">
                        Clear search
                    </a>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Product</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Current Stock</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Last
                            Updated</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($products as $product)
                        <tr data-product-id="{{ $product->id }}"
                            data-is-batch="{{ $product->isBatchManaged() ? 'true' : 'false' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex-shrink-0 w-20 h-20">
                                    @if ($product->image && Storage::disk('public')->exists($product->image))
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->product_name }}"
                                            class="object-cover w-full h-full rounded-lg">
                                    @else
                                        <img src="{{ asset('img/default-product.jpg') }}" alt="Default Product Image"
                                            class="object-cover w-full h-full rounded-lg">
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $product->product_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 stock-quantity">
                                    @if ($product->isBatchManaged())
                                        {{ $product->batches()->sum('quantity') }}
                                    @else
                                        {{ $product->quantity ?? 0 }}
                                    @endif
                                </div>
                            </td>
                            <!-- Update the last updated cell -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 last-updated">
                                    {{ $product->stock_updated_at ? Carbon\Carbon::parse($product->stock_updated_at)->format('M d, Y H:i') : 'Never' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <button
                                        onclick="openStockModal(
                                        '{{ $product->id }}', 
                                        '{{ $product->product_name }}', 
                                        {{ $product->isBatchManaged() ? $product->batches()->sum('quantity') : $product->quantity ?? 0 }}, 
                                        {{ $product->isBatchManaged() ? 'true' : 'false' }}
                                    )"
                                        class="px-3 py-1 text-sm text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                        Update Stock
                                    </button>
                                    </button>
                                    @if ($product->isBatchManaged())
                                        <button
                                            onclick="viewBatches('{{ $product->id }}', '{{ $product->product_name }}')"
                                            class="px-3 py-1 text-sm text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                            View Batches
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="flex justify-end px-6 py-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Regular Stock Update Modal -->
    <div id="regularStockModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative w-full max-w-md p-6 bg-white rounded-lg">
                <h2 class="mb-4 text-xl font-bold" id="regularStockModalTitle">Update Stock</h2>
                <form id="regularStockForm" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium">Product Name</label>
                        <div id="regularProductName" class="text-gray-700"></div>
                    </div>

                    <div class="mb-4">
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="stock_type" value="in" checked
                                    class="w-4 h-4 text-blue-600 stock-type-radio">
                                <span class="ml-2 text-sm font-medium text-gray-700">Stock In</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="stock_type" value="out"
                                    class="w-4 h-4 text-blue-600 stock-type-radio">
                                <span class="ml-2 text-sm font-medium text-gray-700">Stock Out</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="block mb-2 text-sm font-medium">Quantity</label>
                        <input type="number" name="quantity" id="regularQuantity"
                            class="w-full px-3 py-2 border rounded-md" required min="1">
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="block mb-2 text-sm font-medium">Notes</label>
                        <textarea name="notes" id="regularNotes" rows="2" class="w-full px-3 py-2 border rounded-md"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('regularStockModal')"
                            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit" id="regularSubmitButton"
                            class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Batch Stock Update Modal -->
    <div id="batchStockModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative w-full max-w-md p-6 bg-white rounded-lg">
                <h2 class="mb-4 text-xl font-bold" id="batchStockModalTitle">Batch Stock Management</h2>
                <form id="batchStockForm" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium">Product Name</label>
                        <div id="batchProductName" class="text-gray-700"></div>
                    </div>

                    <div class="mb-4">
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="stock_type" value="in" checked
                                    class="w-4 h-4 text-blue-600 batch-stock-type-radio">
                                <span class="ml-2 text-sm font-medium text-gray-700">Stock In</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="stock_type" value="out"
                                    class="w-4 h-4 text-blue-600 batch-stock-type-radio">
                                <span class="ml-2 text-sm font-medium text-gray-700">Stock Out</span>
                            </label>
                        </div>
                    </div>

                    <div id="batchNumberContainer" class="mb-4">
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="block mb-2 text-sm font-medium">Quantity</label>
                        <input type="number" name="quantity" id="batchQuantity"
                            class="w-full px-3 py-2 border rounded-md" required min="1">
                    </div>

                    <!-- These containers will be shown/hidden based on selected stock action -->
                    <div id="batchInFields">
                        <div id="expiryDateContainer" class="mb-4">
                            <label for="expiry_date" class="block mb-2 text-sm font-medium">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiryDate"
                                class="w-full px-3 py-2 border rounded-md" required>
                        </div>

                        <div id="manufacturingDateContainer" class="mb-4">
                            <label for="manufacturing_date" class="block mb-2 text-sm font-medium">Manufactured Date
                            </label>
                            <input type="date" name="manufacturing_date" id="manufacturingDate"
                                class="w-full px-3 py-2 border rounded-md">
                        </div>

                        <div id="supplierContainer" class="mb-4">
                            <label for="supplier" class="block mb-2 text-sm font-medium">Supplier</label>
                            <input type="text" name="supplier" id="supplier"
                                class="w-full px-3 py-2 border rounded-md">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="block mb-2 text-sm font-medium">Notes</label>
                        <textarea name="notes" id="batchNotes" rows="2" class="w-full px-3 py-2 border rounded-md"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('batchStockModal')"
                            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit" id="batchSubmitButton"
                            class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                            Update Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Batch Details Modal -->
    <div id="batchDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative w-full max-w-4xl p-6 bg-white rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold" id="batchDetailsTitle">Batch Inventory</h2>
                    <button onclick="closeModal('batchDetailsModal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="p-4 rounded-lg bg-gray-50">
                        <h3 class="mb-2 font-semibold">Total Stock: <span id="totalBatchStock"
                                class="font-bold text-green-600">0</span></h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Batch #</th>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Quantity</th>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Expiry Date</th>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status</th>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Supplier</th>

                            </tr>
                        </thead>
                        <tbody id="batchesTableBody">
                            <!-- Batch data will be populated here -->
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="button" onclick="closeModal('batchDetailsModal')"
                        class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Set up initial default dates for batch expiration
                setDefaultDates();

                // Set up form submission handlers with explicit binding
                const regularForm = document.getElementById('regularStockForm');
                const batchForm = document.getElementById('batchStockForm');

                if (regularForm) {
                    regularForm.addEventListener('submit', handleFormSubmit);
                }

                if (batchForm) {
                    batchForm.addEventListener('submit', handleFormSubmit);
                    console.log('Batch form submit handler attached');
                }
            });

            function setDefaultDates() {
                // Set tomorrow as minimum expiry date
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.getElementById('expiryDate').min = tomorrow.toISOString().split('T')[0];

                // Set today as maximum manufacturing date
                const today = new Date();
                document.getElementById('manufacturingDate').max = today.toISOString().split('T')[0];
            }

            function openStockModal(productId, productName, currentStock, isBatchManaged) {
                console.log('Opening stock modal:', {
                    productId,
                    productName,
                    currentStock,
                    isBatchManaged
                });

                // Check if the value is actually being passed
                if (currentStock === undefined || currentStock === null) {
                    console.error('Current stock value is missing!');
                    currentStock = 0; // Provide a default
                }

                if (isBatchManaged) {
                    openBatchModal(productId, productName);
                } else {
                    // For regular products, directly access the modal and show it
                    const modal = document.getElementById('regularStockModal');
                    const form = document.getElementById('regularStockForm');

                    if (!modal || !form) {
                        console.error('Regular stock modal elements not found');
                        return;
                    }

                    // Reset form and set basic properties
                    form.reset();
                    form.action = `/inventory/${productId}/update-stock`;

                    // Set product name
                    document.getElementById('regularProductName').textContent = productName;

                    // Add current stock display with forced visibility
                    let currentStockDisplay = document.getElementById('currentStockDisplay');
                    if (!currentStockDisplay) {
                        currentStockDisplay = document.createElement('div');
                        currentStockDisplay.id = 'currentStockDisplay';
                        currentStockDisplay.className = 'mt-1 text-sm font-medium text-gray-600';
                        document.getElementById('regularProductName').parentNode.appendChild(currentStockDisplay);
                    }

                    // Make sure the stock is visible with clear formatting
                    currentStockDisplay.innerHTML = `<strong>Current Stock:</strong> ${currentStock}`;
                    currentStockDisplay.style.display = 'block';

                    document.getElementById('regularQuantity').value = '';

                    // Set the radio button default
                    const stockInRadio = form.querySelector('input[name="stock_type"][value="in"]');
                    const stockOutRadio = form.querySelector('input[name="stock_type"][value="out"]');

                    if (stockInRadio) {
                        stockInRadio.checked = true;
                    }

                    if (stockOutRadio) {
                        stockOutRadio.checked = false;
                    }

                    // Configure radio buttons for change events
                    setupRadioButtons(form, updateRegularForm);

                    // Initialize the form display based on the "in" selection
                    updateRegularForm();

                    // Make the modal visible
                    modal.classList.remove('hidden');
                    console.log('Regular stock modal should now be visible');
                }
            }

            function openRegularModal(productId, productName, currentStock) {
                const modal = document.getElementById('regularStockModal');
                const form = document.getElementById('regularStockForm');

                // Reset form and set basic properties
                form.reset();
                form.action = `/inventory/${productId}/update-stock`;

                // Set product name and add current stock info
                document.getElementById('regularProductName').textContent = productName;

                // Add current stock display - create the element if it doesn't exist
                let currentStockDisplay = document.getElementById('currentStockDisplay');
                if (!currentStockDisplay) {
                    currentStockDisplay = document.createElement('div');
                    currentStockDisplay.id = 'currentStockDisplay';
                    currentStockDisplay.className = 'mt-1 text-sm text-gray-600';
                    document.getElementById('regularProductName').parentNode.appendChild(currentStockDisplay);
                }
                currentStockDisplay.textContent = `Current Stock: ${currentStock}`;

                document.getElementById('regularQuantity').value = '';

                // Configure radio buttons
                setupRadioButtons(form, updateRegularForm);

                // Initial form update based on default selection (Stock In)
                updateRegularForm();

                // Show the modal
                modal.classList.remove('hidden');
            }

            function openBatchModal(productId, productName) {
                const modal = document.getElementById('batchStockModal');
                const form = document.getElementById('batchStockForm');

                // Reset form and set basic properties
                form.reset();
                form.action = `/inventory/${productId}/update-stock`;
                document.getElementById('batchProductName').textContent = productName;

                // Reset dates
                setDefaultDates();

                // Configure radio buttons
                setupRadioButtons(form, updateBatchForm);

                // Initial form update based on default selection (Stock In)
                updateBatchForm();

                // Show the modal
                modal.classList.remove('hidden');
            }


            function setupRadioButtons(form, updateCallback) {
                const radioButtons = form.querySelectorAll('input[type="radio"][name="stock_type"]');

                // Set "in" as default selection
                radioButtons.forEach(radio => {
                    radio.checked = radio.value === 'in';

                    // Remove existing listeners to prevent duplicates
                    radio.removeEventListener('change', updateCallback);
                    radio.addEventListener('change', updateCallback);
                });
            }

            function updateRegularForm() {
                const stockType = document.querySelector('#regularStockForm input[name="stock_type"]:checked').value;
                const submitButton = document.getElementById('regularSubmitButton');

                // Update button style based on stock type
                submitButton.className = stockType === 'in' ?
                    'px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600' :
                    'px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600';

                // Clear notes field
                document.getElementById('regularNotes').value = '';
            }

            // In the updateBatchForm function - update to properly disable required attribute when hidden

            function updateBatchForm() {
                const stockType = document.querySelector('#batchStockForm input[name="stock_type"]:checked').value;
                const submitButton = document.getElementById('batchSubmitButton');
                const batchInFields = document.getElementById('batchInFields');
                const batchNumberContainer = document.getElementById('batchNumberContainer');

                // Update button style based on stock type
                submitButton.className = stockType === 'in' ?
                    'px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600' :
                    'px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600';

                // Clear notes field
                document.getElementById('batchNotes').value = '';

                // Handle required attributes for batch fields
                const expiryDateField = document.getElementById('expiryDate');

                if (stockType === 'in') {
                    // For Stock In: show fields for new batch details and set required
                    batchInFields.classList.remove('hidden');
                    if (expiryDateField) expiryDateField.setAttribute('required', '');

                    batchNumberContainer.innerHTML = '';

                } else {
                    // For Stock Out: hide batch creation fields and remove required
                    batchInFields.classList.add('hidden');
                    if (expiryDateField) expiryDateField.removeAttribute('required');

                    batchNumberContainer.innerHTML = `
                    <label for="batch_number" class="block mb-2 text-sm font-medium">Select Batch</label>
                    <div class="p-3 border border-gray-200 rounded-md bg-gray-50">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-sm text-gray-600">Loading available batches...</p>
                        </div>
                    </div>
                `;

                    const productId = document.getElementById('batchStockForm').action.split('/').slice(-2)[0];
                    fetchAvailableBatches(productId);
                }
            }

            function fetchAvailableBatches(productId) {
                fetch(`/inventory/${productId}/batches`)
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to fetch batches');
                        return response.json();
                    })
                    .then(data => {
                        const batchNumberContainer = document.getElementById('batchNumberContainer');

                        if (!data.success) {
                            throw new Error(data.message || 'Failed to load batches');
                        }

                        if (data.batches.length === 0) {
                            batchNumberContainer.innerHTML = `
                    <div class="p-3 border border-yellow-100 rounded-md bg-yellow-50">
                        <p class="text-sm text-yellow-700">No batches available for this product.</p>
                    </div>
                `;
                            return;
                        }

                        // Create dropdown with available batches
                        let batchOptions = `
                <label for="batch_number" class="block mb-2 text-sm font-medium">Select Batch</label>
                <select name="batch_number" id="batchNumber" class="w-full px-3 py-2 border rounded-md" required>
            `;

                        data.batches.forEach(batch => {
                            batchOptions += `
                            <option value="${batch.batch_number}" data-quantity="${batch.quantity}">
                                Batch ${batch.batch_number} - Qty: ${batch.quantity} - Exp: ${batch.formatted_expiry_date}
                            </option>
                        `;
                        });

                        batchOptions += '</select>';
                        batchNumberContainer.innerHTML = batchOptions;

                        // Add event listener to limit quantity based on selected batch
                        const batchSelect = document.getElementById('batchNumber');
                        if (batchSelect) {
                            batchSelect.addEventListener('change', updateMaxQuantity);
                            updateMaxQuantity.call(batchSelect); // Set initial max quantity
                        }
                    })
                    .catch(error => {
                        console.error('Error loading batches:', error);
                        document.getElementById('batchNumberContainer').innerHTML = `
                <div class="p-3 border border-red-100 rounded-md bg-red-50">
                    <p class="text-sm text-red-600">Error loading batches: ${error.message}</p>
                </div>
            `;
                    });
            }

            function updateMaxQuantity() {
                const selectedOption = this.options[this.selectedIndex];
                const maxQty = selectedOption.dataset.quantity;
                const quantityInput = document.getElementById('batchQuantity');

                quantityInput.max = maxQty;
                quantityInput.value = Math.min(1, maxQty);
            }

            function viewBatches(productId, productName) {
                const modal = document.getElementById('batchDetailsModal');
                document.getElementById('batchDetailsTitle').textContent = `Batch Inventory: ${productName}`;

                // Show loading state
                document.getElementById('batchesTableBody').innerHTML = `
            <tr><td colspan="6" class="px-4 py-2 text-center">Loading batches...</td></tr>
        `;

                // Show the modal
                modal.classList.remove('hidden');

                // Fetch batch details
                fetch(`/inventory/${productId}/batches`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Failed to load batches');
                        }

                        let totalStock = 0;
                        let tableHtml = '';

                        if (data.batches.length === 0) {
                            tableHtml = '<tr><td colspan="6" class="px-4 py-2 text-center">No batches found</td></tr>';
                        } else {
                            data.batches.forEach(batch => {
                                totalStock += batch.quantity;

                                // Determine status style
                                let statusClass = 'bg-green-100 text-green-800';
                                let statusText = 'Good';

                                if (batch.expiry_status === 'expired') {
                                    statusClass = 'bg-red-100 text-red-800';
                                    statusText = 'Expired';
                                } else if (batch.expiry_status === 'expiring_soon') {
                                    statusClass = 'bg-yellow-100 text-yellow-800';
                                    statusText = 'Expiring Soon';
                                }

                                // Format date
                                const expiryDate = new Date(batch.expiry_date).toLocaleDateString();

                                tableHtml += `
                                <tr>
                                    <td class="px-4 py-2 border-b">${batch.batch_number}</td>
                                    <td class="px-4 py-2 border-b">${batch.quantity}</td>
                                    <td class="px-4 py-2 border-b">${batch.formatted_expiry_date}</td>
                                    <td class="px-4 py-2 border-b">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                                            ${statusText}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border-b">${batch.supplier || 'N/A'}</td>
                                </tr>
                                `;
                            });
                        }

                        document.getElementById('batchesTableBody').innerHTML = tableHtml;
                        document.getElementById('totalBatchStock').textContent = totalStock;
                    })
                    .catch(error => {
                        console.error('Error fetching batches:', error);
                        document.getElementById('batchesTableBody').innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-red-500">
                            Error loading batches: ${error.message}
                        </td>
                    </tr>
                `;
                    });
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                } else {
                    console.error(`Modal with ID "${modalId}" not found`);
                }
            }

            function handleFormSubmit(e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                formData.append('_method', 'PUT');

                const productId = form.action.split('/').slice(-2)[0];
                const submitButton = form.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.textContent;

                // Show processing state
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update the product row in the table
                            updateProductRow(productId, data.current_stock, data.last_updated);

                            // Close the modal - identify which one to close based on form ID
                            const modalId = form.id === 'batchStockForm' ? 'batchStockModal' : 'regularStockModal';
                            closeModal(modalId);

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            throw new Error(data.message || 'Failed to update stock');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to update stock'
                        });
                    })
                    .finally(() => {
                        // Reset button state
                        submitButton.disabled = false;
                        submitButton.textContent = originalButtonText;
                    });
            }

            function updateProductRow(productId, currentStock, lastUpdated) {
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (!row) {
                    console.error(`Row with product ID ${productId} not found`);
                    return;
                }

                // Update stock quantity by finding the div inside the cell
                const stockCell = row.querySelector('.stock-quantity');
                if (stockCell) {
                    console.log(`Updating stock for product ${productId} to ${currentStock}`);
                    stockCell.textContent = currentStock;
                } else {
                    console.error(`Stock cell not found for product ${productId}`);
                }

                // Update last updated timestamp by finding the div inside the cell
                const lastUpdatedCell = row.querySelector('.last-updated');
                if (lastUpdatedCell) {
                    console.log(`Updating timestamp for product ${productId} to ${lastUpdated}`);
                    lastUpdatedCell.textContent = lastUpdated;
                } else {
                    console.error(`Last updated cell not found for product ${productId}`);
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Setup the toggle switch styling
                const toggle = document.getElementById('restockAlert');
                const dot = document.querySelector('.dot');

                function updateToggleStyle(checked) {
                    if (checked) {
                        toggle.parentElement.querySelector('.block').classList.remove('bg-gray-300');
                        toggle.parentElement.querySelector('.block').classList.add('bg-green-500');
                        dot.classList.add('transform', 'translate-x-4');
                    } else {
                        toggle.parentElement.querySelector('.block').classList.add('bg-gray-300');
                        toggle.parentElement.querySelector('.block').classList.remove('bg-green-500');
                        dot.classList.remove('transform', 'translate-x-4');
                    }
                }

                // Initial style based on checked state
                updateToggleStyle(toggle.checked);

                // Update style on change
                toggle.addEventListener('change', function() {
                    updateToggleStyle(this.checked);
                });
            });

            function toggleRestockAlert(enabled) {
                fetch('{{ route("distributors.inventory.toggle-restock-alert") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            enabled
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Notification that the setting was saved
                            const message = enabled ? 'Restock alerts enabled' : 'Restock alerts disabled';

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    })
                    .catch(error => console.error('Error updating restock alert setting:', error));
            }
        </script>
    @endpush
</x-distributor-layout>
