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
        </div>
    </div>
</div>
@endsection
