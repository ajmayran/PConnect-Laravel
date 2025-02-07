<x-guest-layout>
    <div class="flex justify-center mb-4 space-x-4">
        <button id="retailerButton" class="px-4 py-2 text-white bg-green-600 rounded-lg focus:outline-none">Register as
            Retailer</button>
        <button id="distributorButton"
            class="px-4 py-2 text-white bg-gray-400 rounded-lg focus:outline-none hover:bg-gray-200 hover:text-black">Register
            as Distributor</button>
    </div>
    <div
        class="flex items-center justify-center min-h-screen px-4 py-12 bg-gradient-to-r from-green-400 to-green-600 sm:px-6 lg:px-8">
        <div class="w-full max-w-3xl p-8 space-y-8 bg-white shadow-2xl rounded-2xl" id="retailerForm">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-xl font-bold text-gray-900">Register as Retailer</h2>
                <p class="mt-2 text-sm text-gray-600">Create your account to get started</p>
            </div>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf
                <input type="hidden" name="user_type" value="retailer">
                <!-- Name Fields Grid -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- First Name -->
                    <div>
                        <x-text-input id="first_name"
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="text" name="first_name" :value="old('first_name')" placeholder="First Name" required
                            autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <!-- Last Name -->
                    <div>
                        <x-text-input id="last_name"
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="text" name="last_name" :value="old('last_name')" placeholder="Last Name" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>

                <!-- Middle Name -->
                <div>
                    <x-text-input id="middle_name"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                        type="text" name="middle_name" :value="old('middle_name')" placeholder="Middle Name (Optional)" />
                    <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-text-input id="email"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                        type="email" name="email" :value="old('email')" placeholder="Email" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Credentials Upload -->
                <div>
                    <label for="Credentials" class="text-xs">Permit ( Mayor's, Business )</label>
                    <input id="credentials"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                        type="file" name="credentials" placeholder="Upload Credentials (Image, PDF)" required />
                    <div class="text-xs text-right text-gray-400">Image or PDF ( Maximum of 20MB )</div>
                    <x-input-error :messages="$errors->get('credentials')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-text-input id="password"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                        type="password" name="password" placeholder="Password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-text-input id="password_confirmation"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                        type="password" name="password_confirmation" placeholder="Confirm Password" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Register Button -->
                <button type="submit"
                    class="w-full px-4 py-3 text-white transition-colors duration-200 bg-green-600 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
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
        <div class="hidden w-full max-w-3xl p-8 space-y-8 bg-white shadow-2xl rounded-2xl" id="distributorForm">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-xl font-bold text-gray-900">Register as Distributor</h2>
                <p class="mt-2 text-sm text-gray-600">Create your account to get started</p>
            </div>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf

                <input type="hidden" name="user_type" value="distributor">
                <!-- Name Fields Grid -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- First Name -->
                    <div>
                        <x-text-input id="first_name"
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="text" name="first_name" :value="old('first_name')" placeholder="First Name" required
                            autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <!-- Last Name -->
                    <div>
                        <x-text-input id="last_name"
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                            type="text" name="last_name" :value="old('last_name')" placeholder="Last Name" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>

                <!-- Middle Name -->
                <div>
                    <x-text-input id="middle_name"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                        type="text" name="middle_name" :value="old('middle_name')" placeholder="Middle Name (Optional)" />
                    <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-text-input id="email"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                        type="email" name="email" :value="old('email')" placeholder="Email" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Credentials Upload -->
                <div>
                    <label for="Credentials" class="text-xs">Credentials ( BIR and SEC )</label>
                    <input id="credentials"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                        type="file" name="credentials" placeholder="Upload Credentials (Image, PDF)" required />
                    <x-input-error :messages="$errors->get('credentials')" class="mt-2" />
                    <div class="text-xs text-right text-gray-400">Image or PDF ( Maximum of 20MB )</div>
                </div>

                <!-- Password -->
                <div>
                    <x-text-input id="password"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                        type="password" name="password" placeholder="Password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-text-input id="password_confirmation"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500"
                        type="password" name="password_confirmation" placeholder="Confirm Password" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Register Button -->
                <button type="submit"
                    class="w-full px-4 py-3 text-white transition-colors duration-200 bg-green-600 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Register
                </button>

                <!-- Login Link -->
                <p class="text-sm text-center text-gray-600">Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500">
                        Login
                    </a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const distributorButton = document.getElementById('distributorButton');
        const retailerButton = document.getElementById('retailerButton');
        const distributorForm = document.getElementById('distributorForm');
        const retailerForm = document.getElementById('retailerForm');

        distributorButton.addEventListener('click', () => {
            distributorForm.classList.remove('hidden');
            retailerForm.classList.add('hidden');
            retailerButton.classList.remove('bg-green-600')
            retailerButton.classList.add('bg-gray-400', 'hover:bg-gray-400', 'hover:text-black');
            distributorButton.classList.remove('bg-gray-400', 'hover:bg-gray-400', 'hover:text-black');
            distributorButton.classList.add('bg-green-600', 'hover:bg-green-400')
        });

        retailerButton.addEventListener('click', () => {
            retailerForm.classList.remove('hidden');
            distributorForm.classList.add('hidden');
            distributorButton.classList.remove('bg-green-600')
            distributorButton.classList.add('bg-gray-400', 'hover:bg-gray-400', 'hover:text-black');
            retailerButton.classList.remove('bg-gray-400', 'hover:bg-gray-400', 'hover:text-black');
            retailerButton.classList.add('bg-green-600', 'hover:bg-green-400')
        });
    });
</script>
