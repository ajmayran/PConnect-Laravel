{{-- filepath: /c:/Users/nunez/Documents/PConnect-Laravel/resources/views/distributors/tickets/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container px-4 py-8 mx-auto">
        <div class="overflow-hidden bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 bg-gray-800">
                <h1 class="text-2xl font-bold text-white">Create Ticket</h1>
            </div>
            <div class="p-6">
                <form action="{{ route('tickets.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" id="subject" required class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
                    </div>
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                        <textarea name="content" id="content" rows="5" required class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-700">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection