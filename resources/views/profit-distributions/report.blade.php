@extends('layouts.app')

@section('title', 'Profit Distribution Report')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Profit Distribution Report</h1>

    <table class="min-w-full bg-white shadow-md rounded">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Investor Name</th>
                <th class="py-2 px-4 border-b">Capital Amount</th>
                <th class="py-2 px-4 border-b">Ownership Percentage</th>
                <th class="py-2 px-4 border-b">Distributed Amount</th>
                <th class="py-2 px-4 border-b">Distribution Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($distributions as $distribution)
            <tr>
                <td class="py-2 px-4 border-b">{{ $distribution->capitalInvestor->name ?? 'N/A' }}</td>
                <td class="py-2 px-4 border-b">${{ number_format($distribution->capitalInvestor->capital_amount, 2) ?? '0.00' }}</td>
                <td class="py-2 px-4 border-b">{{ $distribution->capitalInvestor->ownership_percentage ?? '0' }}%</td>
                <td class="py-2 px-4 border-b">${{ number_format($distribution->amount, 2) }}</td>
                <td class="py-2 px-4 border-b">{{ $distribution->distribution_date->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
