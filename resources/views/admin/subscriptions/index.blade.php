<!-- filepath: c:\Users\EMMAN\Documents\PConnect-Laravel\resources\views\admin\subscriptions\index.blade.php -->
<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 p-4 ml-64">
            @if (session('error'))
                <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <div class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center justify-between mb-6">
                                <h1 class="text-2xl font-semibold">Distributor Subscriptions</h1>
                                <!-- Download PDF Button -->
                                <button id="downloadSubscriptionsPdf" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                                    Download PDF
                                </button>
                            </div>
                            
                            <!-- Wrapper for Stats and Table -->
                            <div id="subscriptionsContentWrapper">
                                <!-- Stats cards -->
                                <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
                                    <div class="p-4 bg-white border rounded-lg shadow">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h2 class="text-sm font-medium text-gray-600">Total</h2>
                                                <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                                            </div>
                                            <div class="p-3 bg-blue-100 rounded-full">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 bg-white border rounded-lg shadow">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h2 class="text-sm font-medium text-gray-600">Active</h2>
                                                <p class="text-2xl font-bold">{{ $stats['active'] }}</p>
                                            </div>
                                            <div class="p-3 bg-green-100 rounded-full">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 bg-white border rounded-lg shadow">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h2 class="text-sm font-medium text-gray-600">Expired/Cancelled</h2>
                                                <p class="text-2xl font-bold">{{ $stats['expired'] }}</p>
                                            </div>
                                            <div class="p-3 bg-red-100 rounded-full">
                                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 bg-white border rounded-lg shadow">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h2 class="text-sm font-medium text-gray-600">Pending</h2>
                                                <p class="text-2xl font-bold">{{ $stats['pending'] }}</p>
                                            </div>
                                            <div class="p-3 bg-yellow-100 rounded-full">
                                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Subscriptions Table -->
                                <div id="subscriptionsTableWrapper" class="overflow-auto bg-white rounded-lg shadow">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Distributor
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Plan
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Amount Paid
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Status
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Start Date
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Expiry Date
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($subscriptions as $subscription)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ $subscription->distributor->company_name ?? 'N/A' }}
                                                                </div>
                                                                <div class="text-sm text-gray-500">
                                                                    {{ $subscription->distributor->user->email ?? 'N/A' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                        {{ $subscription->plan_name }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                        ₱{{ number_format($subscription->amount_paid, 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                               ($subscription->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                               ($subscription->status === 'expired' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')) }}">
                                                            {{ ucfirst($subscription->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                        {{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : 'Not started' }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                        {{ $subscription->expires_at ? $subscription->expires_at->format('Y-m-d') : 'N/A' }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                                        <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="mr-2 text-indigo-600 hover:text-indigo-900">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $subscriptions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const downloadButton = document.getElementById('downloadSubscriptionsPdf');
            const contentWrapper = document.getElementById('subscriptionsContentWrapper');

            if (downloadButton) {
                downloadButton.addEventListener('click', function () {
                    html2canvas(contentWrapper, { scale: 2 }).then(canvas => {
                        const imageData = canvas.toDataURL('image/png'); // Convert the canvas to an image

                        // Create a PDF
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF('p', 'mm', 'a4'); // Portrait, millimeters, A4 size

                        // Calculate dimensions to fit the image in the PDF
                        const pdfWidth = pdf.internal.pageSize.getWidth();
                        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                        // Add the image to the PDF
                        pdf.addImage(imageData, 'PNG', 0, 0, pdfWidth, pdfHeight);

                        // Download the PDF
                        pdf.save('subscriptions.pdf');
                    }).catch(error => {
                        console.error('Error generating PDF:', error);
                    });
                });
            }
        });
    </script>
      @vite(['resources/js/admin_dash.js'])
</x-app-layout>