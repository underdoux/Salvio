@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold">Compare Versions</h2>
                        <p class="text-gray-600">{{ $rule->name }}</p>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('commission-rules.versions.index', $rule) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to History
                        </a>
                    </div>
                </div>

                <!-- Version Information -->
                <div class="grid grid-cols-2 gap-8 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Version {{ $versionA->version_number }}</h3>
                        <div class="text-sm text-gray-600">
                            <p>Created: {{ $versionA->created_at->format('Y-m-d H:i:s') }}</p>
                            <p>By: {{ $versionA->user?->name ?? 'System' }}</p>
                            <p>Reason: {{ $versionA->change_reason ?? 'No reason provided' }}</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Version {{ $versionB->version_number }}</h3>
                        <div class="text-sm text-gray-600">
                            <p>Created: {{ $versionB->created_at->format('Y-m-d H:i:s') }}</p>
                            <p>By: {{ $versionB->user?->name ?? 'System' }}</p>
                            <p>Reason: {{ $versionB->change_reason ?? 'No reason provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Differences -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold">Changes</h3>
                    @if(empty($differences))
                        <p class="text-gray-600">No differences found between these versions.</p>
                    @else
                        @foreach($differences as $field => $diff)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold mb-2 capitalize">{{ str_replace('_', ' ', $field) }}</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm text-gray-600 mb-1">Version {{ $versionA->version_number }}</div>
                                        <div class="bg-red-50 p-3 rounded">
                                            @if($field === 'conditions')
                                                <pre class="text-sm text-red-700 whitespace-pre-wrap">{{ $diff['old'] }}</pre>
                                            @elseif($field === 'active')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $diff['old'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $diff['old'] ? 'Active' : 'Inactive' }}
                                                </span>
                                            @else
                                                <div class="text-red-700">{{ $diff['old'] ?? 'Not set' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600 mb-1">Version {{ $versionB->version_number }}</div>
                                        <div class="bg-green-50 p-3 rounded">
                                            @if($field === 'conditions')
                                                <pre class="text-sm text-green-700 whitespace-pre-wrap">{{ $diff['new'] }}</pre>
                                            @elseif($field === 'active')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $diff['new'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $diff['new'] ? 'Active' : 'Inactive' }}
                                                </span>
                                            @else
                                                <div class="text-green-700">{{ $diff['new'] ?? 'Not set' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end space-x-4">
                    @if($versionA->version_number !== $rule->current_version)
                        <form action="{{ route('commission-rules.versions.restore', [$rule, $versionA]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to restore version {{ $versionA->version_number }}?')">
                                Restore Version {{ $versionA->version_number }}
                            </button>
                        </form>
                    @endif
                    @if($versionB->version_number !== $rule->current_version)
                        <form action="{{ route('commission-rules.versions.restore', [$rule, $versionB]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to restore version {{ $versionB->version_number }}?')">
                                Restore Version {{ $versionB->version_number }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
