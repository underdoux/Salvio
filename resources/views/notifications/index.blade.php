@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Notifications</h2>
            @if($notifications->isNotEmpty())
                <button
                    class="text-sm text-blue-600 hover:text-blue-800"
                    onclick="markAllAsRead()"
                >
                    Mark all as read
                </button>
            @endif
        </div>

        @if($notifications->isEmpty())
            <div class="text-center py-8">
                <p class="text-gray-600">No notifications found.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="notification-item border rounded-lg p-4 {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50' }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h3>
                                <p class="text-gray-600 mt-1">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <div class="mt-2 text-sm text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>
                            @if(!$notification->read_at)
                                <button
                                    class="text-sm text-blue-600 hover:text-blue-800 ml-4"
                                    onclick="markAsRead('{{ $notification->id }}')"
                                >
                                    Mark as read
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(() => window.location.reload())
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(() => window.location.reload())
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection
