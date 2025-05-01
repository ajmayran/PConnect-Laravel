<x-guest-layout>
    <div class="flex items-center justify-center w-full min-h-screen px-4 py-8 md:py-0">
        <!-- Login Form Card -->
        <div class="flex flex-col w-full max-w-6xl overflow-hidden bg-white shadow-2xl rounded-3xl lg:flex-row">
            <!-- Image Section - Hidden on mobile, visible on lg screens -->
            <div class="hidden p-4 bg-center bg-cover border lg:block lg:w-1/2 rounded-2xl" 
                 style="background-image: url('{{ asset('img/welcome2.png') }}');">
                <a href="/" 
                   class="inline-flex items-center px-4 py-2 font-sans text-base text-white transition duration-300 ease-in-out bg-gray-800 shadow-2xl cursor-pointer hover:bg-gray-600 rounded-3xl">
                    <span class="mr-2">&larr;</span>
                    Back to website
                </a>
            </div>
            
            <!-- Form Section -->
            <div class="w-full p-6 lg:w-1/2 sm:p-8 md:p-12 lg:p-16 rounded-2xl">
                <!-- Mobile only back button -->
                <div class="block mb-6 lg:hidden">
                    <a href="/" 
                       class="inline-flex items-center px-4 py-2 font-sans text-sm text-white transition duration-300 ease-in-out bg-gray-800 shadow-2xl cursor-pointer hover:bg-gray-600 rounded-3xl">
                        <span class="mr-2">&larr;</span>
                        Back to website
                    </a>
                </div>
                
                <!-- Header -->
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Login</h2>
                    <p class="mt-2 text-base text-gray-600 sm:text-xl md:text-2xl">Welcome back! Please enter your details</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4 sm:mt-8 sm:space-y-6">
                    @csrf
                    <!-- Email Field -->
                    <div>
                        <x-text-input 
                            id="email"
                            class="w-full px-3 py-2 border border-gray-300 sm:px-4 sm:py-3 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="email"
                            name="email"
                            :value="old('email')"
                            placeholder="Email"
                            required 
                            autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <x-text-input 
                            id="password"
                            class="w-full px-3 py-2 border border-gray-300 sm:px-4 sm:py-3 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="password"
                            name="password"
                            placeholder="Password"
                            required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                        <label class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-500">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" 
                        class="w-full px-3 py-2 text-white transition-colors duration-200 bg-green-600 sm:px-4 sm:py-3 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Sign in
                    </button>
                </form>

                <!-- Sign Up Link -->
                <p class="mt-4 text-sm text-center text-gray-600 sm:mt-6">
                    Don't have an account? 
                    <a href="#" id="signUpModalBtn" class="font-medium text-green-600 hover:text-green-500">
                        Sign up
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Modal for Registration Options -->
    <div id="signUpModal" class="fixed inset-0 z-50 flex items-center justify-center transition-opacity duration-300 bg-black bg-opacity-50 opacity-0 pointer-events-none">
        <div id="modalContent" class="w-full max-w-sm p-6 mx-4 transition-all duration-300 transform scale-95 bg-white rounded-lg shadow-xl">
            <h2 class="mb-4 text-xl font-bold text-center">Register As</h2>
            <div class="flex space-x-4">
                <a href="{{ route('register.retailer') }}" class="w-1/2 px-4 py-2 text-center text-white transition-colors duration-500 bg-green-500 rounded hover:bg-green-700">
                    Retailer
                </a>
                <a href="{{ route('register.distributor') }}" class="w-1/2 px-4 py-2 text-center text-white transition-colors duration-500 bg-blue-500 rounded hover:bg-blue-700">
                    Distributor
                </a>
            </div>
            <button id="closeSignUpModal" class="block mx-auto mt-4 text-red-500">
                Cancel
            </button>
        </div>
    </div>

    <script>
        const signUpModal = document.getElementById('signUpModal');
        const modalContent = document.getElementById('modalContent');
        const signUpModalBtn = document.getElementById('signUpModalBtn');
        const closeSignUpModal = document.getElementById('closeSignUpModal');

        // Function to show the modal with transition
        function showModal() {
            signUpModal.classList.remove('pointer-events-none');
            signUpModal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }
        
        // Function to hide the modal with transition
        function hideModal() {
            signUpModal.classList.add('opacity-0');
            modalContent.classList.add('scale-95');
            // Disable clicks after the transition ends
            setTimeout(() => {
                signUpModal.classList.add('pointer-events-none');
            }, 300);
        }

        if (signUpModalBtn) {
            signUpModalBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showModal();
            });
        }

        if (closeSignUpModal) {
            closeSignUpModal.addEventListener('click', function() {
                hideModal();
            });
        }
    </script>
</x-guest-layout>