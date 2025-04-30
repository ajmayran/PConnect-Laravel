<x-guest-layout>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('img/Pconnect Logo.png') }}" alt="PConnect Logo" class="w-auto h-16">
        </div>

        <!-- Email Verification Message -->
        <div class="p-4 mb-6 border-l-4 border-green-500 rounded-md bg-green-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">
                        @if(auth()->user()->user_type === 'distributor')
                            {{ __('Thanks for signing up as a distributor! Before we can review your account, please verify your email address by clicking on the link we just emailed to you.') }}
                        @else
                            {{ __('Thanks for signing up as a retailer! Before getting started, please verify your email address by clicking on the link we just emailed to you.') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="p-4 mb-6 border-l-4 border-green-500 rounded-md bg-green-50">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Email Instructions -->
        <div class="p-4 mb-6 rounded-md bg-gray-50">
            <h3 class="text-sm font-medium text-gray-700">{{ __('If you didn\'t receive the email:') }}</h3>
            <ul class="mt-2 ml-4 text-sm text-gray-600 list-disc">
                <li>{{ __('Check your spam or junk folder') }}</li>
                <li>{{ __('Make sure you entered your email address correctly') }}</li>
                <li>{{ __('Click the "Resend Verification Email" button below') }}</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full md:w-auto">
                @csrf
                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white transition-colors duration-150 bg-green-500 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        {{ __('Resend Verification Email') }}
                    </div>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full md:w-auto">
                @csrf
                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-150 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>

    <!-- JavaScript for Redirect -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @auth
                if ({{ auth()->user()->hasVerifiedEmail() ? 'true' : 'false' }}) {
                    window.location.href = "{{ route(auth()->user()->user_type . '.dashboard') }}";
                }
            @endauth
        });
    </script>
</x-guest-layout>
