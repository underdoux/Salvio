@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Dependency Analysis for {{ $rule->name }}</h2>
                    <div class="flex gap-4">
                        <a href="{{ route('commission-rules.dependencies.index', $rule) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Dependencies
                        </a>
                    </div>
                </div>

                <!-- Analysis Summary -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Analysis Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-4 rounded-lg {{ $analysis['dependencies_satisfied'] ? 'bg-green-100' : 'bg-red-100' }}">
                            <div class="font-medium">Dependencies Status</div>
                            <div class="text-sm mt-1">
                                @if($analysis['dependencies_satisfied'])
                                    All dependencies are satisfied
                                @else
                                    Some dependencies are not satisfied
                                @endif
                            </div>
                        </div>

                        <div class="p-4 rounded-lg {{ $analysis['circular_dependencies'] ? 'bg-red-100' : 'bg-green-100' }}">
                            <div class="font-medium">Circular Dependencies</div>
                            <div class="text-sm mt-1">
                                @if($analysis['circular_dependencies'])
                                    Circular dependencies detected
                                @else
                                    No circular dependencies
                                @endif
                            </div>
                        </div>

                        <div class="p-4 rounded-lg {{ $analysis['date_overlaps']->isEmpty() ? 'bg-green-100' : 'bg-yellow-100' }}">
                            <div class="font-medium">Date Overlaps</div>
                            <div class="text-sm mt-1">
                                {{ $analysis['date_overlaps']->count() }} rules with overlapping dates
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dependency Chain -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Dependency Chain</h3>
                    <div class="bg-white rounded-lg border overflow-hidden">
                        <div class="overflow-x-auto">
                            <div class="inline-flex p-4 space-x-2">
                                @foreach($analysis['dependency_chain'] as $chainRule)
                                    <div class="flex items-center">
                                        @if(!$loop->first)
                                            <svg class="h-6 w-6 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        @endif
                                        <div class="px-4 py-2 rounded {{ $chainRule->isActive() ? 'bg-green-100' : 'bg-red-100' }}">
                                            <div class="font-medium">{{ $chainRule->name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $chainRule->isActive() ? 'Active' : 'Inactive' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Override Analysis -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Rules that override this rule -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Overriding Rules</h3>
                        <div class="bg-white rounded-lg border">
                            @if($analysis['overriding_rules']->isNotEmpty())
                                <ul class="divide-y">
                                    @foreach($analysis['overriding_rules'] as $overridingRule)
                                        <li class="p-4">
                                            <div class="font-medium">{{ $overridingRule->name }}</div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                Status: {{ $overridingRule->isActive() ? 'Active' : 'Inactive' }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="p-4 text-gray-500">No rules override this rule</div>
                            @endif
                        </div>
                    </div>

                    <!-- Rules overridden by this rule -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Overridden Rules</h3>
                        <div class="bg-white rounded-lg border">
                            @if($analysis['overridden_rules']->isNotEmpty())
                                <ul class="divide-y">
                                    @foreach($analysis['overridden_rules'] as $overriddenRule)
                                        <li class="p-4">
                                            <div class="font-medium">{{ $overriddenRule->name }}</div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                Status: {{ $overriddenRule->isActive() ? 'Active' : 'Inactive' }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="p-4 text-gray-500">This rule doesn't override any rules</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Date Overlap Analysis -->
                @if($analysis['date_overlaps']->isNotEmpty())
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Date Overlaps</h3>
                        <div class="bg-white rounded-lg border">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rule</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective From</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Until</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($analysis['date_overlaps'] as $overlappingRule)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="font-medium">{{ $overlappingRule->name }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $overlappingRule->effective_from?->format('Y-m-d') ?? 'Any' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $overlappingRule->effective_until?->format('Y-m-d') ?? 'Any' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $overlappingRule->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $overlappingRule->isActive() ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
