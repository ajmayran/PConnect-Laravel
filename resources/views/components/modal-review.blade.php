@props(['distributor', 'reviews' => []])

<div x-data="{ open: false }" x-on:open-modal-review.window="open = true" x-on:close-modal-review.window="open = false">
    <!-- Modal Trigger Button -->
    <button @click="open = true"
        class="flex items-center px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600">
        <svg class="w-4 h-4 text-yellow-300 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 22 20">
            <path
                d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z" />
        </svg>
        Rating
        <span class="ml-1 font-bold">{{ number_format($distributor->average_rating ?? 0, 1) }}</span>
    </button>

    <!-- Modal -->
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div
                class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Review Form -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Rate {{ $distributor->company_name }}</h3>

                    <form action="{{ route('retailers.reviews.store') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="distributor_id" value="{{ $distributor->id }}">

                        <!-- Star Rating -->
                        <div class="flex items-center mb-4" x-data="{ rating: 0 }">
                            <div class="flex items-center space-x-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}"
                                        class="focus:outline-none">
                                        <svg class="w-6 h-6 transition-colors duration-200"
                                            :class="{ 'text-yellow-300': rating >=
                                                {{ $i }}, 'text-gray-300': rating < {{ $i }} }"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 20" fill="currentColor">
                                            <path
                                                d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z" />
                                        </svg>
                                    </button>
                                @endfor
                                <input type="hidden" name="rating" x-model="rating">
                                <span class="ml-2 text-sm text-gray-600"
                                    x-text="rating ? `${rating} Stars` : 'Select Rating'"></span>
                            </div>
                        </div>

                        <!-- Review Text -->
                        <div class="mt-4">
                            <textarea name="review" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Write your review..."></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit"
                                class="px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Reviews List -->
                <div class="mt-6">
                    <h4 class="mb-4 text-lg font-medium text-gray-900">Customer Reviews</h4>
                    <div class="space-y-4 overflow-y-auto max-h-[400px]">
                        @forelse($reviews as $review)
                            <x-card-review :user_name="$review->user->first_name . ' ' . $review->user->last_name" :user_image="$review->user->profile_image ?? ''" :rating="$review->rating" :review_date="$review->created_at"
                                :review_text="$review->review" />
                        @empty
                            <p class="text-gray-500">No reviews yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
