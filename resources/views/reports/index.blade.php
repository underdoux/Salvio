@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Reports Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Revenue Report -->
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Revenue Report</h2>
            <p>View revenue over selected periods.</p>
            <!-- Add charts or tables here -->
        </div>

        <!-- Commission Report -->
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Commission Report</h2>
            <p>View commissions per user and product.</p>
            <!-- Add charts or tables here -->
        </div>

        <!-- Profit Distribution Report -->
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Profit Distribution Report</h2>
            <p>View profit distributions to investors.</p>
            <!-- Add charts or tables here -->
        </div>
    </div>
</div>
@endsection
