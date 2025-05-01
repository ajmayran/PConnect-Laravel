<form id="editDiscountForm" action="{{ route('distributors.discounts.update', $discount) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mt-2">
        <h3 class="text-lg font-medium text-gray-700">Apply Discount to Products</h3>
        <p class="text-sm text-gray-500">Select products that this discount will apply to.</p>

        <div class="p-4 mt-4 space-y-4 overflow-y-auto border border-gray-200 rounded-md max-h-96">
            @foreach ($products as $product)
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="product_ids[]" id="product_{{ $product->id }}"
                            value="{{ $product->id }}"
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                            {{ in_array($product->id, $selectedProductIds) ? 'checked' : '' }}>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="product_{{ $product->id }}"
                            class="font-medium text-gray-700">{{ $product->product_name }}</label>
                        <p class="text-gray-500">Price: ₱{{ number_format($product->price, 2) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="hidden mt-1 text-sm text-red-600" id="product_ids_error"></p>
    </div>

    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Discount Name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $discount->name) }}"
            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
    </div>

    <div class="mb-4">
        <label for="type" class="block text-sm font-medium text-gray-700">Discount Type</label>
        <select name="type" id="type" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
            <option value="percentage" {{ old('type', $discount->type) === 'percentage' ? 'selected' : '' }}>Percentage Off</option>
            <option value="freebie" {{ old('type', $discount->type) === 'freebie' ? 'selected' : '' }}>Buy X Get Y Free</option>
        </select>
    </div>

    <div id="percentage_section" class="{{ old('type', $discount->type) === 'percentage' ? '' : 'hidden' }}">
        <div class="mb-4">
            <label for="percentage" class="block text-sm font-medium text-gray-700">Percentage</label>
            <input type="number" name="percentage" id="percentage" value="{{ old('percentage', $discount->percentage) }}"
                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
        </div>
    </div>

    <div id="freebie_section" class="{{ old('type', $discount->type) === 'freebie' ? '' : 'hidden' }}">
        <div class="mb-4">
            <label for="buy_quantity" class="block text-sm font-medium text-gray-700">Buy Quantity</label>
            <input type="number" name="buy_quantity" id="buy_quantity" value="{{ old('buy_quantity', $discount->buy_quantity) }}"
                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
        </div>
        <div class="mb-4">
            <label for="free_quantity" class="block text-sm font-medium text-gray-700">Free Quantity</label>
            <input type="number" name="free_quantity" id="free_quantity" value="{{ old('free_quantity', $discount->free_quantity) }}"
                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
        </div>
    </div>

    <div class="mb-4">
        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $discount->start_date->format('Y-m-d')) }}"
            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
    </div>

    <div class="mb-4">
        <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $discount->start_date->format('H:i')) }}"
            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
    </div>

    <div class="mb-4">
        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $discount->end_date->format('Y-m-d')) }}"
            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
    </div>

    <div class="mb-4">
        <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $discount->end_date->format('H:i')) }}"
            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
    </div>

    <div class="mb-4">
        <div class="flex items-center">
            <input type="checkbox" name="is_active" id="is_active"
                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
            <label for="is_active" class="block ml-2 text-sm text-gray-700">Active Discount</label>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">Update</button>
        <button type="button" onclick="closeEditModal()"
            class="px-4 py-2 ml-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
    </div>
</form>

<script>
    document.getElementById('editDiscountForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach(el => el.remove());

        // Show loading state
        Swal.fire({
            title: 'Processing...',
            text: 'Updating discount',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Get form data
        const formData = new FormData(this);

        // Send AJAX request
        fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                return response.json().then(data => {
                    return {
                        ...data,
                        status: response.status
                    };
                });
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        closeEditModal();
                        window.location.reload(); // Reload to see the updated data
                    });
                } else {
                    if (data.status === 422 && data.errors) {
                        Swal.close();
                        Object.keys(data.errors).forEach(field => {
                            const inputField = document.getElementById(field);
                            if (inputField) {
                                const errorMsg = document.createElement('p');
                                errorMsg.className = 'error-message mt-1 text-sm text-red-600';
                                errorMsg.textContent = data.errors[field][0];
                                inputField.parentNode.appendChild(errorMsg);
                            }
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please check the form for errors'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Failed to update discount'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An unexpected error occurred during the request'
                });
            });
    });

    // Handle discount type changes
    document.getElementById('type').addEventListener('change', function() {
        const type = this.value;
        const percentageSection = document.getElementById('percentage_section');
        const freebieSection = document.getElementById('freebie_section');

        if (type === 'percentage') {
            percentageSection.classList.remove('hidden');
            freebieSection.classList.add('hidden');
        } else {
            percentageSection.classList.add('hidden');
            freebieSection.classList.remove('hidden');
        }
    });
</script>
