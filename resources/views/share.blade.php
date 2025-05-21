<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuFlow - Share Files</title>
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
            <h1 class="text-2xl font-bold mb-6">Share Files</h1>
            
            @if(request()->has('file'))
                @php
                    $file = \App\Models\File::find(request()->get('file'));
                @endphp
                
                @if($file && $file->user_id == auth()->id())
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="flex items-center">
                            <i data-lucide="file" class="w-8 h-8 text-gray-500 mr-3"></i>
                            <div>
                                <h2 class="font-semibold">{{ $file->name }}</h2>
                                <p class="text-sm text-gray-500">{{ $file->human_readable_size }} • Uploaded {{ $file->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">Share with Users</h3>
                        <form action="{{ route('files.share', $file->id) }}" method="POST">
                            @csrf
                            <div class="flex">
                                <input type="email" name="email" placeholder="Enter email address" class="flex-1 border rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <select name="permission" class="border-t border-b border-r px-4 py-2">
                                    <option value="view">Can view</option>
                                    <option value="edit">Can edit</option>
                                </select>
                                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-r-lg hover:bg-purple-700">Share</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">Get Shareable Link</h3>
                        <div class="flex">
                            <input type="text" value="{{ route('files.shared-link', $file->id) }}" class="flex-1 border rounded-l-lg px-4 py-2 bg-gray-50" readonly id="share-link">
                            <button onclick="copyShareLink()" class="bg-gray-200 px-4 py-2 rounded-r-lg hover:bg-gray-300">Copy</button>
                        </div>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-purple-600" checked>
                                <span class="ml-2 text-sm text-gray-700">Anyone with the link can view</span>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-2">People with Access</h3>
                        <div class="border rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b">
                                <div class="grid grid-cols-3">
                                    <div>User</div>
                                    <div>Access</div>
                                    <div>Actions</div>
                                </div>
                            </div>
                            
                            <div class="divide-y">
                                <div class="px-4 py-3">
                                    <div class="grid grid-cols-3 items-center">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-purple-600 font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ auth()->user()->name }} (You)</div>
                                                <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Owner</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 text-sm">Cannot change</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @foreach($file->shares ?? [] as $share)
                                    <div class="px-4 py-3">
                                        <div class="grid grid-cols-3 items-center">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-gray-600 font-semibold">{{ substr($share->user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ $share->user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $share->user->email }}</div>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ ucfirst($share->permission) }}</span>
                                            </div>
                                            <div>
                                                <form action="{{ route('files.unshare', [$file->id, $share->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    The file you're trying to share doesn't exist or you don't have permission to share it.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Files Shared with You</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 text-left">Name</th>
                                    <th class="py-2 px-4 text-left">Shared By</th>
                                    <th class="py-2 px-4 text-left">Permission</th>
                                    <th class="py-2 px-4 text-left">Date Shared</th>
                                    <th class="py-2 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse(auth()->user()->sharedWithMe ?? [] as $share)
                                    <tr>
                                        <td class="py-2 px-4">
                                            <div class="flex items-center">
                                                <i data-lucide="file" class="w-5 h-5 text-gray-400 mr-2"></i>
                                                <span>{{ $share->file->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4">{{ $share->file->user->name }}</td>
                                        <td class="py-2 px-4">
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ ucfirst($share->permission) }}</span>
                                        </td>
                                        <td class="py-2 px-4">{{ $share->created_at->format('M d, Y') }}</td>
                                        <td class="py-2 px-4">
                                            <a href="{{ route('files.download', $share->file->id) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                <i data-lucide="download" class="w-4 h-4"></i>
                                            </a>
                                            @if($share->permission == 'edit')
                                                <a href="{{ route('files.edit', $share->file->id) }}" class="text-green-500 hover:text-green-700">
                                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">No files have been shared with you yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                    <h2 class="text-xl font-semibold mb-4">Files You've Shared</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 text-left">Name</th>
                                    <th class="py-2 px-4 text-left">Shared With</th>
                                    <th class="py-2 px-4 text-left">Permission</th>
                                    <th class="py-2 px-4 text-left">Date Shared</th>
                                    <th class="py-2 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse(auth()->user()->sharedByMe ?? [] as $share)
                                    <tr>
                                        <td class="py-2 px-4">
                                            <div class="flex items-center">
                                                <i data-lucide="file" class="w-5 h-5 text-gray-400 mr-2"></i>
                                                <span>{{ $share->file->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4">{{ $share->user->name }}</td>
                                        <td class="py-2 px-4">
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ ucfirst($share->permission) }}</span>
                                        </td>
                                        <td class="py-2 px-4">{{ $share->created_at->format('M d, Y') }}</td>
                                        <td class="py-2 px-4">
                                            <a href="{{ route('share') }}?file={{ $share->file->id }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('files.unshare', [$share->file->id, $share->id]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">You haven't shared any files yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
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
        
        // Copy share link
        function copyShareLink() {
            const shareLink = document.getElementById('share-link');
            shareLink.select();
            document.execCommand('copy');
            
            // Show a temporary "Copied!" message
            const button = shareLink.nextElementSibling;
            const originalText = button.innerText;
            button.innerText = 'Copied!';
            button.classList.add('bg-green-200');
            
            setTimeout(() => {
                button.innerText = originalText;
                button.classList.remove('bg-green-200');
            }, 2000);
        }
    </script>
</body>
</html>