@props(['route', 'imagepath', 'distributor_name', 'distributor_desc', 'address'])
<a href="{{$route}}" 
   class="block bg-white shadow-md rounded-lg overflow-hidden 
          border border-gray-100
          transition-all duration-300 ease-in-out
          hover:shadow-xl hover:scale-105 hover:border-green-200 hover:bg-green-50">
    <img src="{{$imagepath}}" 
         alt="{{$distributor_name}} LOGO" 
         class="w-full h-48 object-cover">
    <div class="p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{$distributor_name}}</h3>
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{$distributor_desc}}</p>
        @if($address)
            <div class="flex items-start gap-2 mb-3">
                <svg class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-sm text-gray-600 line-clamp-2">{{$address}}</p>
            </div>
        @endif
        <div class="mt-2 text-right">
            <span class="inline-flex items-center text-green-600 hover:text-green-700">
                View Details 
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        </div>
    </div>
</a>