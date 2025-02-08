<x-app-layout>
    <x-dashboard-nav />
        <!-- Back Button -->
        <div class="container mx-auto my-10">
            <a href="{{ route('retailers.dashboard') }}" class="text-green-500 hover:text-green-700">
                Go back
            </a>
        </div>
    
        <div class="container flex items-center px-4 py-5 mx-auto border border-gray-400">
            <div class="flex justify-center p-5 mx-auto bg-white">
                <div>
                    <!-- Main Product Image -->
                    <div class="flex justify-center px-5 py-5 m-5">
                        <img id="main-product-image" src="{{ asset('img/alaska_products/krem_top_5g.jpg') }}" alt="main product img" class="object-cover h-48 rounded-lg"/>
                    </div>
    
                    <!-- Product Variants -->
                    <div>
                        <div class="flex justify-center">
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="product-variant" class="hidden peer" data-image="{{ asset('img/alaska_products/krem_top_5g.jpg') }}" checked/>
                                <img src="{{ asset('img/alaska_products/krem_top_5g.jpg') }}" alt="product img variant 1" class="object-cover w-24 h-24 p-1 m-2 bg-gray-100 border rounded-lg peer-checked:border-green-500"/>
                            </label>
                            
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="product-variant" class="hidden peer" data-image="{{ asset('img/alaska_products/krem_top_back.jpg') }}"/>
                                <img src="{{ asset('img/alaska_products/krem_top_back.jpg') }}" alt="product img variant 2" class="object-cover w-24 h-24 p-1 m-2 border border-gray-300 rounded-lg peer-checked:border-green-500"/>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="w-1/3">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-bold">Krem-Top Coffee Creamer</h2>
                </div>
                <p class="w-auto mb-4 text-gray-500">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                <span class="mr-2 text-2xl font-md">Size:</span>
                <span class="text-2xl font-bold">24x5g</span>
                <div class="flex items-center mb-4">
                    <span class="mr-2 text-2xl font-md">Per Case:</span>
                    <span class="text-2xl font-bold">₱840</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <button class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded hover:bg-green-600 focus:outline-none" id="minus-btn">-</button>
                    <input type="number" id="quantity-input" value="1" min="1" class="w-12 text-center border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:outline-none"/>
                    <button class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded hover:bg-green-600 focus:outline-none" id="plus-btn">+</button>
                </div>
    
                <div class="rounded-lg">
                    <button type="button" onclick="openproductModal()" class="px-4 py-2 mt-5 mr-2 font-bold text-white bg-green-500 rounded hover:bg-green-700">
                        Add to Cart
                    </button>
    
                    <div class="mt-4">
                        <p>Type: Ready to Cook</p>
                        <p>SKU: FWM513VKT</p>
                        <p>MFG: Jun 4, 2024</p>
                        <p>LIFE: 70 days</p>
                        <p>Stock: 8 Items In Stock</p>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Success Modal -->
        <div id="productModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-800 bg-opacity-50">
            <div class="relative w-full max-w-sm max-h-screen p-8 overflow-y-auto bg-white rounded-lg">   
                <div class="flex justify-center">
                    <div class="p-2 text-white bg-green-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75" />
                        </svg>
                    </div>
                </div>
                <p class="flex justify-center text-2xl font-semibold text-green-500">Success!</p>
                <div class="flex justify-center">
                    <p>Your item has been added to the cart.</p>
                </div>
                <hr class="my-6">
                <div class="flex justify-center mt-6">
                    <button onclick="closeproductModal()" class="px-6 py-2 mr-4 text-white bg-gray-400 border rounded-lg hover:bg-gray-300 hover:text-gray-700">Close</button>
                    <a href="{{ route('cart.show') }}"><button class="px-6 py-2 mr-4 text-gray-700 bg-white border rounded-lg hover:bg-gray-100">View Cart</button></a>
                </div>
            </div>
        </div>
    
        <script>
            function openproductModal() {
                document.getElementById('productModal').classList.remove('hidden');
            }
            
            function closeproductModal() {
                document.getElementById('productModal').classList.add('hidden');
            }
        
            document.getElementById('plus-btn').addEventListener('click', () => {
                const input = document.getElementById('quantity-input');
                input.value = parseInt(input.value) + 1;
            });
    
            document.getElementById('minus-btn').addEventListener('click', () => {
                const input = document.getElementById('quantity-input');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
            
            const variantRadios = document.querySelectorAll('input[name="product-variant"]');
            const mainImage = document.getElementById('main-product-image');
    
            variantRadios.forEach((radio) => {
                radio.addEventListener('change', (event) => {
                    const selectedImage = event.target.getAttribute('data-image');
                    mainImage.src = selectedImage;
                });
            });
        </script>
</x-app-layout>