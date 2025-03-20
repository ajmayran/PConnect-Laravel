<x-guest-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="mb-6 text-2xl font-semibold text-center">Application Under Review</h1>
                    
                    @if(session('message'))
                        <div class="p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500" role="alert">
                            <p>{{ session('message') }}</p>
                        </div>
                    @endif
                    
                    <div class="text-center">
                        <svg class="w-24 h-24 mx-auto text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        
                        <h2 class="mt-4 text-lg font-medium">Email Verified Successfully!</h2>
                        <p class="mt-2 text-gray-600">Thank you for verifying your email address.</p>
                        
                        <div class="p-6 mt-8 rounded-lg bg-gray-50">
                            <h3 class="text-lg font-medium">Next Steps:</h3>
                            <div class="mt-4 space-y-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-700">Your application is being reviewed by our team</p>
                                    </div>
                                </div>
                                
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-700">This process typically takes 24-48 hours</p>
                                    </div>
                                </div>
                                
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-700">You'll be notified by email once your account is approved</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-gray-800 border border-transparent rounded-md hover:bg-gray-700">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>