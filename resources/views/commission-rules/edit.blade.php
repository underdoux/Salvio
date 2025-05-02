@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Edit Commission Rule</h2>
                    <div class="flex gap-4">
                        <a href="{{ route('commission-rules.versions.index', $commissionRule) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Version History
                        </a>
                        <form action="{{ route('commission-rules.versions.save-as-template', $commissionRule) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="name" value="{{ $commissionRule->name }} Template">
                            <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                Save as Template
                            </button>
                        </form>
                        <form action="{{ route('commission-rules.versions.duplicate', $commissionRule) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="name" value="{{ $commissionRule->name }} (Copy)">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Duplicate
                            </button>
                        </form>
                        <a href="{{ route('commission-rules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                    </div>
                </div>

                <form action="{{ route('commission-rules.update', $commissionRule) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Rule Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $commissionRule->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description', $commissionRule->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="type" :value="__('Commission Type')" />
                                <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="percentage" {{ old('type', $commissionRule->type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('type', $commissionRule->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="value" :value="__('Value')" />
                                <x-text-input id="value" name="value" type="number" step="0.01" class="mt-1 block w-full" :value="old('value', $commissionRule->value)" required />
                                <x-input-error :messages="$errors->get('value')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <x-input-label for="conditions" :value="__('Conditions (JSON)')" />
                                <textarea id="conditions" name="conditions" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono" rows="10">{{ old('conditions', json_encode($commissionRule->conditions, JSON_PRETTY_PRINT)) }}</textarea>
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
                                <x-text-input id="effective_from" name="effective_from" type="datetime-local" class="mt-1 block w-full" :value="old('effective_from', $commissionRule->effective_from?->format('Y-m-d\TH:i'))" />
                                <x-input-error :messages="$errors->get('effective_from')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="effective_until" :value="__('Effective Until')" />
                                <x-text-input id="effective_until" name="effective_until" type="datetime-local" class="mt-1 block w-full" :value="old('effective_until', $commissionRule->effective_until?->format('Y-m-d\TH:i'))" />
                                <x-input-error :messages="$errors->get('effective_until')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('active', $commissionRule->active) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">Active</span>
                                </label>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="change_reason" :value="__('Change Reason')" />
                                <textarea id="change_reason" name="change_reason" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2" placeholder="Describe why you're making these changes"></textarea>
                                <x-input-error :messages="$errors->get('change_reason')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>{{ __('Update Rule') }}</x-primary-button>
                    </div>
                </form>

                @if($currentVersion)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Current Version Information</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <dt class="font-medium">Version Number</dt>
                            <dd>{{ $currentVersion->version_number }}</dd>

                            <dt class="font-medium">Last Updated</dt>
                            <dd>{{ $currentVersion->created_at->format('Y-m-d H:i:s') }}</dd>

                            <dt class="font-medium">Updated By</dt>
                            <dd>{{ $currentVersion->user?->name ?? 'System' }}</dd>

                            <dt class="font-medium">Change Reason</dt>
                            <dd>{{ $currentVersion->change_reason ?? 'No reason provided' }}</dd>
                        </dl>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
