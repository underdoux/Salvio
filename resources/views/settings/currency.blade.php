@extends('layouts.app')

@section('title', 'Currency Settings')

@section('header', 'Currency Settings')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Currency Settings</h2>

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
            @csrf

            <!-- Currency Code -->
            <div>
                <label for="currency_code" class="block text-sm font-medium text-gray-700">Currency Code</label>
                <select name="settings[currency_code]" id="currency_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @php
                        $currencies = json_decode($settings->firstWhere('key', 'currency_symbols')->value ?? '{}', true);
                        $currentCode = $settings->firstWhere('key', 'currency_code')->value ?? 'IDR';
                    @endphp
                    @foreach($currencies as $code => $symbol)
                        <option value="{{ $code }}" {{ $currentCode === $code ? 'selected' : '' }}>
                            {{ $code }} ({{ $symbol }})
                        </option>
                    @endforeach
                </select>
                @error('settings.currency_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Currency Position -->
            <div>
                <label for="currency_position" class="block text-sm font-medium text-gray-700">Currency Position</label>
                <select name="settings[currency_position]" id="currency_position" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @php
                        $currentPosition = $settings->firstWhere('key', 'currency_position')->value ?? 'before';
                    @endphp
                    <option value="before" {{ $currentPosition === 'before' ? 'selected' : '' }}>Before amount (e.g., $ 100)</option>
                    <option value="after" {{ $currentPosition === 'after' ? 'selected' : '' }}>After amount (e.g., 100 $)</option>
                </select>
                @error('settings.currency_position')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Preview</h3>
                <p class="text-gray-600">
                    Example amount: {{ App\Models\Setting::formatMoney(1000000) }}
                </p>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
