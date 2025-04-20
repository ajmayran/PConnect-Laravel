<!-- filepath: c:\xampp\htdocs\PConnect-Laravel\resources\views\retailers\credentials-reupload.blade.php -->
<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="mb-4 text-2xl font-semibold text-center">Credentials Verification Required</h1>
                    
                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if($status === 'pending')
                        <div class="p-4 mb-6 text-sm text-blue-700 bg-blue-100 border-l-4 border-blue-500 rounded-lg">
                            <p class="font-bold">Your credentials are currently under review</p>
                            <p class="mt-2">Please wait while our admin team verifies your submission. You'll be notified once your credentials are approved.</p>
                            
                            <div class="flex items-center mt-4">
                                <svg class="w-5 h-5 text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="ml-2">Processing</span>
                            </div>
                        </div>
                    @else
                        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border-l-4 border-red-500 rounded-lg">
                            <p class="font-bold">Your credentials have been rejected for the following reason:</p>
                            <p class="mt-2">{{ $rejection_reason ?? 'Invalid business permit or documentation' }}</p>
                        </div>

                        <div class="p-4 mb-6 text-sm text-gray-700 bg-gray-100 border-l-4 border-gray-500 rounded-lg">
                            <p>To continue using PConnect, please upload a valid Business Permit or Mayor's Permit.</p>
                            <p class="mt-2">Our admin team will review your documentation and restore your access once verified.</p>
                        </div>

                        <form method="POST" action="{{ route('retailers.credentials.process-reupload') }}" enctype="multipart/form-data" class="mt-4">
                            @csrf
                            <div class="mb-6">
                                <label for="credentials" class="block mb-2 text-sm font-medium text-gray-700">Business Permit / Mayor's Permit</label>
                                <input type="file" id="credentials" name="credentials" accept=".pdf,.jpg,.jpeg,.png" 
                                    class="block w-full text-sm text-gray-900 bg-gray-100 border border-gray-300 rounded-lg cursor-pointer focus:outline-none">
                                @error('credentials')
                                    <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Upload your business permit (PDF, JPG, PNG, max 5MB)</p>
                            </div>

                            <div class="flex justify-center">
                                <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Upload Credentials
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="flex justify-center mt-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 underline hover:text-gray-900">
                    Log Out
                </button>
            </form>
        </div>
    </div>

    @if($status === 'pending')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Show a SweetAlert notification
                Swal.fire({
                    icon: 'info',
                    title: 'Credentials Under Review',
                    text: 'Your credentials are already submitted and are being reviewed by our admin team.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif
</x-app-layout>