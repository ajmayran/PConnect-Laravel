<x-app-layout>
    <x-dashboard-nav />
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Admin Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <x-retailer-sidebar :user="Auth::user()" />

        <div class="flex-1 space-y-6 lg:px-8">
            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('retailers.profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('retailers.profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-footer />