<x-distributor-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="mb-6 text-2xl font-semibold text-gray-800">Blocked Retailers</h2>

                    @if(session('success'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($blockedRetailers->isEmpty())
                        <div class="p-4 text-lg text-gray-500">
                            No retailers have been blocked yet.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full border divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Retailer
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Store
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Reason
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($blockedRetailers as $block)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-10 h-10">
                                                        @if($block->retailer->profile_photo_path)
                                                            <img class="w-10 h-10 rounded-full" src="{{ asset('storage/' . $block->retailer->profile_photo_path) }}" alt="Profile photo">
                                                        @else
                                                            <div class="flex items-center justify-center w-10 h-10 text-white bg-gray-500 rounded-full">
                                                                {{ substr($block->retailer->first_name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $block->retailer->first_name }} {{ $block->retailer->last_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $block->retailer->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $block->retailer->retailerProfile->store_name ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $block->reason ?? 'Not specified' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                                <form action="{{ route('distributors.retailers.block', $block->retailer->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Unblock
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $blockedRetailers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-distributor-layout>