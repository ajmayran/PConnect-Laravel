<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distributor Subscription</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="flex flex-col min-h-screen pt-16 font-sans bg-gray-50">
    <div class="flex-grow">
        <div class="container p-8 mx-auto">
            <div class="flex flex-col items-center justify-center mb-8 text-center">
                <h1 class="mt-4 text-4xl font-bold text-gray-800">Distributor Subscription Plans</h1>
                <p class="max-w-xl mt-2 text-gray-500">
                    Congratulations! You have <span class="font-bold text-green-600">1 month free access</span> as a new distributor.<br>
                    To continue using all features after your trial, please choose a subscription plan below.
                </p>
                <div class="mt-4">
                    <a href="{{ route('distributors.dashboard') }}" class="px-6 py-3 font-semibold text-green-600 transition-all transform bg-white border-2 border-green-500 rounded-full shadow-md hover:bg-green-50 hover:scale-105">
                        Skip and Start Using Your Free Month
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- 3 Months -->
                <div class="flex flex-col items-center p-6 bg-white border-2 border-green-100 rounded-lg shadow-lg">
                    <h2 class="mb-2 text-2xl font-bold text-gray-800">3 Months</h2>
                    <div class="mb-2 text-5xl font-extrabold text-green-500">₱399</div>
                    <p class="mb-4 text-gray-500">Full access for 3 months</p>
                    <button onclick="payWithPaymongo(399, '3_months')" class="w-full px-5 py-3 font-semibold text-white transition-all transform rounded-full shadow-lg bg-gradient-to-r from-green-400 to-green-500 hover:from-green-500 hover:to-green-600 hover:scale-105">
                        Subscribe
                    </button>
                </div>
                <!-- 6 Months -->
                <div class="flex flex-col items-center p-6 bg-white border-2 border-green-400 rounded-lg shadow-lg">
                    <h2 class="mb-2 text-2xl font-bold text-gray-800">6 Months</h2>
                    <div class="mb-2 text-5xl font-extrabold text-green-500">₱649</div>
                    <p class="mb-4 text-gray-500">Full access for 6 months</p>
                    <button onclick="payWithPaymongo(649, '6_months')" class="w-full px-5 py-3 font-semibold text-white transition-all transform rounded-full shadow-lg bg-gradient-to-r from-green-400 to-green-500 hover:from-green-500 hover:to-green-600 hover:scale-105">
                        Subscribe
                    </button>
                </div>
                <!-- 1 Year -->
                <div class="flex flex-col items-center p-6 bg-white border-2 border-green-600 rounded-lg shadow-lg">
                    <h2 class="mb-2 text-2xl font-bold text-gray-800">1 Year</h2>
                    <div class="mb-2 text-5xl font-extrabold text-green-500">₱999</div>
                    <p class="mb-4 text-gray-500">Full access for 12 months</p>
                    <button onclick="payWithPaymongo(999, '1_year')" class="w-full px-5 py-3 font-semibold text-white transition-all transform rounded-full shadow-lg bg-gradient-to-r from-green-400 to-green-500 hover:from-green-500 hover:to-green-600 hover:scale-105">
                        Subscribe
                    </button>
                </div>
            </div>
            <div id="paymongo-redirect" class="hidden mt-8 text-lg font-semibold text-center text-blue-600"></div>
        </div>
    </div>

    <script>
        function payWithPaymongo(amount, plan) {
            // Display loading message
            document.getElementById('paymongo-redirect').textContent = "Processing your request...";
            document.getElementById('paymongo-redirect').classList.remove('hidden');
            
            fetch("{{ route('distributors.subscription.paymongo') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    amount: amount,
                    plan: plan
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.checkout_url) {
                    document.getElementById('paymongo-redirect').textContent = "Redirecting to payment gateway...";
                    window.location.href = data.checkout_url;
                } else {
                    document.getElementById('paymongo-redirect').textContent = "Error: " + (data.message || "Could not process payment");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('paymongo-redirect').textContent = "Error: Could not connect to payment service";
            });
        }
    </script>
</body>
</html>