<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Commission Management - Salvio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-3xl font-bold mb-6">Commission Management</h1>
    <a href="{{ route('commissions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">Add New Commission</a>
    <table class="min-w-full bg-white shadow-md rounded">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">User</th>
                <th class="py-2 px-4 border-b">Product</th>
                <th class="py-2 px-4 border-b">Category</th>
                <th class="py-2 px-4 border-b">Commission Rate (%)</th>
                <th class="py-2 px-4 border-b">Min Cap</th>
                <th class="py-2 px-4 border-b">Max Cap</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($commissions as $commission)
            <tr>
                <td class="py-2 px-4 border-b">{{ $commission->user->name ?? 'N/A' }}</td>
                <td class="py-2 px-4 border-b">{{ $commission->product->name ?? 'N/A' }}</td>
                <td class="py-2 px-4 border-b">{{ $commission->category->name ?? 'N/A' }}</td>
                <td class="py-2 px-4 border-b">{{ $commission->commission_rate }}</td>
                <td class="py-2 px-4 border-b">{{ $commission->min_cap }}</td>
                <td class="py-2 px-4 border-b">{{ $commission->max_cap }}</td>
                <td class="py-2 px-4 border-b">
                    <a href="{{ route('commissions.edit', $commission->id) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                    <form action="{{ route('commissions.destroy', $commission->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
