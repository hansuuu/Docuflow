<div class="navbar">
    <div class="navbar-container">
        <!-- Logo -->
        <div class="navbar-logo">
            <div class="navbar-logo-icon">
                <i data-lucide="file" class="w-4 h-4"></i>
            </div>
            <span>DocuFlow</span>
        </div>
        
        <!-- Navigation -->
        <div class="navbar-nav">
            <a href="{{ route('dashboard') }}" class="navbar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="home" class="w-5 h-5"></i>
            </a>
            <a href="{{ route('starred') }}" class="navbar-nav-item {{ request()->routeIs('starred') ? 'active' : '' }}">
                <i data-lucide="star" class="w-5 h-5"></i>
            </a>
            <a href="{{ route('folders') }}" class="navbar-nav-item {{ request()->routeIs('folders') ? 'active' : '' }}">
                <i data-lucide="folder" class="w-5 h-5"></i>
            </a>
            <a href="{{ route('share') }}" class="navbar-nav-item {{ request()->routeIs('share') ? 'active' : '' }}">
                <i data-lucide="send" class="w-5 h-5"></i>
            </a>
            <a href="{{ route('trash') }}" class="navbar-nav-item {{ request()->routeIs('trash') ? 'active' : '' }}">
                <i data-lucide="trash" class="w-5 h-5"></i>
            </a>
            <a href="{{ route('file.settings') }}" class="navbar-nav-item {{ request()->routeIs('file.settings') ? 'active' : '' }}">
                <i data-lucide="settings" class="w-5 h-5"></i>
            </a>
        </div>
        
        <!-- Actions -->
        <div class="navbar-actions">
            <a href="{{ route('settings') }}" class="navbar-nav-item">
                <i data-lucide="settings" class="w-5 h-5"></i>
            </a>
            
            <!-- Notifications -->
            <div class="dropdown-container">
                <button class="navbar-nav-item" onclick="toggleNotifications()">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    @if(count($notifications ?? []) > 0)
                        <div class="notification-badge"></div>
                    @endif
                </button>
                
                <div id="notification-dropdown" class="notification-dropdown">
                    <div class="notification-header">
                        <div class="notification-title">Notifications</div>
                        @if(count($notifications ?? []) > 0)
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                @csrf
                                <button type="submit" class="notification-action">
                                    Mark all as read
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="notification-list">
                        @forelse($notifications ?? [] as $notification)
                            <div class="notification-item {{ $notification->is_read ? '' : 'unread' }}">
                                <div class="notification-content">{{ $notification->message }}</div>
                                <div class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                                
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="notification-action">
                                            Mark as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="notification-item">
                                <div class="notification-content text-center">No notifications</div>
                            </div>
                        @endforelse
                    </div>
                    
                    @if(count($notifications ?? []) > 0)
                        <div class="notification-footer">
                            <a href="{{ route('notifications.index') }}">View all notifications</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- User -->
            <div class="dropdown-container">
                <button class="navbar-nav-item" onclick="toggleUserDropdown()">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </button>
                
                <div id="user-dropdown" class="user-dropdown">
                    <div class="user-dropdown-header">
                        <div class="user-dropdown-name">{{ auth()->user()->name ?? 'Guest' }}</div>
                        <div class="user-dropdown-email">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                    
                    <a href="{{ route('user.settings') }}" class="user-dropdown-item">Profile</a>
                    <a href="{{ route('help') }}" class="user-dropdown-item">Help & Support</a>
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="user-dropdown-item danger">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleNotifications() {
        const dropdown = document.getElementById('notification-dropdown');
        dropdown.classList.toggle('show');
        
        // Close user dropdown if open
        const userDropdown = document.getElementById('user-dropdown');
        if (userDropdown.classList.contains('show')) {
            userDropdown.classList.remove('show');
        }
    }
    
    function toggleUserDropdown() {
        const dropdown = document.getElementById('user-dropdown');
        dropdown.classList.toggle('show');
        
        // Close notification dropdown if open
        const notificationDropdown = document.getElementById('notification-dropdown');
        if (notificationDropdown.classList.contains('show')) {
            notificationDropdown.classList.remove('show');
        }
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const notificationDropdown = document.getElementById('notification-dropdown');
        const userDropdown = document.getElementById('user-dropdown');
        
        const notificationButton = event.target.closest('.dropdown-container button');
        
        if (!notificationButton && notificationDropdown.classList.contains('show')) {
            notificationDropdown.classList.remove('show');
        }
        
        if (!notificationButton && userDropdown.classList.contains('show')) {
            userDropdown.classList.remove('show');
        }
    });
</script>