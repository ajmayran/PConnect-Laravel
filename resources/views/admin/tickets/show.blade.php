{{-- filepath: /c:/Users/nunez/Documents/PConnect-Laravel/resources/views/admin/tickets/show.blade.php --}}
@extends('layouts.app')

@section('content')
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
                        <button type="submit" class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-700">Reject</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection