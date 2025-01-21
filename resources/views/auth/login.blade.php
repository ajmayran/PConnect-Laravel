<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-green-400 to-green-600 py-12 px-4 sm:px-6 lg:px-8">
        <!-- Login Form Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Login</h2>
                <p class="mt-2 text-sm text-gray-600">Welcome back! Please enter your details</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <x-text-input id="email" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-green-500 focus:border-green-500"
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
                    <x-text-input id="password" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-green-500 focus:border-green-500"
                        type="password"
                        name="password"
                        placeholder="Password"
                        required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
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
                    class="w-full px-4 py-3 text-white bg-green-600 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200">
                    Sign in
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 text-gray-500 bg-white">Or continue with</span>
                </div>
            </div>

            <!-- Social Buttons -->
            <div class="space-y-4">
                <!-- Facebook -->
                <button onclick="window.location.href='{{ route('auth.facebook') }}'"
                    class="w-full px-4 py-3 flex items-center justify-center space-x-2 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Continue with Facebook</span>
                </button>

                <!-- Google -->
                <button onclick="window.location.href='{{ route('auth.google') }}'"
                    class="w-full px-4 py-3 flex items-center justify-center space-x-2 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-5 h-5" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                    </svg>
                    <span>Continue with Google</span>
                </button>
            </div>

            <!-- Sign Up Link -->
            <p class="text-center text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-medium text-green-600 hover:text-green-500">
                    Sign up
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>