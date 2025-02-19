<x-guest-layout>
    <div class="min-h-screen px-4 py-8 bg-gradient-to-r from-green-400 to-green-600 sm:px-6 lg:px-8 over">
        <div class="flex flex-row p-5 mx-auto bg-white shadow-2xl rounded-3xl max-w-7xl overf">
            <!-- Left Side - Image -->
            <div class="w-1/2 p-4 border rounded-2xl" style="background-image: url('{{ asset('img/signup-distributor.png') }}'); background-size: cover; background-position: center;">
                <a href="" class="inline-flex items-center px-5 py-2 font-sans text-base text-white transition duration-300 ease-in-out bg-gray-800 shadow-2xl cursor-pointer hover:bg-gray-600 rounded-3xl">
                    <span class="mr-2">&larr;</span>
                    Back
                </a>
            </div>

            <!-- Right Side - Form -->
            <div class="w-1/2 p-8 overflow-y-auto rounded-2xl">
                <!-- Header -->
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-900">Register as Distributor</h2>
                    <p class="mt-2 text-xl text-gray-600">Create your account to get started</p>
                </div>

                <form method="POST" action="{{ route('register.distributor') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="user_type" value="distributor">

                    <!-- Name Fields -->
                    <div class="space-y-4">
                        <div>
                            <x-text-input id="first_name"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                                type="text" name="first_name" :value="old('first_name')" placeholder="First Name" required
                                autofocus />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                        </div>

                        <div>
                            <x-text-input id="last_name"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                                type="text" name="last_name" :value="old('last_name')" placeholder="Last Name" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                        </div>

                        <div>
                            <x-text-input id="middle_name"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                                type="text" name="middle_name" :value="old('middle_name')" placeholder="Middle Name (Optional)" />
                            <x-input-error :messages="$errors->get('middle_name')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <x-text-input id="email"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="email" name="email" :value="old('email')" placeholder="Email" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Credentials Upload -->
                    <div class="space-y-4">
                        <div>
                            <label for="credentials" class="block mb-1 text-sm font-medium text-gray-700">BIR Form 2303</label>
                            <input id="credentials"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                                type="file" name="credentials" required />
                            <div class="mt-1 text-xs text-gray-400">Image or PDF (Maximum of 20MB)</div>
                            <x-input-error :messages="$errors->get('credentials')" class="mt-1" />
                        </div>

                        <div>
                            <label for="credentials2" class="block mb-1 text-sm font-medium text-gray-700">SEC Registration</label>
                            <input id="credentials2"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                                type="file" name="credentials2" required />
                            <div class="mt-1 text-xs text-gray-400">Image or PDF (Maximum of 20MB)</div>
                            <x-input-error :messages="$errors->get('credentials2')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Password Fields -->
                    <div class="space-y-4">
                        <div>
                            <x-text-input id="password"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                                type="password" name="password" placeholder="Password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <div>
                            <x-text-input id="password_confirmation"
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                                type="password" name="password_confirmation" placeholder="Confirm Password" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Register Button -->
                    <button type="submit"
                        class="w-full px-4 py-3 mt-6 text-white transition-colors duration-200 bg-green-600 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Register
                    </button>

                    <!-- Login Link -->
                    <p class="mt-4 text-sm text-center text-gray-600">
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