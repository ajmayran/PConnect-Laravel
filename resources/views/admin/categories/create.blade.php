{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\categories\create.blade.php --}}
<x-app-layout>
    <div class="flex min-h-screen">
        {{-- Include the admin sidebar --}}
        <div class="w-64 bg-green-200">
            @include('components.admin-sidebar')
        </div>

        {{-- Main content area --}}
        <div class="flex-1 p-6 bg-gray-50">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Add Category</h1>

            {{-- Add Category Form --}}
            <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white p-8 rounded-lg shadow-md border border-gray-200">
                @csrf
                <div class="mb-6">
                    <label for="name" class="block text-lg font-medium text-gray-700 mb-2">Category Name</label>
                    <input type="text" name="name" id="name" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:outline-none text-gray-800" 
                           placeholder="Enter category name" required>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('categories.index') }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition mr-4">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>
            <!-- Include Admin Dashboard Scripts -->
            @vite(['resources/js/admin_dash.js'])
</x-app-layout>