<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuFlow - Starred Files</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        .navbar {
            background-color: #000;
            color: white;
            height: 56px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 18px;
        }
        
        .navbar-logo-icon {
            background-color: white;
            color: black;
            width: 28px;
            height: 28px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .navbar-nav-item {
            color: white;
            opacity: 0.8;
            transition: opacity 0.2s;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        
        .navbar-nav-item:hover {
            opacity: 1;
            background-color: #333;
        }
        
        .navbar-nav-item.active {
            opacity: 1;
            background-color: #7e22ce;
        }
        
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-top: 8px;
            color: #333;
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }
        
        .notification-dropdown.show {
            display: block;
        }
        
        .notification-header {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-title {
            font-weight: 600;
            font-size: 14px;
        }
        
        .notification-action {
            color: #6366f1;
            font-size: 12px;
            cursor: pointer;
        }
        
        .notification-list {
            max-height: 320px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f9fafb;
        }
        
        .notification-item.unread {
            background-color: #f0f7ff;
        }
        
        .notification-content {
            font-size: 13px;
            margin-bottom: 4px;
        }
        
        .notification-time {
            font-size: 11px;
            color: #6b7280;
        }
        
        .notification-footer {
            padding: 8px 16px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        
        .notification-footer a {
            color: #6366f1;
            font-size: 13px;
            font-weight: 500;
        }
        
        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
        }
        
        .dropdown-container {
            position: relative;
        }
        
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 200px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-top: 8px;
            color: #333;
            z-index: 1000;
            display: none;
        }
        
        .user-dropdown.show {
            display: block;
        }
        
        .user-dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
        }
        
        .user-dropdown-name {
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-dropdown-email {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-dropdown-item {
            padding: 8px 16px;
            font-size: 13px;
            transition: background-color 0.2s;
            display: block;
            width: 100%;
            text-align: left;
        }
        
        .user-dropdown-item:hover {
            background-color: #f9fafb;
        }
        
        .user-dropdown-item.danger {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
                <a href="{{ route('file.settings') }}" class="navbar-nav-item">
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

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto mt-6 px-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Starred Files</h1>
                <div class="flex space-x-2">
                    <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-md flex items-center">
                        <i data-lucide="list" class="w-4 h-4 mr-1"></i>
                        List
                    </button>
                    <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-md flex items-center">
                        <i data-lucide="grid" class="w-4 h-4 mr-1"></i>
                        Grid
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 text-left">Name</th>
                            <th class="py-2 px-4 text-left">Location</th>
                            <th class="py-2 px-4 text-left">Size</th>
                            <th class="py-2 px-4 text-left">Last Modified</th>
                            <th class="py-2 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse(auth()->user()->files()->where('is_starred', true)->get() as $file)
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <i data-lucide="file" class="w-5 h-5 text-gray-400 mr-2"></i>
                                        <span>{{ $file->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($file->folder)
                                        {{ $file->folder->name }}
                                    @else
                                        Root Directory
                                    @endif
                                </td>
                                <td class="py-3 px-4">{{ $file->human_readable_size }}</td>
                                <td class="py-3 px-4">{{ $file->updated_at->format('M d, Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('files.download', $file->id) }}" class="text-blue-500 hover:text-blue-700">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('share') }}?file={{ $file->id }}" class="text-green-500 hover:text-green-700">
                                            <i data-lucide="share" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('files.toggle-star', $file->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-yellow-500 hover:text-yellow-700">
                                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('files.delete', $file->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="star" class="w-12 h-12 text-gray-300 mb-2"></i>
                                        <p>No starred files yet</p>
                                        <p class="text-sm mt-1">Star your important files to access them quickly</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Toggle notifications dropdown
        function toggleNotifications() {
            const dropdown = document.getElementById('notification-dropdown');
            dropdown.classList.toggle('show');
            
            // Close user dropdown if open
            const userDropdown = document.getElementById('user-dropdown');
            if (userDropdown.classList.contains('show')) {
                userDropdown.classList.remove('show');
            }
        }
        
        // Toggle user dropdown
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
            
            if (!notificationButton && notificationDropdown && notificationDropdown.classList.contains('show')) {
                notificationDropdown.classList.remove('show');
            }
            
            if (!notificationButton && userDropdown && userDropdown.classList.contains('show')) {
                userDropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>