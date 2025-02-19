<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <h1 class="mb-4 text-2xl font-bold">Payments</h1>
        <table class="min-w-full bg-white rounded-lg shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2">Order ID</th>
                    <th class="px-4 py-2">Payment Status</th>
                    <th class="px-4 py-2">Paid At</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $payment->order->formatted_order_id ?? $payment->order_id }}</td>
                        <td class="px-4 py-2">{{ ucfirst($payment->payment_status) }}</td>
                        <td class="px-4 py-2">
                            @if($payment->paid_at)
                                {{ \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if($payment->payment_status == 'unpaid')
                                <a href="{{ route('payment.create', $payment->order_id) }}" class="text-blue-500">Confirm Payment</a>
                            @else
                                <span class="text-green-500">Paid</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-distributor-layout>