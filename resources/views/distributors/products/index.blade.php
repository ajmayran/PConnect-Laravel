@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Your Products</h1>
    <a href="{{ route('distributors.products.create') }}" class="btn btn-primary">Add New Product</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->description }}</td>
                <td>${{ $product->price }}</td>
                <td>{{ $product->is_approved ? 'Approved' : 'Pending Approval' }}</td>
                <td>
                    <a href="{{ route('distributors.products.show', $product->id) }}" class="btn btn-info">View</a>
                    <a href="{{ route('distributors.products.edit', $product->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('distributors.products.destroy', $product->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
