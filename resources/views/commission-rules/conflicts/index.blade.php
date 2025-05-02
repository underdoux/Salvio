@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Commission Rule Conflicts</h2>
                    <div class="flex gap-4">
                        <form action="{{ route('commission-rules.conflicts.detect-all') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Detect All Conflicts
                            </button>
                        </form>
                        <a href="{{ route('commission-rules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Rules
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="mb-6">
                    <form action="{{ route('commission-rules.conflicts.index') }}" method="GET" class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-48">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Status</option>
                                    <option value="unresolved" {{ ($filters['status'] ?? '') == 'unresolved' ? 'selected' : '' }}>Unresolved</option>
                                    <option value="resolved" {{ ($filters['status'] ?? '') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                </select>
                            </div>
                            <div class="w-48">
                                <label for="type" class="block text-sm font-medium text-gray-700">Conflict Type</label>
                                <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Types</option>
                                    <option value="condition_overlap" {{ ($filters['type'] ?? '') == 'condition_overlap' ? 'selected' : '' }}>Condition Overlap</option>
                                    <option value="value_conflict" {{ ($filters['type'] ?? '') == 'value_conflict' ? 'selected' : '' }}>Value Conflict</option>
                                    <option value="date_overlap" {{ ($filters['type'] ?? '') == 'date_overlap' ? 'selected' : '' }}>Date Overlap</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Conflicts Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rules</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detected</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolved</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($conflicts as $conflict)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $conflict->ruleA->name }}</div>
                                        <div class="text-gray-500">vs</div>
                                        <div class="font-medium text-gray-900">{{ $conflict->ruleB->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ match($conflict->conflict_type) {
                                            'condition_overlap' => 'bg-yellow-100 text-yellow-800',
                                            'value_conflict' => 'bg-red-100 text-red-800',
                                            'date_overlap' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        } }}">
                                        {{ str_replace('_', ' ', ucfirst($conflict->conflict_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $conflict->resolved ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $conflict->resolved ? 'Resolved' : 'Unresolved' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $conflict->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($conflict->resolved)
                                        {{ $conflict->resolved_at->format('Y-m-d H:i:s') }}<br>
                                        <span class="text-xs">by {{ $conflict->resolvedBy->name }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('commission-rules.conflicts.show', $conflict) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View Details</a>
                                    @if(!$conflict->resolved)
                                        <a href="{{ route('commission-rules.conflicts.show', $conflict) }}#resolve" class="text-green-600 hover:text-green-900">Resolve</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $conflicts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
