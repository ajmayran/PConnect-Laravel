<x-app-layout>
    <x-dashboard-nav />
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Delivery Addresses') }}
        </h2>
    </x-slot>

    <div class="flex py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <x-retailer-sidebar :user="Auth::user()" /> <!-- Retailer Sidebar -->

        <div class="flex-1 space-y-6 lg:pl-8">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Delivery Addresses</h1>
                <div>
                    <span class="text-sm text-gray-500">Manage your delivery addresses (max 3)</span>
                </div>
            </div>
            
            @if(session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500 rounded-md">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded-md">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            <!-- Current Addresses -->
            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <h2 class="mb-6 text-lg font-medium text-gray-900">Your Addresses</h2>
                
                @if($addresses->isEmpty())
                    <div class="p-4 mb-4 text-gray-700 bg-gray-100 rounded-md">
                        <p>You don't have any saved addresses yet. Add one below.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($addresses as $address)
                            <div class="p-4 border rounded-lg {{ $address->is_default ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center">
                                            <h3 class="text-lg font-medium">{{ $address->label }}</h3>
                                            @if($address->is_default)
                                                <span class="px-2 py-1 ml-2 text-xs font-medium text-white bg-green-500 rounded-full">Default</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-gray-600">{{ $address->barangay_name }}</p>
                                        <p class="text-gray-600">{{ $address->street }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="openEditModal({{ $address->id }}, '{{ $address->label }}', '{{ $address->barangay }}', '{{ addslashes($address->street) }}', {{ $address->is_default ? 'true' : 'false' }})" 
                                            class="text-blue-600 hover:text-blue-800">
                                            Edit
                                        </button>
                                        @if(!$address->is_default)
                                            <form action="{{ route('retailers.address.set-default', $address) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:text-green-800">
                                                    Set as Default
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('retailers.address.destroy', $address) }}" method="POST" class="inline" 
                                            onsubmit="return confirm('Are you sure you want to delete this address?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Add New Address -->
            @if($addresses->count() < 3)
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <h2 class="mb-6 text-lg font-medium text-gray-900">Add New Address</h2>
                    
                    <form action="{{ route('retailers.address.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="label" class="block text-sm font-medium text-gray-700">Address Label</label>
                                <input type="text" id="label" name="label" value="{{ old('label') }}" required
                                    placeholder="e.g. Home, Work, Shop" 
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                                @error('label')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay</label>
                                <select id="barangay" name="barangay" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">Select Barangay</option>
                                    @foreach($barangays as $barangay)
                                        <option value="{{ $barangay->code }}" {{ old('barangay') == $barangay->code ? 'selected' : '' }}>
                                            {{ $barangay->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('barangay')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="street" class="block text-sm font-medium text-gray-700">Street Address</label>
                                <textarea id="street" name="street" rows="2" required
                                    placeholder="House/Building number, Street name, etc." 
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('street') }}</textarea>
                                @error('street')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_default" name="is_default" value="1" 
                                        {{ old('is_default') || $addresses->isEmpty() ? 'checked' : '' }}
                                        class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                    <label for="is_default" class="block ml-2 text-sm font-medium text-gray-700">
                                        Set as default delivery address
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Add Address
                            </button>
                        </div>
                    </form>
                </div>
            @endif
            
            <!-- Edit Address Modal -->
            <div id="editAddressModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
                <div class="w-full max-w-md p-6 bg-white rounded-lg">
                    <h2 class="mb-4 text-lg font-medium text-gray-900">Edit Address</h2>
                    
                    <form id="editAddressForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="edit_label" class="block text-sm font-medium text-gray-700">Address Label</label>
                                <input type="text" id="edit_label" name="label" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                            </div>
                            
                            <div>
                                <label for="edit_barangay" class="block text-sm font-medium text-gray-700">Barangay</label>
                                <select id="edit_barangay" name="barangay" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">Select Barangay</option>
                                    @foreach($barangays as $barangay)
                                        <option value="{{ $barangay->code }}">{{ $barangay->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="edit_street" class="block text-sm font-medium text-gray-700">Street Address</label>
                                <textarea id="edit_street" name="street" rows="2" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="edit_is_default" name="is_default" value="1" 
                                    class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <label for="edit_is_default" class="block ml-2 text-sm font-medium text-gray-700">
                                    Set as default delivery address
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6 space-x-3">
                            <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function openEditModal(id, label, barangayCode, street, isDefault) {
            document.getElementById('edit_label').value = label;
            document.getElementById('edit_barangay').value = barangayCode;
            document.getElementById('edit_street').value = street;
            document.getElementById('edit_is_default').checked = isDefault;
            
            document.getElementById('editAddressForm').action = `/retailers/address/${id}`;
            document.getElementById('editAddressModal').classList.remove('hidden');
        }
        
        function closeEditModal() {
            document.getElementById('editAddressModal').classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.getElementById('editAddressModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</x-app-layout>
<x-footer />