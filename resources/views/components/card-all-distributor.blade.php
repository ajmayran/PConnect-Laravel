@props(['route','imagepath', 'distributor_name', 'distributor_desc', 'address'])
<div class="bg-gray-200 shadow-md rounded-lg overflow-hidden 
            border border-gray-100
            transition-all duration-300 ease-in-out
            hover:shadow-xl hover:scale-105 hover:border-green-200 hover:bg-green-50">
    <img src="{{$imagepath}}" 
         alt="{{$distributor_name}} LOGO" 
         class="w-full h-48 object-cover">
    <div class="p-6">
        <h3 class="text-2xl font-semibold text-gray-800">{{ $distributor_name}}</h3>
        <p class="text-gray-600 mt-2">{{$distributor_desc}}</p>
        <div class="mt-4">
            <p class="font-medium text-gray-700">Location: {{$address}}</p>
        </div>
        <div class="mt-6 flex justify-between items-center">
            <a href="{{$route}}" 
               class="text-blue-500 hover:text-blue-700">
                View Products
            </a>
        </div>
    </div>
</div>