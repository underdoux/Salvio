@extends('layouts.app')

@section('title', 'Profit Distributions')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Profit Distributions</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profit-distributions.distribute') }}">
        @csrf
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Distribute Monthly Profit
        </button>
    </form>

    <!-- Add table or charts for profit distribution reports here -->
</div>
@endsection
