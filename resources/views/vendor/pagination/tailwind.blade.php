@if ($paginator->hasPages())
    <nav class="flex items-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                Previous
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 text-sm text-gray-600 bg-white border rounded-md hover:bg-gray-50">
                Previous
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="flex items-center gap-1">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-1 text-sm text-gray-700">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1 text-sm text-white bg-green-500 rounded-md">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1 text-sm text-gray-600 bg-white border rounded-md hover:bg-gray-50">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 text-sm text-gray-600 bg-white border rounded-md hover:bg-gray-50">
                Next
            </a>
        @else
            <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                Next
            </span>
        @endif
    </nav>
@endif