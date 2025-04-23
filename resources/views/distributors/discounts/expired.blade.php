@if ($expiredDiscounts->isEmpty())
    <p class="text-center text-gray-500">No expired discounts found.</p>
@else
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Details</th>
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Period</th>
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Expired On</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($expiredDiscounts as $discount)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $discount->name }}</div>
                        @if ($discount->code)
                            <div class="text-xs text-gray-500">
                                Code: {{ $discount->code }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold {{ $discount->type === 'percentage' ? 'text-green-800 bg-green-100' : 'text-blue-800 bg-blue-100' }} rounded-full">
                            {{ $discount->type === 'percentage' ? 'Percentage Off' : 'Buy X Get Y Free' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if ($discount->type === 'percentage')
                            <div class="text-sm text-gray-900">{{ $discount->percentage }}% off</div>
                        @else
                            <div class="text-sm text-gray-900">Buy {{ $discount->buy_quantity }}, Get {{ $discount->free_quantity }} Free</div>
                        @endif
                        <div class="text-xs text-gray-500">
                            Applied to {{ $discount->products->count() }} products
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $discount->start_date->format('M d, Y h:i A') }} - {{ $discount->end_date->format('M d, Y h:i A') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                            Expired
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $discount->end_date->format('M d, Y h:i A') }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif