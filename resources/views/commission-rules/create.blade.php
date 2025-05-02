@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Create Commission Rule</h2>
                    <a href="{{ route('commission-rules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                </div>

                @if($templates->isNotEmpty())
                    <div class="mb-6 p-4 bg-purple-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Available Templates</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($templates as $template)
                                <div class="p-4 border rounded-lg bg-white">
                                    <h4 class="font-semibold mb-2">{{ $template->name }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ $template->description ?? 'No description' }}</p>
                                    <div class="text-sm mb-2">
                                        <span class="font-medium">Type:</span> {{ ucfirst($template->type) }}<br>
                                        <span class="font-medium">Value:</span>
                                        {{ $template->type === 'percentage' ? $template->value . '%' : '$' . number_format($template->value, 2) }}
                                    </div>
                                    <form action="{{ route('commission-rules.versions.create-from-template', $template) }}" method="POST">
                                        @csrf
                                        <div class="mb-2">
                                            <x-text-input name="name" type="text" class="w-full text-sm" placeholder="Enter new rule name" required />
                                        </div>
                                        <button type="submit" class="w-full bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm">
                                            Use Template
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('commission-rules.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Rule Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="type" :value="__('Commission Type')" />
                                <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="value" :value="__('Value')" />
                                <x-text-input id="value" name="value" type="number" step="0.01" class="mt-1 block w-full" :value="old('value')" required />
                                <x-input-error :messages="$errors->get('value')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <x-input-label for="conditions" :value="__('Conditions (JSON)')" />
                                <textarea id="conditions" name="conditions" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono" rows="10">{{ old('conditions', '{}') }}</textarea>
                                <x-input-error :messages="$errors->get('conditions')" class="mt-2" />
                                <p class="mt-2 text-sm text-gray-500">
                                    Supported conditions:
                                    <ul class="list-disc list-inside">
                                        <li>min_order_value: Minimum order value</li>
                                        <li>max_order_value: Maximum order value</li>
                                        <li>minimum_quantity: Minimum order quantity</li>
                                        <li>maximum_quantity: Maximum order quantity</li>
                                        <li>product_categories: Array of category IDs</li>
                                    </ul>
                                </p>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="effective_from" :value="__('Effective From')" />
                                <x-text-input id="effective_from" name="effective_from" type="datetime-local" class="mt-1 block w-full" :value="old('effective_from')" />
                                <x-input-error :messages="$errors->get('effective_from')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="effective_until" :value="__('Effective Until')" />
                                <x-text-input id="effective_until" name="effective_until" type="datetime-local" class="mt-1 block w-full" :value="old('effective_until')" />
                                <x-input-error :messages="$errors->get('effective_until')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('active') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>{{ __('Create Rule') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valueInput = document.getElementById('value');

    function updateValueValidation() {
        if (typeSelect.value === 'percentage') {
            valueInput.setAttribute('max', '100');
        } else {
            valueInput.removeAttribute('max');
        }
    }

    typeSelect.addEventListener('change', updateValueValidation);
    updateValueValidation();
});
</script>
@endpush
@endsection
