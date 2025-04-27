{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\tickets\show.blade.php --}}
<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 ml-64 p-4">
            @if (session('success'))
                <div class="relative px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <div class="container px-4 py-8 mx-auto">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="px-6 py-4 bg-gray-800">
                        <h1 class="text-2xl font-bold text-white">Ticket Details</h1>
                    </div>
                    <div class="p-6">
                        <h2 class="text-xl font-bold">{{ $ticket->subject }}</h2>
                        <p>{{ $ticket->content }}</p>

                        @if ($ticket->image)
                            <div class="mt-4">
                                <h3 class="text-lg font-bold">Attached Image</h3>
                                <img src="{{ asset('storage/' . $ticket->image) }}" alt="Ticket Image" class="max-w-full h-auto cursor-pointer" style="max-width: 200px;" onclick="openModal('{{ asset('storage/' . $ticket->image) }}')">
                            </div>
                        @endif

                        @if ($ticket->status === 'rejected')
                            <div class="mt-4">
                                <h3 class="text-lg font-bold text-red-600">Rejection Reason</h3>
                                <p>{{ $ticket->rejection_reason }}</p>
                            </div>
                        @elseif($ticket->status === 'resolved')
                            <div class="mt-4">
                                <h3 class="text-lg font-bold text-green-600">This ticket has been resolved.</h3>
                            </div>
                        @else
                            <form action="{{ route('admin.tickets.resolve', $ticket->id) }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-700">Resolve</button>
                            </form>

                            <form action="{{ route('admin.tickets.reject', $ticket->id) }}" method="POST" class="mt-4">
                                @csrf
                                <label for="rejection_reason" class="block mb-2 text-sm font-medium text-gray-700">Rejection Reason</label>
                                <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                                <button type="submit" class="px-4 py-2 mt-2 text-white bg-red-500 rounded-md hover:bg-red-700">Reject</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <img id="modalImage" src="" alt="Ticket Image" class="w-full h-auto">
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal()" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-500 border border-transparent rounded-md shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
    </script>

    <!-- Include Admin Dashboard Scripts -->
    @vite(['resources/js/admin_dash.js'])
</x-app-layout>