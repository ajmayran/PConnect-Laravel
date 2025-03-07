<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-3xl p-8 mx-auto space-y-8 bg-white shadow-2xl rounded-2xl">
            <div class="text-center">
                <h2 class="text-xl font-bold text-gray-900">Application Under Review</h2>
                <div class="mt-4">
                    <div class="flex justify-center">
                        <svg class="w-16 h-16 text-green-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="mt-4 text-gray-600">
                        Thank you for registering as a distributor. Your application is currently under review.
                        We will notify you via email once your account has been approved.
                    </p>
                    <p class="mt-4 text-sm text-gray-500">
                        This usually takes 1-2 business days.
                    </p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                        ← Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>