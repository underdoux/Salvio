@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Preview Commission Rule: {{ $commissionRule->name }}</h2>
                    <a href="{{ route('commission-rules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to List
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Rule Details -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Rule Details</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <dt class="font-medium">Type:</dt>
                            <dd>{{ ucfirst($commissionRule->type) }}</dd>

                            <dt class="font-medium">Value:</dt>
                            <dd>{{ $commissionRule->type === 'percentage' ? $commissionRule->value . '%' : '$' . number_format($commissionRule->value, 2) }}</dd>

                            <dt class="font-medium">Status:</dt>
                            <dd>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $commissionRule->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $commissionRule->active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>

                            @if($commissionRule->conditions)
                                <dt class="font-medium col-span-2">Conditions:</dt>
                                <dd class="col-span-2">
                                    <ul class="list-disc list-inside space-y-1">
                                        @if(isset($commissionRule->conditions->min_order_value))
                                            <li>Minimum order value: ${{ number_format($commissionRule->conditions->min_order_value, 2) }}</li>
                                        @endif
                                        @if(isset($commissionRule->conditions->max_order_value))
                                            <li>Maximum order value: ${{ number_format($commissionRule->conditions->max_order_value, 2) }}</li>
                                        @endif
                                        @if(isset($commissionRule->conditions->minimum_quantity))
                                            <li>Minimum quantity: {{ $commissionRule->conditions->minimum_quantity }}</li>
                                        @endif
                                        @if(isset($commissionRule->conditions->maximum_quantity))
                                            <li>Maximum quantity: {{ $commissionRule->conditions->maximum_quantity }}</li>
                                        @endif
                                    </ul>
                                </dd>
                            @endif
                        </dl>
                    </div>

                    <!-- Commission Calculator -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Commission Calculator</h3>
                        <form id="calculator-form" class="space-y-4">
                            <div>
                                <x-input-label for="order_value" :value="__('Order Value ($)')" />
                                <x-text-input id="order_value" type="number" step="0.01" class="mt-1 block w-full" required />
                            </div>

                            <div>
                                <x-input-label for="quantity" :value="__('Quantity')" />
                                <x-text-input id="quantity" type="number" class="mt-1 block w-full" />
                            </div>

                            <div>
                                <x-input-label for="product_category" :value="__('Product Category')" />
                                <select id="product_category" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Category</option>
                                    <!-- Categories will be populated via JavaScript -->
                                </select>
                            </div>

                            <div class="flex justify-end">
                                <x-primary-button type="submit">
                                    Calculate Commission
                                </x-primary-button>
                            </div>
                        </form>

                        <div id="calculation-result" class="mt-4 hidden">
                            <div class="border-t pt-4">
                                <h4 class="font-semibold mb-2">Result:</h4>
                                <div id="result-content"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historical Data -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">Historical Analysis</h3>
                    <div id="historical-data" class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Summary</h4>
                            <div id="historical-summary" class="grid grid-cols-3 gap-4">
                                <!-- Summary will be populated via JavaScript -->
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Would Apply</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Potential Commission</th>
                                    </tr>
                                </thead>
                                <tbody id="historical-orders" class="bg-white divide-y divide-gray-200">
                                    <!-- Orders will be populated via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calculatorForm = document.getElementById('calculator-form');
    const resultDiv = document.getElementById('calculation-result');
    const resultContent = document.getElementById('result-content');

    // Load historical data
    fetch(`/commission-rules/{{ $commissionRule->id }}/test-historical`)
        .then(response => response.json())
        .then(data => {
            // Update summary
            const summary = document.getElementById('historical-summary');
            summary.innerHTML = `
                <div>
                    <div class="text-sm text-gray-500">Total Orders</div>
                    <div class="text-xl font-semibold">${data.summary.total_orders}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Applicable Orders</div>
                    <div class="text-xl font-semibold">${data.summary.applicable_orders}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Total Potential Commission</div>
                    <div class="text-xl font-semibold">$${data.summary.total_potential_commission.toFixed(2)}</div>
                </div>
            `;

            // Update orders table
            const ordersTable = document.getElementById('historical-orders');
            ordersTable.innerHTML = data.orders.map(order => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">${order.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${order.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap">$${order.total.toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${order.items_count}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${order.would_apply ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${order.would_apply ? 'Yes' : 'No'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">$${order.potential_commission.toFixed(2)}</td>
                </tr>
            `).join('');
        });

    // Handle calculator form submission
    calculatorForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {
            order_value: document.getElementById('order_value').value,
            quantity: document.getElementById('quantity').value,
            product_category: document.getElementById('product_category').value,
        };

        fetch(`/commission-rules/{{ $commissionRule->id }}/calculate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(formData),
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.classList.remove('hidden');
            if (data.applied) {
                resultContent.innerHTML = `
                    <div class="text-green-600">
                        <p class="font-semibold">Commission Amount: $${data.commission_amount.toFixed(2)}</p>
                    </div>
                `;
            } else {
                resultContent.innerHTML = `
                    <div class="text-red-600">
                        <p class="font-semibold">Commission not applicable</p>
                        <p class="text-sm">${data.reason}</p>
                    </div>
                `;
            }
        });
    });
});
</script>
@endpush
@endsection
