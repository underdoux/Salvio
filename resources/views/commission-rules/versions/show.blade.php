@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold">Version {{ $version->version_number }}</h2>
                        <p class="text-gray-600">{{ $rule->name }}</p>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('commission-rules.versions.index', $rule) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to History
                        </a>
                        @if($version->version_number !== $rule->current_version)
                            <form action="{{ route('commission-rules.versions.restore', [$rule, $version]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to restore this version?')">
                                    Restore This Version
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Version Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Version Details</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <dt class="font-medium">Created By</dt>
                            <dd>{{ $version->user?->name ?? 'System' }}</dd>

                            <dt class="font-medium">Created At</dt>
                            <dd>{{ $version->created_at->format('Y-m-d H:i:s') }}</dd>

                            <dt class="font-medium">Change Reason</dt>
                            <dd>{{ $version->change_reason ?? 'No reason provided' }}</dd>

                            <dt class="font-medium">Status</dt>
                            <dd>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $version->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $version->isActive() ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>

                            @if($version->version_number === $rule->current_version)
                                <dt class="col-span-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        Current Version
                                    </span>
                                </dt>
                            @endif
                        </dl>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Schedule</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <dt class="font-medium">Effective From</dt>
                            <dd>{{ $version->effective_from?->format('Y-m-d H:i:s') ?? 'Not set' }}</dd>

                            <dt class="font-medium">Effective Until</dt>
                            <dd>{{ $version->effective_until?->format('Y-m-d H:i:s') ?? 'Not set' }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Rule Configuration -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Rule Configuration</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <dt class="font-medium">Name</dt>
                            <dd>{{ $version->name }}</dd>

                            <dt class="font-medium">Description</dt>
                            <dd>{{ $version->description ?? 'No description' }}</dd>

                            <dt class="font-medium">Type</dt>
                            <dd>{{ ucfirst($version->type) }}</dd>

                            <dt class="font-medium">Value</dt>
                            <dd>{{ $version->type === 'percentage' ? $version->value . '%' : '$' . number_format($version->value, 2) }}</dd>
                        </dl>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Conditions</h3>
                        @if($version->conditions)
                            <div class="bg-white p-4 rounded border">
                                <pre class="text-sm whitespace-pre-wrap">{{ json_encode($version->conditions, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @else
                            <p class="text-gray-600">No conditions set</p>
                        @endif
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-6 flex justify-between">
                    @if($previousVersion = $rule->versions()->where('version_number', '<', $version->version_number)->orderBy('version_number', 'desc')->first())
                        <a href="{{ route('commission-rules.versions.show', [$rule, $previousVersion]) }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Previous Version ({{ $previousVersion->version_number }})
                        </a>
                    @else
                        <span></span>
                    @endif

                    @if($nextVersion = $rule->versions()->where('version_number', '>', $version->version_number)->orderBy('version_number')->first())
                        <a href="{{ route('commission-rules.versions.show', [$rule, $nextVersion]) }}" class="text-indigo-600 hover:text-indigo-900">
                            Next Version ({{ $nextVersion->version_number }}) →
                        </a>
                    @else
                        <span></span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
