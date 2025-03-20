<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .custom-border {
            border: 3px solid #dcfce7;
        }
        .custom-border-active {
            border: 2px solid #16a34a;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col pt-16">
    <div class="flex-grow">
        <div class="container mx-auto p-8">
            <div class="flex flex-col items-center justify-center mb-8 text-center">

                <h1 class="text-4xl font-bold mt-4 text-gray-800">Pricing Plans for Every Need</h1>
                <p class="text-gray-500 mt-2">Choose a plan that works best for you and your Company.</p>
            </div>
            <div class="flex justify-center mb-6">
                <button id="monthly" class="px-6 py-3 rounded-full bg-white text-gray-700 mr-4 border custom-border hover:bg-green-500 hover:text-white transition-colors font-medium shadow-md">Monthly</button>
                <button id="yearly" class="px-6 py-3 rounded-full bg-white text-gray-700 border custom-border hover:bg-green-500 hover:text-white transition-colors font-medium shadow-md">Yearly</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-6 shadow-lg custom-border plan hover:shadow-xl transition-shadow duration-300">
                    <h2 class="text-2xl font-bold mb-3 text-gray-800">Standard</h2>
                    <p class="text-gray-500 mb-5">Perfect for individuals and small teams.</p>
                    <div class="text-4xl font-extrabold text-green-500 mb-2">₱</div>
                    <p class="text-sm text-gray-400 mb-6">Billed Annually</p>
                    <h3 class="font-semibold text-lg mb-3 text-gray-700">What's Included?</h3>
                    <ul class="list-none space-y-3 mb-6">
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">1 Legal Policy</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">4 Policy Edits</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">Privacy Regulation Monitoring</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">Normal Support</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 px-5 rounded-full bg-gradient-to-r from-green-400 to-green-500 text-white font-semibold shadow-lg hover:from-green-500 hover:to-green-600 transition-all transform hover:scale-105">Get Started</button>
                    <button class="w-full py-2 mt-3 text-green-500 font-medium hover:text-green-600 hover:underline transition-colors">Learn more</button>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg custom-border plan hover:shadow-xl transition-shadow duration-300 relative">
                    <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-bl-lg">POPULAR</div>
                    <h2 class="text-2xl font-bold mb-3 text-gray-800">Premium</h2>
                    <p class="text-gray-500 mb-5">Perfect for big companies and larger teams.</p>
                    <div class="text-4xl font-extrabold text-green-500 mb-2">₱</div>
                    <p class="text-sm text-gray-400 mb-6">Billed Annually</p>
                    <h3 class="font-semibold text-lg mb-3 text-gray-700">What's Included?</h3>
                    <ul class="list-none space-y-3 mb-6">
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">2 Legal Policy</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">10 Policy Edits</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">Privacy Regulation Monitoring</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">Premium Support</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 px-5 rounded-full bg-gradient-to-r from-green-400 to-green-500 text-white font-semibold shadow-lg hover:from-green-500 hover:to-green-600 transition-all transform hover:scale-105">Get Started</button>
                    <button class="w-full py-2 mt-3 text-green-500 font-medium hover:text-green-600 hover:underline transition-colors">Learn more</button>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg custom-border plan hover:shadow-xl transition-shadow duration-300 relative">
                    <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-bl-lg">POPULAR</div>
                    <h2 class="text-2xl font-bold mb-3 text-gray-800">Deluxe</h2>
                    <p class="text-gray-500 mb-5">Perfect for small companies and small teams.</p>
                    <div class="text-4xl font-extrabold text-green-500 mb-2">₱</div>
                    <p class="text-sm text-gray-400 mb-6">Billed Annually</p>
                    <h3 class="font-semibold text-lg mb-3 text-gray-700">What's Included?</h3>
                    <ul class="list-none space-y-3 mb-6">
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">2 Legal Policy</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">10 Policy Edits</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">Privacy Regulation Monitoring</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600">Premium Support</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 px-5 rounded-full bg-gradient-to-r from-green-400 to-green-500 text-white font-semibold shadow-lg hover:from-green-500 hover:to-green-600 transition-all transform hover:scale-105">Get Started</button>
                    <button class="w-full py-2 mt-3 text-green-500 font-medium hover:text-green-600 hover:underline transition-colors">Learn more</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Include Footer -->
    @include('components.footer')
</body>
</html>