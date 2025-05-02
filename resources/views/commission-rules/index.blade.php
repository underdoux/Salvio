@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Commission Rules</h2>
                    <div class="flex gap-4">
                        <a href="{{ route('commission-rules.export') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Export Rules
                        </a>
                        <label for="import-file" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded cursor-pointer">
                            Import Rules
                        </label>
                        <a href="{{ route('commission-rules.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Rule
                        </a>
                    </div>
                </div>

                <!-- Import Form (Hidden) -->
                <form id="import-form" action="{{ route('commission-rules.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" id="import-file" name="file" accept=".csv" class="hidden" onchange="document.getElementById('import-form').submit()">
                </form>

                <!-- Search and Filters -->
                <div class="mb-6">
                    <form action="{{ route('commission-rules.index') }}" method="GET" class="space-y-4">
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Search by name or description...">
                            </div>
                            <div class="w-48">
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Types</option>
                                    <option value="percentage" {{ ($filters['type'] ?? '') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ ($filters['type'] ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                            </div>
                            <div class="w-48">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Status</option>
                                    <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="w-48 flex items-end">
                                <button type="submit" class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bulk Actions -->
                <form action="{{ route('commission-rules.bulk-action') }}" method="POST" id="bulk-action-form">
                    @csrf
                    <div class="mb-4 flex gap-4">
                        <select name="action" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Bulk Actions</option>
                            <option value="activate">Activate Selected</option>
                            <option value="deactivate">Deactivate Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                                onclick="return confirm('Are you sure you want to perform this action on the selected rules?')">
                            Apply
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                               onclick="document.querySelectorAll('input[name^=selected]').forEach(cb => cb.checked = this.checked)">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('commission-rules.index', array_merge(
                                            request()->except(['sort', 'direction']),
                                            ['sort' => 'name', 'direction' => ($filters['sort'] === 'name' && $filters['direction'] === 'asc') ? 'desc' : 'asc']
                                        )) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Name</span>
                                            @if($filters['sort'] === 'name')
                                                <span class="text-gray-400">
                                                    {!! $filters['direction'] === 'asc' ? '↑' : '↓' !!}
                                                </span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('commission-rules.index', array_merge(
                                            request()->except(['sort', 'direction']),
                                            ['sort' => 'type', 'direction' => ($filters['sort'] === 'type' && $filters['direction'] === 'asc') ? 'desc' : 'asc']
                                        )) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Type</span>
                                            @if($filters['sort'] === 'type')
                                                <span class="text-gray-400">
                                                    {!! $filters['direction'] === 'asc' ? '↑' : '↓' !!}
                                                </span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('commission-rules.index', array_merge(
                                            request()->except(['sort', 'direction']),
                                            ['sort' => 'value', 'direction' => ($filters['sort'] === 'value' && $filters['direction'] === 'asc') ? 'desc' : 'asc']
                                        )) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Value</span>
                                            @if($filters['sort'] === 'value')
                                                <span class="text-gray-400">
                                                    {!! $filters['direction'] === 'asc' ? '↑' : '↓' !!}
                                                </span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('commission-rules.index', array_merge(
                                            request()->except(['sort', 'direction']),
                                            ['sort' => 'active', 'direction' => ($filters['sort'] === 'active' && $filters['direction'] === 'asc') ? 'desc' : 'asc']
                                        )) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                            <span>Status</span>
                                            @if($filters['sort'] === 'active')
                                                <span class="text-gray-400">
                                                    {!! $filters['direction'] === 'asc' ? '↑' : '↓' !!}
                                                </span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rules as $rule)
                                <tr>
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="selected[]" value="{{ $rule->id }}"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $rule->name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 line-clamp-2">
                                            {{ $rule->description ?? 'No description provided' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($rule->type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $rule->type === 'percentage' ? $rule->value . '%' : '$' . number_format($rule->value, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rule->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $rule->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('commission-rules.preview', $rule) }}" class="text-blue-600 hover:text-blue-900 mr-3">Preview</a>
                                        <a href="{{ route('commission-rules.edit', $rule) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="{{ route('commission-rules.destroy', $rule) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this rule?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                <div class="mt-4">
                    {{ $rules->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
