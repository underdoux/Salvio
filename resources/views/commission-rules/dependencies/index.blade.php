@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Dependencies for {{ $rule->name }}</h2>
                    <div class="flex gap-4">
                        <a href="{{ route('commission-rules.dependencies.graph', $rule) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            View Graph
                        </a>
                        <a href="{{ route('commission-rules.dependencies.analyze', $rule) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            Analyze Dependencies
                        </a>
                        <form action="{{ route('commission-rules.dependencies.validate', $rule) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Validate Status
                            </button>
                        </form>
                        <a href="{{ route('commission-rules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Rules
                        </a>
                    </div>
                </div>

                <!-- Rule Status -->
                <div class="mb-6 p-4 {{ $rule->isActive() ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-semibold">Status:</span>
                            <span class="ml-2 px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $rule->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $rule->isActive() ? 'Active' : 'Inactive' }}
                            </span>
                            @if($hasCircularDependency)
                                <span class="ml-2 px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    Circular Dependency Detected
                                </span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">
                            Effective: {{ $rule->effective_from?->format('Y-m-d') ?? 'Any' }} to {{ $rule->effective_until?->format('Y-m-d') ?? 'Any' }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dependencies -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Dependencies</h3>
                        <div class="space-y-4">
                            @foreach(['requires', 'conflicts', 'overrides'] as $type)
                                <div class="border rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-2 font-medium">
                                        {{ ucfirst($type) }}
                                    </div>
                                    <div class="p-4">
                                        @if(isset($dependencies[$type]) && $dependencies[$type]->isNotEmpty())
                                            <ul class="space-y-2">
                                                @foreach($dependencies[$type] as $dependency)
                                                    <li class="flex justify-between items-center">
                                                        <div>
                                                            <span class="font-medium">{{ $dependency->dependsOnRule->name }}</span>
                                                            @if($dependency->reason)
                                                                <p class="text-sm text-gray-500">{{ $dependency->reason }}</p>
                                                            @endif
                                                        </div>
                                                        <form action="{{ route('commission-rules.dependencies.destroy', [$rule, $dependency]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to remove this dependency?')">
                                                                Remove
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-500">No {{ $type }} dependencies</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dependents -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Dependent Rules</h3>
                        <div class="space-y-4">
                            @foreach(['requires', 'conflicts', 'overrides'] as $type)
                                <div class="border rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-2 font-medium">
                                        {{ ucfirst($type) }} This Rule
                                    </div>
                                    <div class="p-4">
                                        @if(isset($dependents[$type]) && $dependents[$type]->isNotEmpty())
                                            <ul class="space-y-2">
                                                @foreach($dependents[$type] as $dependent)
                                                    <li>
                                                        <span class="font-medium">{{ $dependent->commissionRule->name }}</span>
                                                        @if($dependent->reason)
                                                            <p class="text-sm text-gray-500">{{ $dependent->reason }}</p>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-500">No rules {{ $type }} this rule</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Add Dependency Form -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Add Dependency</h3>
                    <form action="{{ route('commission-rules.dependencies.store', $rule) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="depends_on_rule_id" :value="__('Depends On')" />
                                <select id="depends_on_rule_id" name="depends_on_rule_id" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Rule</option>
                                    @foreach($availableRules as $availableRule)
                                        <option value="{{ $availableRule->id }}">{{ $availableRule->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('depends_on_rule_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="dependency_type" :value="__('Dependency Type')" />
                                <select id="dependency_type" name="dependency_type" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Type</option>
                                    <option value="requires">Requires</option>
                                    <option value="conflicts">Conflicts</option>
                                    <option value="overrides">Overrides</option>
                                </select>
                                <x-input-error :messages="$errors->get('dependency_type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="reason" :value="__('Reason')" />
                                <x-text-input id="reason" name="reason" type="text" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <x-primary-button>
                                {{ __('Add Dependency') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
