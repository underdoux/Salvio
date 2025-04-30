@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Commission Rules</h1>
        @can('configure commissions')
        <button
            onclick="document.getElementById('createRuleModal').classList.remove('hidden')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        >
            Add New Rule
        </button>
        @endcan
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Type
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Reference
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Rate (%)
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Min Amount
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Max Amount
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    @can('configure commissions')
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                    @endcan
                </tr>
            </thead>
            <tbody class="bg-white">
                @foreach($rules as $rule)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                        {{ ucfirst($rule->type) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                        @if($rule->type === 'global')
                            Global
                        @elseif($rule->type === 'category')
                            {{ $rule->reference->name ?? 'N/A' }}
                        @else
                            {{ $rule->reference->name ?? 'N/A' }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                        {{ number_format($rule->rate, 2) }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                        {{ $rule->min_amount ? 'Rp ' . number_format($rule->min_amount, 2) : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                        {{ $rule->max_amount ? 'Rp ' . number_format($rule->max_amount, 2) : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                        @if($rule->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </td>
                    @can('configure commissions')
                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                        <button
                            onclick="editRule({{ $rule->id }})"
                            class="text-blue-600 hover:text-blue-900 mr-3"
                        >
                            Edit
                        </button>
                        <form action="{{ route('commissions.rules.delete', $rule) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </form>
                    </td>
                    @endcan
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Create Rule Modal -->
    <div id="createRuleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Create Commission Rule</h3>
                <form action="{{ route('commissions.rules.create') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Type</label>
                        <select name="type" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="global">Global</option>
                            <option value="category">Category</option>
                            <option value="product">Product</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Rate (%)</label>
                        <input type="number" name="rate" step="0.01" min="0" max="100"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Min Amount</label>
                        <input type="number" name="min_amount" step="0.01" min="0"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Max Amount</label>
                        <input type="number" name="max_amount" step="0.01" min="0"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>

                    <div class="flex justify-end">
                        <button type="button"
                                onclick="document.getElementById('createRuleModal').classList.add('hidden')"
                                class="bg-gray-500 text-white px-4 py-2 rounded mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editRule(ruleId) {
    // Implement edit functionality
    console.log('Edit rule:', ruleId);
}
</script>
@endpush
@endsection
