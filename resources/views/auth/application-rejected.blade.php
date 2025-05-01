<x-guest-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="mb-6 text-2xl font-semibold text-center">Application Rejected</h1>
                    
                    @if(session('message'))
                        <div class="p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500" role="alert">
                            <p>{{ session('message') }}</p>
                        </div>
                    @endif
                    
                    <div class="p-6 border-l-4 border-red-500 rounded-lg bg-red-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-red-700">Your distributor application has been rejected</h3>
                                @if($rejection_reason)
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Reason for rejection: {{ $rejection_reason }}</p>
                                    </div>
                                @else
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Your application did not meet our requirements. For more information, please contact our support team.</p>
                                    </div>
                                @endif
                                <div class="mt-4 text-sm">
                                    <p>For further assistance or to appeal this decision, please contact our support team at support@pconnect.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 text-center">
                        <p class="mb-4 text-gray-600">You may return to the homepage:</p>
                        <a href="/" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-green-600 border border-transparent rounded-md hover:bg-green-700">
                            Return to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>