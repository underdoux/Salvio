@extends('layouts.app')

@section('title', 'Insights')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Insights Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Best Selling Products -->
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Best Selling Products</h2>
            <ul>
                @foreach($bestSelling as $product)
                <li class="mb-2">
                    {{ $product->product->name ?? 'Unknown' }} - Quantity Sold: {{ $product->quantity ?? 'N/A' }}
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Least Selling Products -->
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Least Selling Products</h2>
            <ul>
                @foreach($leastSelling as $product)
                <li class="mb-2">
                    {{ $product->product->name ?? 'Unknown' }} - Quantity Sold: {{ $product->quantity ?? 'N/A' }}
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Market Response -->
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Market Response by Customer Type</h2>
            <p>Data not available yet.</p>
        </div>
    </div>
</div>
@endsection
