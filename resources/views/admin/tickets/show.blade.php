{{-- filepath: /c:/Users/nunez/Documents/PConnect-Laravel/resources/views/admin/tickets/show.blade.php --}}
<x-app-layout>
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

                @if ($ticket->status === 'rejected')
                    <div class="mt-4">
                        <h3 class="text-lg font-bold text-red-600">Rejection Reason</h3>
                        <p>{{ $ticket->rejection_reason }}</p>
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
</x-app-layout>