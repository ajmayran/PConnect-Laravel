{{-- filepath: /c:/Users/nunez/Documents/PConnect-Laravel/resources/views/retailers/tickets/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container px-4 py-8 mx-auto">
        <h1 class="text-2xl font-bold">Create Ticket</h1>
        @if (session('success'))
            <div class="relative px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        <div class="overflow-hidden bg-white rounded-lg shadow-lg mt-4">
            <div class="p-6">
                <form action="{{ route('retailers.tickets.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" id="subject" required
                            class="block w-full px-4 py-2 mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                        <textarea name="content" id="content" rows="4" required
                            class="block w-full px-4 py-2 mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 font-medium text-white bg-green-600 rounded hover:bg-green-700">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection