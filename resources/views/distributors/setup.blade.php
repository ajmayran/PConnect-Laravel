<x-app-layout>
    <div class="min-h-screen bg-cover bg-center" style="background-image: url('{{ asset('img/loginbg.png') }}')">
        <div class="flex items-center justify-center min-h-screen px-4 py-12">
            <div class="w-full max-w-3xl p-8 space-y-8 bg-white shadow-2xl rounded-2xl">
                <!-- Header -->
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-900">Company Profile Setup</h1>
                    <p class="mt-2 text-sm text-gray-600">Please complete your company profile information</p>
                </div>

                <!-- Alerts -->
                @if ($errors->any())
                    <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50">
                        <ul class="ml-4 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form id="setupForm" action="{{ route('profile.updateSetup') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Company Logo -->
                    <div>
                        <label for="company_profile_image" class="block text-sm font-medium text-gray-700">Company Logo</label>
                        <div class="mt-1">
                            <input type="file" id="company_profile_image" name="company_profile_image" accept="image/*"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" id="company_name" name="company_name" required
                            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Company Email -->
                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700">Company Email</label>
                        <input type="email" id="company_email" name="company_email" required
                            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Company Address -->
                    <div>
                        <label for="company_address" class="block text-sm font-medium text-gray-700">Company Address</label>
                        <input type="text" id="company_address" name="company_address" required
                            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Company Phone -->
                    <div>
                        <label for="company_phone_number" class="block text-sm font-medium text-gray-700">Company Phone Number</label>
                        <input type="text" id="company_phone_number" name="company_phone_number" required
                            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500">
                    </div>

                    <input type="hidden" name="subscription_plan" id="subscription_plan">

                    <!-- Continue Button -->
                    <button type="button" onclick="showSubscriptionModal()"
                        class="w-full px-4 py-3 text-white transition-colors duration-200 bg-green-600 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Continue to Subscription
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Subscription Modal -->
    <div id="subscriptionModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-70 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <!-- Modal Content -->
            <div class="relative inline-block w-full max-w-4xl p-8 overflow-hidden text-left align-middle transition-all transform bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Choose Your Subscription Plan</h2>
                    <p class="mt-2 text-gray-600">Select a plan that best suits your distribution needs</p>
                </div>

                <!-- Plans Grid -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <!-- Basic Plan -->
                    <div class="relative p-6 bg-white border border-gray-200 rounded-2xl hover:shadow-xl transition-all duration-300 hover:border-green-500 hover:translate-y-[-8px]">
                        <h3 class="text-xl font-bold text-gray-900">Basic Plan</h3>
                        <p class="text-sm text-gray-500 mt-2">Perfect for starting distributors</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-gray-900">₱1,000</span>
                            <span class="text-gray-500">/month</span>
                        </div>
                        <ul class="mt-6 space-y-4 text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Adding Products
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Product Management
                            </li>
                        </ul>
                        <button onclick="selectPlan('basic')" class="w-full px-4 py-3 mt-8 text-white bg-green-600 rounded-xl hover:bg-green-700 transition-colors duration-200">
                            Get Started
                        </button>
                    </div>

                    <!-- Professional Plan -->
                    <div class="relative p-6 bg-white border-2 border-green-500 rounded-2xl hover:shadow-xl transition-all duration-300 hover:translate-y-[-8px]">
                        <!-- 10% OFF Badge -->
                        <div class="absolute -top-3 -right-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold transform rotate-12">
                            10% OFF
                        </div>
                        <!-- Popular Tag - Moved to top left -->
                        <div class="absolute top-0 left-0">
                            <div class="bg-green-500 text-white text-xs px-3 py-1 rounded-full">
                                Popular
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Professional Plan</h3>
                        <p class="text-sm text-gray-500 mt-2">Best value for growing business</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-gray-900">₱5,400</span>
                            <span class="text-gray-500">/6 months</span>
                        </div>
                        <ul class="mt-6 space-y-4 text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Adding Products
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Product Management
                            </li>
                        </ul>
                        <button onclick="selectPlan('professional')" class="w-full px-4 py-3 mt-8 text-white bg-green-600 rounded-xl hover:bg-green-700 transition-colors duration-200">
                            Get Started
                        </button>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="relative p-6 bg-white border border-gray-200 rounded-2xl hover:shadow-xl transition-all duration-300 hover:border-green-500 hover:translate-y-[-8px]">
                        <!-- 10% OFF Badge -->
                        <div class="absolute -top-3 -right-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold transform rotate-12">
                            10% OFF
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Enterprise Plan</h3>
                        <p class="text-sm text-gray-500 mt-2">For established businesses</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-gray-900">₱10,800</span>
                            <span class="text-gray-500">/year</span>
                        </div>
                        <ul class="mt-6 space-y-4 text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Adding Products
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Product Management
                            </li>
                        </ul>
                        <button onclick="selectPlan('enterprise')" class="w-full px-4 py-3 mt-8 text-white bg-green-600 rounded-xl hover:bg-green-700 transition-colors duration-200">
                            Get Started
                        </button>
                    </div>
                </div>

                <!-- Cancel Button -->
                <div class="mt-8 text-center">
                    <button onclick="closeSubscriptionModal()" 
                        class="px-6 py-2 text-white bg-red-500 rounded-lg font-medium 
                        transition-all duration-300 hover:bg-red-600 hover:shadow-lg 
                        transform hover:scale-105 focus:outline-none focus:ring-2 
                        focus:ring-red-500 focus:ring-offset-2">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSubscriptionModal() {
            const form = document.getElementById('setupForm');
            if (form.checkValidity()) {
                document.getElementById('subscriptionModal').classList.remove('hidden');
            } else {
                form.reportValidity();
            }
        }

        function closeSubscriptionModal() {
            document.getElementById('subscriptionModal').classList.add('hidden');
        }

        function selectPlan(plan) {
            document.getElementById('subscription_plan').value = plan;
            document.getElementById('setupForm').submit();
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('subscriptionModal');
            const modalContent = modal.querySelector('.relative');
            if (event.target === modal) {
                closeSubscriptionModal();
            }
        });
    </script>
</x-app-layout>
