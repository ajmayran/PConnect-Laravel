<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Discounts & Promotions</h1>
            <a href="{{ route('distributors.discounts.create') }}" 
               class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                Create New Discount
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 mb-6 text-green-700 bg-green-100 border border-green-200 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-6 text-red-700 bg-red-100 border border-red-200 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden bg-white rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Name
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Type
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Details
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Period
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Status
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($discounts as $discount)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $discount->name }}
                                    </div>
                                    @if ($discount->code)
                                        <div class="text-xs text-gray-500">
                                            Code: {{ $discount->code }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold {{ $discount->type === 'percentage' ? 'text-green-800 bg-green-100' : 'text-blue-800 bg-blue-100' }} rounded-full">
                                        {{ $discount->type === 'percentage' ? 'Percentage Off' : 'Buy X Get Y Free' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($discount->type === 'percentage')
                                        <div class="text-sm text-gray-900">
                                            {{ $discount->percentage }}% off
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-900">
                                            Buy {{ $discount->buy_quantity }}, Get {{ $discount->free_quantity }} Free
                                        </div>
                                    @endif
                                    <div class="text-xs text-gray-500">
                                        Applied to {{ $discount->products->count() }} products
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $discount->start_date->format('M d, Y') }} - {{ $discount->end_date->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold {{ $discount->is_active && $discount->isValid() ? 'text-green-800 bg-green-100' : 'text-gray-800 bg-gray-100' }} rounded-full">
                                        {{ $discount->is_active && $discount->isValid() ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <button 
                                           onclick="openEditModal({{ $discount->id }})" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </button>
                                        <form action="{{ route('distributors.discounts.toggle', $discount) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-{{ $discount->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $discount->is_active ? 'yellow' : 'green' }}-900">
                                                {{ $discount->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <button 
                                           onclick="confirmDelete({{ $discount->id }}, '{{ $discount->name }}')" 
                                           class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                        <form id="delete-form-{{ $discount->id }}" action="{{ route('distributors.discounts.destroy', $discount) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No discounts found. Create your first discount now!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $discounts->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Discount Modal -->
    <div id="editDiscountModal" class="fixed inset-0 z-50 flex items-center justify-center hidden overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="relative w-full max-w-4xl mx-auto my-6">
            <!-- Modal content -->
            <div class="relative flex flex-col w-full bg-white border-0 rounded-lg shadow-lg outline-none focus:outline-none">
                <!-- Header -->
                <div class="flex items-start justify-between p-5 border-b border-gray-200 rounded-t">
                    <h3 class="text-2xl font-semibold">
                        Edit Discount
                    </h3>
                    <button class="float-right text-gray-500 hover:text-gray-800" onclick="closeEditModal()">
                        <span class="block w-6 h-6 text-2xl">×</span>
                    </button>
                </div>
                
                <!-- Modal body -->
                <div class="relative flex-auto p-6" id="editModalContent">
                    <div class="flex items-center justify-center">
                        <div class="w-12 h-12 border-4 border-t-4 border-gray-200 rounded-full border-t-blue-600 animate-spin"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal backdrop -->
    <div id="modalBackdrop" class="fixed inset-0 z-40 hidden bg-black opacity-25"></div>

    @push('scripts')
    <script>
        // Function to confirm discount deletion with SweetAlert
        function confirmDelete(discountId, discountName) {
            Swal.fire({
                title: 'Delete Discount?',
                html: `Are you sure you want to delete <strong>${discountName}</strong>?<br>This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${discountId}`).submit();
                }
            });
        }
        
        // Function to open the edit modal
        function openEditModal(discountId) {
            // Show the modal and backdrop
            document.getElementById('editDiscountModal').classList.remove('hidden');
            document.getElementById('modalBackdrop').classList.remove('hidden');
            
            // Fetch discount data
            fetch(`{{ url('discounts') }}/${discountId}/edit`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('editModalContent').innerHTML = html;
                    
                    // Initialize any JS components in the modal
                    initializeModalComponents();
                })
                .catch(error => {
                    console.error('Error fetching discount data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load discount data. Please try again.'
                    });
                    closeEditModal();
                });
        }
        
        // Function to close the edit modal
        function closeEditModal() {
            document.getElementById('editDiscountModal').classList.add('hidden');
            document.getElementById('modalBackdrop').classList.add('hidden');
            document.getElementById('editModalContent').innerHTML = '<div class="flex items-center justify-center"><div class="w-12 h-12 border-4 border-t-4 border-gray-200 rounded-full border-t-blue-600 animate-spin"></div></div>';
        }
        
        // Function to initialize components inside the modal
        function initializeModalComponents() {
            // Get the form in the modal
            const form = document.querySelector('#editModalContent form');
            
            if (form) {
                // Set up event listener for form submission
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form data
                    const formData = new FormData(form);
                    const url = form.getAttribute('action');
                    
                    // Add method override for PUT request
                    formData.append('_method', 'PUT');
                    
                    // Show loading state
                    Swal.fire({
                        title: 'Updating...',
                        text: 'Please wait while we update the discount.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit form via AJAX
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                        // Close modal and show success message
                        closeEditModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Discount updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Reload page to show updated data
                            window.location.reload();
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update discount. Please check the form and try again.'
                        });
                    });
                });
                
                // Set up discount type toggle behavior
                const typeRadios = document.querySelectorAll('input[name="type"]');
                const percentageSection = document.getElementById('percentage_section');
                const freebieSection = document.getElementById('freebie_section');
                
                if (typeRadios.length && percentageSection && freebieSection) {
                    typeRadios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            if (this.value === 'percentage') {
                                percentageSection.classList.remove('hidden');
                                freebieSection.classList.add('hidden');
                            } else {
                                percentageSection.classList.add('hidden');
                                freebieSection.classList.remove('hidden');
                            }
                        });
                    });
                }
            }
        }
        
        // Close modal when clicking outside
        document.getElementById('editDiscountModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        // Initialize SweetAlert for success/error messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000
            });
        @endif
    </script>
    @endpush
</x-distributor-layout>