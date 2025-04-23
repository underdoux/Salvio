@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-800">Products</h1>
    <a href="{{ route('products.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add New Product</a>
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="border px-4 py-2">Name</th>
                <th class="border px-4 py-2">Category</th>
                <th class="border px-4 py-2">BPOM Code</th>
                <th class="border px-4 py-2">Price</th>
                <th class="border px-4 py-2">Stock</th>
                <th class="border px-4 py-2">Is By Order</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr>
                <td class="border px-4 py-2">{{ $product->name }}</td>
                <td class="border px-4 py-2">{{ $product->category->name ?? 'N/A' }}</td>
                <td class="border px-4 py-2">{{ $product->bpom_code ?? '-' }}</td>
                <td class="border px-4 py-2">${{ number_format($product->price, 2) }}</td>
                <td class="border px-4 py-2">{{ $product->stock }}</td>
                <td class="border px-4 py-2">{{ $product->is_by_order ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
