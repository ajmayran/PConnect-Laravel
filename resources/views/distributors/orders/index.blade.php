@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Your Orders</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user->name }}</td>
                <td>${{ $order->total_amount }}</td>
                <td>{{ $order->status }}</td>
                <td>
                    <a href="{{ route('distributors.orders.show', $order->id) }}" class="btn btn-info">View</a>
                    <form action="{{ route('distributors.orders.destroy', $order->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Cancel Order</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
