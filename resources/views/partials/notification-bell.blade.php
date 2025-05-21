<div class="relative">
    <button class="navbar-icon" onclick="toggleNotifications()">
        <i data-lucide="bell" class="w-5 h-5"></i>
        @if(count($notifications ?? []) > 0)
            <div class="notification-dot"></div>
        @endif
    </button>
    <!-- Notification Dropdown -->
    <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 hidden z-50">
        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
            <p class="text-sm font-medium text-gray-900">Notifications</p>
            @if(count($notifications ?? []) > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-purple-600 hover:text-purple-800">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>
        <div class="max-h-64 overflow-y-auto">
            @forelse($notifications ?? [] as $notification)
                <div class="px-4 py-2 border-b border-gray-100 hover:bg-gray-50 {{ !$notification->is_read ? 'bg-purple-50' : '' }}">
                    <div class="flex justify-between">
                        <p class="text-sm text-gray-700">{{ $notification->message }}</p>
                        @if(!$notification->is_read)
                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-purple-600 hover:text-purple-800">
                                    Mark read
                                </button>
                            </form>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                    @if(isset($notification->data['action_url']) && isset($notification->data['action_text']))
                        <a href="{{ $notification->data['action_url'] }}" class="block mt-1 text-xs text-purple-600 hover:text-purple-800">
                            {{ $notification->data['action_text'] }}
                        </a>
                    @endif
                </div>
            @empty
                <div class="px-4 py-6 text-center">
                    <p class="text-sm text-gray-500">No notifications</p>
                </div>
            @endforelse
        </div>
        @if(count($notifications ?? []) > 0)
            <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-center text-purple-600 hover:bg-gray-100">View All</a>
        @endif
    </div>
</div>