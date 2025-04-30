@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Products</h1>
        @hasrole('Admin|Sales')
        <a href="{{ route('products.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Add New Product
        </a>
        @endhasrole
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" placeholder="Search products..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category" name="category"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Filter -->
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                <select id="stock" name="stock"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Stock</option>
                    <option value="low">Low Stock</option>
                    <option value="out">Out of Stock</option>
                    <option value="by_order">By Order Only</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BPOM Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    @hasrole('Admin|Sales')
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    @endhasrole
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="products-table">
                @foreach ($products as $product)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->bpom_code ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($product->is_by_order)
                            <span class="text-blue-600">By Order</span>
                        @else
                            <span class="@if($product->stock <= 10) text-red-600 @endif">
                                {{ $product->stock }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($product->is_by_order)
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">By Order</span>
                        @elseif($product->stock <= 0)
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Out of Stock</span>
                        @elseif($product->stock <= 10)
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">In Stock</span>
                        @endif
                    </td>
                    @hasrole('Admin|Sales')
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </td>
                    @endhasrole
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const stockSelect = document.getElementById('stock');

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const fetchProducts = debounce(async () => {
        const searchQuery = searchInput.value;
        const categoryId = categorySelect.value;
        const stockStatus = stockSelect.value;

        try {
            const response = await fetch(`/products/search?query=${searchQuery}&category=${categoryId}&stock=${stockStatus}`);
            const products = await response.json();
            updateProductsTable(products);
        } catch (error) {
            console.error('Error fetching products:', error);
        }
    }, 300);

    function updateProductsTable(products) {
        const tbody = document.getElementById('products-table');
        tbody.innerHTML = products.map(product => `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">${product.name}</td>
                <td class="px-6 py-4 whitespace-nowrap">${product.category ? product.category.name : 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap">${product.bpom_code || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap">Rp ${product.price.toLocaleString('id-ID')}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${product.is_by_order
                        ? '<span class="text-blue-600">By Order</span>'
                        : `<span class="${product.stock <= 10 ? 'text-red-600' : ''}">${product.stock}</span>`
                    }
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${getStatusBadge(product)}
                </td>
                @hasrole('Admin|Sales')
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="/products/${product.id}/edit" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                    <form action="/products/${product.id}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </td>
                @endhasrole
            </tr>
        `).join('');
    }

    function getStatusBadge(product) {
        if (product.is_by_order) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">By Order</span>';
        } else if (product.stock <= 0) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Out of Stock</span>';
        } else if (product.stock <= 10) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>';
        } else {
            return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">In Stock</span>';
        }
    }

    searchInput.addEventListener('input', fetchProducts);
    categorySelect.addEventListener('change', fetchProducts);
    stockSelect.addEventListener('change', fetchProducts);
});
</script>
@endpush
@endsection
