<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-green-400 to-green-600 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl p-8 space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Register</h2>
                <p class="mt-2 text-sm text-gray-600">Create your account to get started</p>
            </div>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf

                <!-- Name Fields Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <x-text-input id="first_name" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-green-500 focus:border-green-500 bg-white"
                            type="text"
                            name="first_name"
                            :value="old('first_name')"
                            placeholder="First Name"
                            required 
                            autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <!-- Last Name -->
                    <div>
                        <x-text-input id="last_name" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-green-500 focus:border-green-500 bg-white"
                            type="text"
                            name="last_name"
                            :value="old('last_name')"
                            placeholder="Last Name"
                            required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>

                <!-- Middle Name -->
                <div>
                    <x-text-input id="middle_name" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-green-500 focus:border-green-500 bg-white"
                        type="text"
                        name="middle_name"
                        :value="old('middle_name')"
                        placeholder="Middle Name (Optional)" />
                    <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-text-input id="email" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-green-500 focus:border-green-500 bg-white"
                        type="email"
                        name="email"
                        :value="old('email')"
                        placeholder="Email"
                        required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Credentials Upload -->
                <div>
                    <input id="credentials" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-green-500 focus:border-green-500 bg-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                        type="file"
                        name="credentials"
                        placeholder="Upload Credentials (Image, PDF)"
                        required />
                    <x-input-error :messages="$errors->get('credentials')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-text-input id="password" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-green-500 focus:border-green-500 bg-white"
                        type="password"
                        name="password"
                        placeholder="Password"
                        required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-text-input id="password_confirmation" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-green-500 focus:border-green-500 bg-white"
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm Password"
                        required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Register Button -->
                <button type="submit" 
                    class="w-full px-4 py-3 text-white bg-green-600 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200">
                    Register
                </button>

                <!-- Login Link -->
                <p class="text-center text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500">
                        Login
                    </a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>
