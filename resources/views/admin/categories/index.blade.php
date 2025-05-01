{{-- filepath: c:\Users\nunez\Documents\PConnect-Laravel\resources\views\admin\categories\index.blade.php --}}
<x-app-layout>
    <div class="flex min-h-screen">
        {{-- Include the admin sidebar --}}
        <div class="w-64 bg-green-200">
            @include('components.admin-sidebar')
        </div>

        {{-- Main content area --}}
        <div class="flex-1 p-6 bg-gray-50">
            <h1 class="text-2xl font-bold mb-4 text-gray-800">Manage Categories</h1>

            {{-- Add Category Button --}}
            <div class="mb-4">
                <a href="{{ route('admin.categories.create') }}" 
                   class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                    Add Category
                </a>
            </div>

            {{-- Categories Table --}}
            <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-md">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">ID</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Name</th>
                            <th class="px-4 py-2 text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $category->id }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $category->name }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                       class="text-blue-500 hover:underline">Edit</a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                          method="POST" 
                                          class="inline-block ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-500 hover:underline">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
            <!-- Include Admin Dashboard Scripts -->
            @vite(['resources/js/admin_dash.js'])
</x-app-layout>