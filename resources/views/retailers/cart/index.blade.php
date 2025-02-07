@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Your Cart</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($carts as $cart)
            <tr>
                <td>{{ $cart->product->name }}</td>
                <td>
                    <form action="{{ route('carts.update', $cart->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" required>
                        <button type="submit" class="btn btn-secondary">Update</button>
                    </form>
                </td>
                <td>${{ $cart->product->price }}</td>
                <td>
                    <form action="{{ route('carts.remove', $cart->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Remove</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('checkout') }}" class="btn btn-primary">Proceed to Checkout</a>
</div>
@endsection
