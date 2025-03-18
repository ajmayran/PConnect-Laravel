<x-guest-layout>
    <div class="flex items-center justify-center w-full min-h-screen px-4 py-8 md:py-0">
        <!-- Registration Form Card -->
        <div class="flex flex-col w-full max-w-6xl overflow-hidden bg-white shadow-2xl rounded-3xl lg:flex-row">
            <!-- Image Section - Hidden on mobile, visible on lg screens -->
            <div class="hidden p-4 bg-center bg-cover border lg:block lg:w-1/2 rounded-2xl" 
                 style="background-image: url('{{ asset('img/signup4.png') }}');">
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
                    <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Register as Retailer</h2>
                    <p class="mt-2 text-base text-gray-600 sm:text-xl md:text-2xl">Create your account to get started</p>
                </div>

                <form method="POST" action="{{ route('register.retailer') }}" enctype="multipart/form-data" class="mt-6 space-y-4 sm:mt-8 sm:space-y-6">
                    @csrf
                    <input type="hidden" name="user_type" value="retailer">
                    
                    <!-- Name Fields - Grid on larger screens -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- First Name -->
                        <div>
                            <x-text-input id="first_name"
                                class="w-full px-3 py-2 border border-gray-300 sm:px-4 sm:py-3 rounded-xl focus:ring-green-500 focus:border-green-500"
                                type="text" name="first_name" :value="old('first_name')" placeholder="First Name" required autofocus />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>
                        <!-- Last Name -->
                        <div>
                            <x-text-input id="last_name"
                                class="w-full px-3 py-2 border border-gray-300 sm:px-4 sm:py-3 rounded-xl focus:ring-green-500 focus:border-green-500"
                                type="text" name="last_name" :value="old('last_name')" placeholder="Last Name" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <x-text-input id="middle_name"
                            class="w-full px-3 py-2 border border-gray-300 sm:px-4 sm:py-3 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="text" name="middle_name" :value="old('middle_name')" placeholder="Middle Name (Optional)" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-text-input id="email"
                            class="w-full px-3 py-2 border border-gray-300 sm:px-4 sm:py-3 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="email" name="email" :value="old('email')" placeholder="Email Address" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Credentials Upload -->
                    <div>
                        <label for="credentials" class="block mb-1 text-sm font-medium text-gray-700">Permit ( Mayor's, Business )</label>
                        <input id="credentials"
                            class="w-full px-3 py-2 border border-gray-300 sm:px-4 sm:py-3 rounded-xl focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-1 file:px-3 sm:file:py-2 sm:file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                            type="file" name="credentials" placeholder="Upload Credentials (Image, PDF)" required />
                        <div class="mt-1 text-xs text-right text-gray-400">Image or PDF ( Maximum of 20MB )</div>
                        <x-input-error :messages="$errors->get('credentials')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-text-input id="password"
                            class="w-full px-3 py-2 border border-gray-300 sm:px-4 sm:py-3 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="password" name="password" placeholder="Password" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-text-input id="password_confirmation"
                            class="w-full px-3 py-2 border border-gray-300 sm:px-4 sm:py-3 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="password" name="password_confirmation" placeholder="Confirm Password" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Register Button -->
                    <button type="submit"
                        class="w-full px-3 py-2 text-white transition-colors duration-200 bg-green-600 sm:px-4 sm:py-3 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Register
                    </button>

                    <!-- Login Link -->
                    <p class="text-sm text-center text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500">
                            Login
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>