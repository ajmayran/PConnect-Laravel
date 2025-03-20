{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\categories\edit.blade.php --}}
<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 ml-64 p-6">
            <h1 class="text-2xl font-bold mb-4 text-gray-800">Edit Category</h1>

            {{-- Edit Category Form --}}
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" name="name" id="name" value="{{ $category->name }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:outline-none" 
                           required>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('categories.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition mr-2">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
        <!-- Include Admin Dashboard Scripts -->
        @vite(['resources/js/admin_dash.js'])
</x-app-layout>