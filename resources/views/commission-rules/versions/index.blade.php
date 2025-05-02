@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold">Version History</h2>
                        <p class="text-gray-600">{{ $rule->name }}</p>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('commission-rules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to List
                        </a>
                        <form action="{{ route('commission-rules.versions.duplicate', $rule) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Duplicate Rule
                            </button>
                        </form>
                        @if(!$rule->isTemplate())
                            <form action="{{ route('commission-rules.versions.save-as-template', $rule) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    Save as Template
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Version Comparison Form -->
                <form action="{{ route('commission-rules.versions.compare', $rule) }}" method="GET" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Compare Versions</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="version_a" class="block text-sm font-medium text-gray-700">Version A</label>
                            <select name="version_a" id="version_a" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($versions as $version)
                                    <option value="{{ $version->id }}">Version {{ $version->version_number }} ({{ $version->created_at->format('Y-m-d H:i') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="version_b" class="block text-sm font-medium text-gray-700">Version B</label>
                            <select name="version_b" id="version_b" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($versions as $version)
                                    <option value="{{ $version->id }}" {{ $loop->first ? 'selected' : '' }}>Version {{ $version->version_number }} ({{ $version->created_at->format('Y-m-d H:i') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Compare Versions
                        </button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Changed By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($versions as $version)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Version {{ $version->version_number }}</div>
                                    @if($version->version_number === $rule->current_version)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Current
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $version->user?->name ?? 'System' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $version->change_reason ?? 'No reason provided' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $version->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $version->isActive() ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $version->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('commission-rules.versions.show', [$rule, $version]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    @if($version->version_number !== $rule->current_version)
                                        <form action="{{ route('commission-rules.versions.restore', [$rule, $version]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Are you sure you want to restore this version?')">
                                                Restore
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $versions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
