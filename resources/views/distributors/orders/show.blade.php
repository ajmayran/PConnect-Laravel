@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Order Details</h1>
    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>User:</strong> {{ $order->user->name }}</p>
    <p><strong>Total Amount:</strong> ${{ $order->total_amount }}</p>
    <p><strong>Status:</strong> {{ $order->status }}</p>

    <h3>Products</h3>
    <ul>
        @foreach ($order->products as $product)
            <li>{{ $product->name }} - Quantity: {{ $product->pivot->quantity }}</li>
        @endforeach
    </ul>

    <a href="{{ route('distributors.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
</div>
@endsection
