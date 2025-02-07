@props(['distributor_name', 'imagepath', 'route'])
<a href="{{$route}}" class="flex flex-col items-center p-6 transition-shadow duration-300 bg-white border border-gray-100 shadow-lg cursor-pointer rounded-xl hover:shadow-xl">
    <img class="w-24 h-24 mb-4 rounded-full shadow-md" src="{{$imagepath}}"
        alt="Distributor Jacob">
    <h3 class="text-lg font-bold text-gray-800">{{$distributor_name}}</h3>
</a>
