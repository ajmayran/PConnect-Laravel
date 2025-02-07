<x-app-layout>
    <div class="container">
        <h1>Your Products</h1>
        <a href="{{ route('distributors.products.create') }}" class="p-2 bg-green-500 btn btn-primary">Add New Product</a>
        <table class="table mt-2">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock Quantity</th>
                    <th>Minimum Purchase Quantity</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            <img src="{{ asset($product->image) }}" alt="{{ $product->product_name }}"
                                style="width: 50px; height: auto;">
                        </td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->description }}</td>
                        <td>${{ $product->price }}</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>{{ $product->minimum_purchase_qty }}</td>
                        <td>
                            {{ $categories->firstWhere('id', $product->category_id)->name ?? 'N/A' }}
                        </td>
                        <td>{{ $product->status }}</td>
                        <td>
                            <a href="{{ route('distributors.products.show', $product->id) }}"
                                class="btn btn-info">View</a>
                            <a href="{{ route('distributors.products.edit', $product->id) }}"
                                class="btn btn-warning">Edit</a>
                            <form action="{{ route('distributors.products.destroy', $product->id) }}" method="POST"
                                style="display:inline;">
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
</x-app-layout>
