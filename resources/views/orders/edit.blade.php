<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar (same as other views) -->
        <aside class="w-64 bg-white shadow-md">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Salvio Admin</h2>
                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Dashboard</a>
                    <a href="{{ route('products.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Products</a>
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 rounded bg-blue-100 text-blue-700 font-semibold">Orders</a>
                    <a href="{{ route('commissions.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Commissions</a>
                    <a href="{{ route('profit-distributions.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Profit Distributions</a>
                    <a href="{{ route('notifications.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Notifications</a>
                    <a href="{{ route('reports.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Reports</a>
                    <a href="{{ route('insights.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 font-semibold">Insights</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Edit Order #{{ $order->id }}</h1>
                        <p class="mt-1 text-sm text-gray-600">Created on {{ $order->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <a href="{{ route('orders.show', $order) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Order
                    </a>
                </div>

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Edit Form -->
                <form action="{{ route('orders.update', $order) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Order Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Order Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach(App\Models\Order::VALID_STATUSES as $status)
                                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Payment Type -->
                            <div>
                                <label for="payment_type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                                <select name="payment_type" id="payment_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="cash" {{ $order->payment_type === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="installment" {{ $order->payment_type === 'installment' ? 'selected' : '' }}>Installment</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Items</h3>
                        <div id="order-items" class="space-y-4">
                            @foreach($order->items as $index => $item)
                                <div class="grid grid-cols-12 gap-4 items-start border-b border-gray-200 pb-4">
                                    <!-- Product -->
                                    <div class="col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">Product</label>
                                        <select name="items[{{ $index }}][product_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ $item->product_id === $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Original Price -->
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Original Price</label>
                                        <input type="number" step="0.01" name="items[{{ $index }}][original_price]" value="{{ $item->original_price }}" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <!-- Adjusted Price -->
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Adjusted Price</label>
                                        <input type="number" step="0.01" name="items[{{ $index }}][adjusted_price]" value="{{ $item->adjusted_price }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <p class="mt-1 text-xs text-gray-500">Max discount: {{ $maxDiscount }}%</p>
                                    </div>

                                    <!-- Adjustment Reason -->
                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium text-gray-700">Adjustment Reason</label>
                                        <input type="text" name="items[{{ $index }}][adjustment_reason]" value="{{ $item->adjustment_reason }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="col-span-1">
                                        <button type="button" onclick="removeItem(this)" class="mt-6 text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Add Item Button -->
                        <button type="button" onclick="addItem()" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function addItem() {
            const container = document.getElementById('order-items');
            const index = container.children.length;
            const template = `
                <div class="grid grid-cols-12 gap-4 items-start border-b border-gray-200 pb-4">
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Product</label>
                        <select name="items[${index}][product_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Original Price</label>
                        <input type="number" step="0.01" name="items[${index}][original_price]" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Adjusted Price</label>
                        <input type="number" step="0.01" name="items[${index}][adjusted_price]"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Max discount: {{ $maxDiscount }}%</p>
                    </div>
                    <div class="col-span-4">
                        <label class="block text-sm font-medium text-gray-700">Adjustment Reason</label>
                        <input type="text" name="items[${index}][adjustment_reason]"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="col-span-1">
                        <button type="button" onclick="removeItem(this)" class="mt-6 text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
        }

        function removeItem(button) {
            button.closest('.grid').remove();
        }
    </script>
</body>
</html>
