@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Conflict Details</h2>
                    <div class="flex gap-4">
                        <a href="{{ route('commission-rules.conflicts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Conflicts
                        </a>
                    </div>
                </div>

                <!-- Conflict Status -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                                {{ match($conflict->conflict_type) {
                                    'condition_overlap' => 'bg-yellow-100 text-yellow-800',
                                    'value_conflict' => 'bg-red-100 text-red-800',
                                    'date_overlap' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800',
                                } }}">
                                {{ str_replace('_', ' ', ucfirst($conflict->conflict_type)) }}
                            </span>
                            <span class="ml-4 px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $conflict->resolved ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $conflict->resolved ? 'Resolved' : 'Unresolved' }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-500">
                            Detected: {{ $conflict->created_at->format('Y-m-d H:i:s') }}
                            @if($conflict->resolved)
                                <br>
                                Resolved: {{ $conflict->resolved_at->format('Y-m-d H:i:s') }}
                                by {{ $conflict->resolvedBy->name }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Conflicting Rules -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Rule A: {{ $conflict->ruleA->name }}</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <dt class="font-medium">Type</dt>
                            <dd>{{ ucfirst($conflict->ruleA->type) }}</dd>

                            <dt class="font-medium">Value</dt>
                            <dd>{{ $conflict->ruleA->type === 'percentage' ? $conflict->ruleA->value . '%' : '$' . number_format($conflict->ruleA->value, 2) }}</dd>

                            <dt class="font-medium">Status</dt>
                            <dd>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $conflict->ruleA->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $conflict->ruleA->isActive() ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>

                            <dt class="font-medium">Priority</dt>
                            <dd>{{ $conflict->ruleA->priority }}</dd>

                            <dt class="font-medium">Effective Period</dt>
                            <dd>
                                {{ $conflict->ruleA->effective_from?->format('Y-m-d') ?? 'Any' }}
                                to
                                {{ $conflict->ruleA->effective_until?->format('Y-m-d') ?? 'Any' }}
                            </dd>

                            <dt class="font-medium col-span-2">Conditions</dt>
                            <dd class="col-span-2">
                                <pre class="text-sm bg-white p-2 rounded border">{{ json_encode($conflict->ruleA->conditions, JSON_PRETTY_PRINT) }}</pre>
                            </dd>
                        </dl>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Rule B: {{ $conflict->ruleB->name }}</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <dt class="font-medium">Type</dt>
                            <dd>{{ ucfirst($conflict->ruleB->type) }}</dd>

                            <dt class="font-medium">Value</dt>
                            <dd>{{ $conflict->ruleB->type === 'percentage' ? $conflict->ruleB->value . '%' : '$' . number_format($conflict->ruleB->value, 2) }}</dd>

                            <dt class="font-medium">Status</dt>
                            <dd>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $conflict->ruleB->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $conflict->ruleB->isActive() ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>

                            <dt class="font-medium">Priority</dt>
                            <dd>{{ $conflict->ruleB->priority }}</dd>

                            <dt class="font-medium">Effective Period</dt>
                            <dd>
                                {{ $conflict->ruleB->effective_from?->format('Y-m-d') ?? 'Any' }}
                                to
                                {{ $conflict->ruleB->effective_until?->format('Y-m-d') ?? 'Any' }}
                            </dd>

                            <dt class="font-medium col-span-2">Conditions</dt>
                            <dd class="col-span-2">
                                <pre class="text-sm bg-white p-2 rounded border">{{ json_encode($conflict->ruleB->conditions, JSON_PRETTY_PRINT) }}</pre>
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Conflict Details -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Conflict Details</h3>
                    <pre class="text-sm bg-white p-4 rounded border">{{ json_encode($conflict->conflict_details, JSON_PRETTY_PRINT) }}</pre>
                </div>

                @if(!$conflict->resolved)
                    <!-- Resolution Form -->
                    <div id="resolve" class="p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Resolve Conflict</h3>

                        <form action="{{ route('commission-rules.conflicts.resolve', $conflict) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <x-input-label for="resolution_type" :value="__('Resolution Type')" />
                                <select id="resolution_type" name="resolution_type" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Resolution Type</option>
                                    <option value="adjust_conditions">Adjust Conditions</option>
                                    <option value="adjust_values">Adjust Values</option>
                                    <option value="adjust_dates">Adjust Dates</option>
                                    <option value="set_priority">Set Priority</option>
                                    <option value="add_dependency">Add Dependency</option>
                                </select>
                                <x-input-error :messages="$errors->get('resolution_type')" class="mt-2" />
                            </div>

                            <!-- Dynamic Resolution Forms -->
                            <div id="resolution-forms" class="space-y-4">
                                <!-- Forms will be shown/hidden via JavaScript -->
                                <div id="adjust-conditions-form" class="hidden">
                                    <!-- Condition adjustment fields -->
                                </div>

                                <div id="adjust-values-form" class="hidden">
                                    <!-- Value adjustment fields -->
                                </div>

                                <div id="adjust-dates-form" class="hidden">
                                    <!-- Date adjustment fields -->
                                </div>

                                <div id="set-priority-form" class="hidden">
                                    <!-- Priority adjustment fields -->
                                </div>

                                <div id="add-dependency-form" class="hidden">
                                    <!-- Dependency fields -->
                                </div>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="notes" :value="__('Resolution Notes')" />
                                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <div class="flex justify-end">
                                <x-primary-button>
                                    {{ __('Resolve Conflict') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const resolutionType = document.getElementById('resolution_type');
    const resolutionForms = document.getElementById('resolution-forms').children;

    resolutionType.addEventListener('change', function() {
        // Hide all forms
        Array.from(resolutionForms).forEach(form => form.classList.add('hidden'));

        // Show selected form
        const selectedForm = document.getElementById(`${this.value}-form`);
        if (selectedForm) {
            selectedForm.classList.remove('hidden');
        }
    });
});
</script>
@endpush
@endsection
