{{-- filepath: /c:/Users/nunez/Documents/PConnect-Laravel/resources/views/admin/tickets/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container px-4 py-8 mx-auto">
        <div class="overflow-hidden bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 bg-gray-800">
                <h1 class="text-2xl font-bold text-white">Ticket Details</h1>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <h2 class="text-xl font-bold">Title: {{ $ticket->subject }}</h2>
                    <p class="text-gray-700">User: {{ $ticket->user->id}}</p>
                    <p class="text-gray-700">First Name: {{ $ticket->user->first_name}}</p>
                    <p class="text-gray-700">Last Name: {{ $ticket->user->last_name}}</p>
                    <p class="text-gray-700">Content: {{ $ticket->content }}</p>
                </div>
                <div class="flex space-x-4">
                    <form action="{{ route('admin.tickets.resolve', $ticket->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="px-4 py-2 font-medium text-white bg-green-600 rounded hover:bg-green-700">
                            Resolve
                        </button>
                    </form>
                    <form action="{{ route('admin.tickets.reject', $ticket->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="text" name="rejection_reason" placeholder="Reason for rejection" required class="border rounded px-2 py-1">
                        <button type="submit" class="px-4 py-2 font-medium text-white bg-red-600 rounded hover:bg-red-700">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection