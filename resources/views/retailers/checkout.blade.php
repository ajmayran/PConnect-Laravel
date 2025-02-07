@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Checkout</h1>
    <form action="{{ route('carts.confirmOrder') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="delivery_address">Delivery Address</label>
            <input type="text" name="delivery_address" class="form-control" required>
        </div>

        @csrf
        <h3>Order Summary</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($carts as $cart)
                <tr>
                    <td>{{ $cart->product->name }}</td>
                    <td>{{ $cart->quantity }}</td>
                    <td>${{ $cart->product->price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <h4>Total Amount: ${{ $totalAmount }}</h4>        

        <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
        <button type="submit" class="btn btn-success">Place Order</button>
    </form>
</div>
@endsection
