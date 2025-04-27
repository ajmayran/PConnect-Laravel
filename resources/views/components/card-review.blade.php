@props(['user_name', 'user_image', 'rating', 'review_date', 'review_text'])

<div class="p-4 border rounded-lg bg-gray-50">
    <div class="flex items-center mb-2">
        <div class="flex-shrink-0">
            @if ($user_image)
                <img class="w-10 h-10 rounded-full object-cover" src="{{ asset('storage/' . $user_image) }}"
                    alt="{{ $user_name }}">
            @else
                <div class="w-10 h-10 rounded-full bg-green-300 flex items-center justify-center text-gray-600">
                    {{ strtoupper(substr($user_name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">{{ $user_name }}</p>
            <div class="flex items-center">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-300' : 'text-gray-300' }}"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 20" fill="currentColor">
                        <path
                            d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z" />
                    </svg>
                @endfor
                <span class="ml-2 text-xs text-gray-500">
                    {{ is_string($review_date) ? $review_date : $review_date->format('M d, Y') }}
                </span>
            </div>
        </div>
    </div>
    @if ($review_text)
        <p class="text-sm text-gray-600">{{ $review_text }}</p>
    @endif
</div>