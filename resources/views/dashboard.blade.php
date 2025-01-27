<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}

                    <!-- User Details -->
                    <div class="mt-4">
                        <h3 class="text-lg font-bold">User Information</h3>
                        <p><strong>Name:</strong> {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>

                        <!-- Credential Display -->
                        @if (Auth::user()->credential)
                            <a href="{{ route('download.credential') }}" class="btn btn-primary">
                                Download Credential
                            </a>
                        @else
                            <p>No credential uploaded yet.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
