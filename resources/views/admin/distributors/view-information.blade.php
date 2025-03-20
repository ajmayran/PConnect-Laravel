{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\distributors\view-information.blade.php --}}
<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 ml-64 p-4">
            <div class="container px-4 py-8 mx-auto">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="px-6 py-4 bg-gray-800">
                        <h1 class="text-2xl font-bold text-white">Distributor Information</h1>
                    </div>
                    <div class="p-6">
                        {{-- Distributor Information --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Field</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Value</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">First Name</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->first_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">Middle Name</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->middle_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">Last Name</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">Email</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $distributor->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">BIR Form</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            @php
                                                $birForm = $distributor->credentials->first(function ($credential) {
                                                    return str_contains($credential->file_path, 'credentials/bir/');
                                                });
                                            @endphp
                                            @if ($birForm)
                                                <a href="{{ asset('storage/' . $birForm->file_path) }}" target="_blank" class="text-blue-500 hover:underline">View BIR Form</a>
                                            @else
                                                <span class="text-gray-500">No BIR Form uploaded</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">SEC Document</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            @php
                                                $secDocument = $distributor->credentials->first(function ($credential) {
                                                    return str_contains($credential->file_path, 'credentials/sec/');
                                                });
                                            @endphp
                                            @if ($secDocument)
                                                <a href="{{ asset('storage/' . $secDocument->file_path) }}" target="_blank" class="text-blue-500 hover:underline">View SEC Document</a>
                                            @else
                                                <span class="text-gray-500">No SEC Document uploaded</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- Include Admin Dashboard Scripts -->
        @vite(['resources/js/admin_dash.js'])
</x-app-layout>