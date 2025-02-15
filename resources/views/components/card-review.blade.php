@props([
    'user_name',
    'user_image',
    'rating',
    'review_date',
    'review_text'
])

<div class="p-4 bg-white border border-gray-200 rounded-lg">
    <div class="flex items-center mb-4">
        <img class="w-10 h-10 me-4 rounded-full" 
             src="{{ $user_image }}" 
             alt="{{ $user_name }}"
             onerror="this.src='{{ asset('img/default-profile.png') }}'">
        <div class="font-medium dark:text-white">
            <p>{{ $user_name }} 
                <time datetime="{{ $review_date }}" class="block text-sm text-gray-500 dark:text-gray-400">
                    Reviewed on: {{ $review_date->format('F j Y') }}
                </time>
            </p>
        </div>
    </div>
    
    <div class="flex items-center mb-1 space-x-1">
        @for ($i = 1; $i <= 5; $i++)
            <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-300' : 'text-gray-300' }}" 
                 aria-hidden="true" 
                 xmlns="http://www.w3.org/2000/svg" 
                 fill="currentColor" 
                 viewBox="0 0 22 20">
                <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
            </svg>
        @endfor
        <h3 class="ms-2 text-sm font-semibold text-gray-900">{{ $rating }} Stars</h3>
    </div>

    <p class="mb-2 text-gray-500">{{ $review_text }}</p>

</div>