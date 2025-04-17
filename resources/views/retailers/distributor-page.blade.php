<x-app-layout>
    <x-dashboard-nav />

    <!-- Back Button -->
    <div class="px-4 py-2 sm:px-6">
        <a href="{{ route('retailers.dashboard') }}"
            class="inline-flex items-center text-sm text-gray-600 sm:text-base hover:text-gray-800">
            <svg class="w-4 h-4 mr-2 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Distributor Header -->
    <section class="container p-4 mx-auto mb-4 bg-white rounded-lg shadow-lg sm:p-8 sm:mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
            <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:items-start sm:text-left sm:gap-6">
                <img class="object-cover w-20 h-20 rounded-full shadow-lg sm:w-24 sm:h-24"
                    src="{{ $distributor->company_profile_image ? asset('storage/' . $distributor->company_profile_image) : asset('img/default-distributor.jpg') }}"
                    alt="{{ $distributor->company_name }}">
                <div>
                    <h1 class="text-xl font-bold text-gray-800 sm:text-2xl">{{ $distributor->company_name }}</h1>
                    <p class="text-sm text-gray-600 sm:text-base">
                        {{ $distributor->barangay_name }}, {{ $distributor->street }}</p>

                    <!-- Follower Info -->
                    <div class="flex items-center mt-2">
                        <span class="inline-flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span id="follower-count">{{ number_format($distributor->followers_count ?? 0) }} Followers</span>
                        </span>

                        <!-- Average Rating -->
                        @if (isset($rating) && $rating > 0)
                            <span class="inline-flex items-center ml-4 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1 text-yellow-400"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                {{ number_format($rating, 1) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-3 sm:justify-start sm:gap-4">
                @if (isset($isBlocked) && $isBlocked)
                    <span class="px-3 py-2 text-sm text-white bg-red-500 rounded-md sm:px-4">Blocked</span>
                @else
                    <!-- Follow/Unfollow Button -->
                    <button id="followDistributorBtn" data-distributor-id="{{ $distributor->id }}"
                        class="flex items-center px-3 py-2 text-sm text-white transition-colors duration-200 bg-green-500 rounded-lg sm:px-4 sm:py-2 sm:text-base hover:bg-green-600 active:bg-green-700 touch-manipulation">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 sm:w-5 sm:h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            @if ($isFollowing ?? false)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            @endif
                        </svg>
                        <span>{{ $isFollowing ?? false ? 'Following' : 'Follow' }}</span>
                    </button>

                    <x-modal-review :distributor="$distributor" :reviews="$distributor->reviews" />
                    <a href="{{ route('retailers.messages.index', ['distributor' => $distributor->user_id]) }}"
                        class="flex items-center px-3 py-2 text-sm text-white transition-colors duration-200 bg-green-500 rounded-lg sm:px-4 sm:py-2 sm:text-base hover:bg-green-600 active:bg-green-700 touch-manipulation">
                        <svg class="w-4 h-4 mr-2 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Message
                    </a>
                    <button type="button" onclick="showReportModal()"
                        class="flex items-center px-3 py-2 text-sm text-white transition-colors duration-200 bg-red-500 rounded-lg sm:px-4 sm:py-2 sm:text-base hover:bg-red-600 active:bg-red-700 touch-manipulation">
                        <svg class="w-4 h-4 mr-2 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Report
                    </button>
                @endif
            </div>
        </div>
    </section>

    @if (isset($isBlocked) && $isBlocked)
        <div class="container p-8 mx-auto mb-8 text-center bg-white rounded-lg shadow-lg">
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 mb-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                <h2 class="mb-2 text-2xl font-bold">Access Restricted</h2>
                <p class="mb-6 text-lg text-gray-600">This distributor has blocked you from viewing their products and
                    placing orders.</p>
                <a href="{{ route('retailers.dashboard') }}"
                    class="px-6 py-3 font-medium text-white transition-colors duration-200 bg-green-500 rounded-lg hover:bg-green-600">
                    Return to Dashboard
                </a>
            </div>
        </div>
    @else
        <!-- Categories -->
        <div class="container px-2 mx-auto mb-2 bg-white rounded-lg shadow-lg sm:px-4 sm:mb-4">
            <div class="flex overflow-x-auto scrollbar-hide">
                <a href="{{ route('retailers.distributor-page', ['id' => $distributor->id]) }}"
                    class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap transition-colors duration-200 touch-manipulation
                {{ $selectedCategory === 'all' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-500 active:text-green-600' }}">
                    All Products
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('retailers.distributor-page', ['id' => $distributor->id, 'category' => $category->id]) }}"
                        class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap transition-colors duration-200 touch-manipulation
                    {{ $selectedCategory == $category->id ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-500 active:text-green-600' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Products Grid -->
        <div class="container px-2 mx-auto mb-8 sm:px-4">
            <div class="grid grid-cols-2 gap-3 sm:gap-6 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @forelse($products as $product)
                    <a href="{{ route('retailers.products.show', $product->id) }}"
                        class="block p-3 transition-all duration-300 transform bg-white border border-gray-200 rounded-lg shadow-md sm:p-6 hover:shadow-xl active:shadow-inner hover:scale-105 active:scale-95 touch-manipulation">
                        <div class="flex justify-center mb-2 sm:mb-4">
                            <img class="object-cover w-24 h-24 transition-transform duration-300 rounded-lg sm:w-32 sm:h-32 hover:scale-110"
                                src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/default-product.jpg') }}"
                                alt="{{ $product->name }}">
                        </div>
                        <h3 class="mb-1 text-sm font-semibold text-gray-800 sm:mb-2 sm:text-base line-clamp-2">
                            {{ $product->name }}
                        </h3>
                        <p class="mb-2 text-xs text-gray-600 sm:text-sm">
                            {{ Str::limit($product->description, 50) }}
                        </p>
                        <div class="flex items-center justify-between">
                            <span
                                class="text-sm font-bold text-green-600 sm:text-lg">₱{{ number_format($product->price, 2) }}</span>
                            <span
                                class="px-3 py-1 text-xs font-medium text-white transition-colors duration-200 bg-green-500 rounded-lg sm:px-4 sm:py-2 sm:text-sm hover:bg-green-600 active:bg-green-700">
                                View Details
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="py-8 text-center text-gray-500 col-span-full">
                        No products found in this category.
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            <div class="flex justify-end mt-6">
                {{ $products->appends(['category' => $selectedCategory])->links() }}
            </div>
        </div>
    @endif
    <!-- Report Modal -->
    <div id="report-distributor-modal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50">
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl md:w-1/2 sm:w-2/3">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Report Distributor</h2>
                <button onclick="closeReportModal()" data-action="close"
                    class="p-1 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('retailers.distributors.report', $distributor->id) }}" method="POST"
                id="reportDistributorForm">
                @csrf
                <div class="p-4 space-y-4">
                    <div>
                        <label for="report_reason" class="block mb-2 text-sm font-medium text-gray-700">Reason for
                            reporting</label>
                        <select id="report_reason" name="reason"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a reason</option>
                            <option value="inappropriate_behavior">Inappropriate Behavior</option>
                            <option value="fraud">Fraud or Scam</option>
                            <option value="fake_profile">Fake Profile</option>
                            <option value="delivery_issues">Delivery Issues</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="report_details" class="block mb-2 text-sm font-medium text-gray-700">Additional
                            Details</label>
                        <textarea id="report_details" name="details" rows="4"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Please provide more details about your report"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t rounded-b-lg bg-gray-50">
                    <button type="button" onclick="closeReportModal()" data-action="close"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmReport()"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show the report modal
        function showReportModal() {
            const modal = document.getElementById('report-distributor-modal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        // Close the report modal
        function closeReportModal() {
            const modal = document.getElementById('report-distributor-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Handle report submission with validation and confirmation
        function confirmReport() {
            const reason = document.getElementById('report_reason').value;
            const details = document.getElementById('report_details').value;

            if (!reason) {
                Swal.fire({
                    icon: 'error',
                    title: 'Required Field Missing',
                    text: 'Please select a reason for reporting this distributor',
                });
                return;
            }

            Swal.fire({
                title: 'Report Distributor',
                html: `Are you sure you want to report <strong>{{ $distributor->company_name }}</strong>?<br><br>
                       This report will be reviewed by our team.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, submit report',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reportDistributorForm').submit();
                }
            });
        }

        // Set up event listeners when the document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Close modal when clicking outside
            const reportModal = document.getElementById('report-distributor-modal');
            if (reportModal) {
                reportModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeReportModal();
                    }
                });
            }

            // Close buttons
            const closeButtons = document.querySelectorAll('[data-action="close"]');
            closeButtons.forEach(button => {
                button.addEventListener('click', closeReportModal);
            });

            // Handle escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeReportModal();
                }
            });
        });

        // Follow/Unfollow Distributor
        document.addEventListener('DOMContentLoaded', function() {
            const followBtn = document.getElementById('followDistributorBtn');
            if (followBtn) {
                followBtn.addEventListener('click', function() {
                    const distributorId = this.dataset.distributorId;

                    fetch('{{ route('retailers.distributors.follow') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                distributor_id: distributorId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update button text
                                const buttonText = followBtn.querySelector('span');
                                buttonText.textContent = data.is_following ? 'Following' : 'Follow';

                                // Update icon
                                const iconElement = followBtn.querySelector('svg');
                                if (iconElement) {
                                    if (data.is_following) {
                                        // Change to "check" icon for following
                                        iconElement.innerHTML =
                                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                                    } else {
                                        // Change to "plus" icon for not following
                                        iconElement.innerHTML =
                                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />';
                                    }
                                }

                                // Update follower count
                                const followerCountElement = document.getElementById('follower-count');
                                if (followerCountElement) {
                                    followerCountElement.textContent = new Intl.NumberFormat().format(
                                        data.follower_count);
                                }

                                // Show a notification using SweetAlert (if available)
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: data.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            } else {
                                // Show error message
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Unable to update follow status'
                                    });
                                } else {
                                    alert('Error: Unable to update follow status');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Follow error:', error);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while processing your request'
                                });
                            } else {
                                alert('Error: An error occurred while processing your request');
                            }
                        });
                });
            }
        });
    </script>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <x-footer />
</x-app-layout>
