@props(['imagepath', 'product_name', 'dist_name', 'min_purchase_qty', 'stocks_remaining', 'price'])
<a href="{{route('retailer.product-description')}}" 
   class="flex flex-col items-center p-6 bg-gray-200 rounded-lg shadow-md basis-1/5 w-[20] border border-gray-100
          transition-all duration-300 ease-in-out
          hover:shadow-xl hover:scale-105 hover:border-green-200 hover:bg-green-50">
    <div class="flex justify-center mb-4">
        <img class="object-cover w-24 h-24 rounded transition duration-300 ease-in-out hover:scale-110" 
             src="{{$imagepath}}" 
             alt="Product 1">
    </div>
    <div class="text-left">
        <h3 class="text-lg font-bold hover:text-green-600 transition duration-300">{{$product_name}}</h3>
        <p class="text-[12px] text-gray-500">{{$dist_name}}</p>
        <p class="text-[12px] text-gray-500">Min purchase qty: {{$min_purchase_qty}}</p>
        <p class="text-[12px] text-gray-500">Stocks Remaining: {{$stocks_remaining}}</p>
        <div class="flex flex-col items-center mt-4">
            <span class="text-lg font-bold text-green-600">₱{{$price}}</span>
            <div class="flex items-center mt-2">
                <input type="number" 
                       value="{{$min_purchase_qty}}" 
                       min="{{$min_purchase_qty}}"
                       class="w-16 text-center border border-gray-300 rounded 
                              focus:ring focus:ring-green-200 focus:border-green-300">
                <button class="px-4 py-2 ml-2 font-bold text-white bg-green-500 rounded 
                             hover:bg-green-700 transition duration-300 ease-in-out
                             transform hover:scale-105 active:scale-95">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</a>
