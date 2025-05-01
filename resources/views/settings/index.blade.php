@extends('layouts.app')

@section('title', 'Settings')

@section('header', 'Settings')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">System Settings</h2>

        <div class="space-y-6">
            <!-- General Settings Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-600">Configure general system settings and preferences.</p>
                    <div class="mt-4">
                        <a href="{{ route('settings.currency') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('Currency Settings') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Management Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">User Management</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-600">Manage user roles and permissions.</p>
                </div>
            </div>

            <!-- System Configuration Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">System Configuration</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-600">Configure system-wide settings and parameters.</p>
                </div>
            </div>

            <!-- Additional Settings Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Settings</h3>
                <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-800">Currency Settings</h4>
                        <p class="text-gray-600 mb-2">Configure currency display and formatting options.</p>
                        <a href="{{ route('settings.currency') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('Manage Currency') }}
                        </a>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-800">Future Settings</h4>
                        <p class="text-gray-600">Additional settings will be available here as the system grows.</p>
                        <ul class="mt-2 text-sm text-gray-600 list-disc list-inside">
                            <li>Timezone settings</li>
                            <li>Date format preferences</li>
                            <li>Notification preferences</li>
                            <li>System maintenance options</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
