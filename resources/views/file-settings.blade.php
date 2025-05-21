<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow - File Settings</title>
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
<body class="bg-gray-100">
    @php
        // Fallback if $userPreferences is not defined
        $userPreferences = $userPreferences ?? (object)[
            'default_view' => 'grid',
            'sort_by' => 'name',
            'show_hidden_files' => false,
            'auto_organize' => false,
            'trash_retention' => 30
        ];

        // Fallback if $sharingPreferences is not defined
        $sharingPreferences = $sharingPreferences ?? (object)[
            'default_permission' => 'view',
            'notify_on_access' => true,
            'password_protect_by_default' => false,
            'default_expiration' => 7
        ];

        // Fallback if $userPlan is not defined
        $userPlan = $userPlan ?? (object)[
            'name' => 'Free Plan',
            'storage_gb' => 5,
            'features' => [
                'Basic file storage',
                'File sharing',
                'Access on all devices',
                'Secure cloud storage',
            ]
        ];

        // Fallback if $fileStats is not defined
        $fileStats = $fileStats ?? [
            'documents' => ['size' => 0, 'count' => 0],
            'images' => ['size' => 0, 'count' => 0],
            'videos' => ['size' => 0, 'count' => 0],
            'archives' => ['size' => 0, 'count' => 0]
        ];

        // Helper function for formatting bytes if not defined globally
        if (!function_exists('formatBytes')) {
            function formatBytes($bytes, $decimals = 2) {
                if ($bytes === 0) return '0 Bytes';
                $k = 1024;
                $dm = $decimals < 0 ? 0 : $decimals;
                $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                $i = floor(log($bytes) / log($k));
                return number_format($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
            }
        }
    @endphp

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

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative max-w-7xl mx-auto mt-4" role="alert" id="success-alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.remove()">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative max-w-7xl mx-auto mt-4" role="alert" id="error-alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.remove()">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    @endif

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto mt-10 px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">File Settings</h1>
            <p class="text-gray-600 mt-2">Manage your file preferences and storage settings</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Settings Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Storage Usage -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Storage Usage</h2>
                    
                    <div class="mb-6">
                        <div class="flex justify-between text-sm mb-2">
                            <span>{{ auth()->user()->getFormattedUsedStorage() }} used of {{ auth()->user()->getFormattedTotalStorage() }}</span>
                            <span>{{ auth()->user()->getFormattedStoragePercentage() }}</span>
                        </div>
                        <div class="w-full bg-gray-200 h-2 rounded">
                            <div class="{{ auth()->user()->getStorageColorClass() }} h-2 rounded" style="width: {{ auth()->user()->used_storage_percentage }}%"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-purple-500 mb-2">
                                <i data-lucide="file-text" class="w-8 h-8 mx-auto"></i>
                            </div>
                            <p class="text-sm font-medium">Documents</p>
                            <p class="text-gray-500 text-xs">{{ formatBytes($fileStats['documents']['size']) }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $fileStats['documents']['count'] }} files</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-purple-500 mb-2">
                                <i data-lucide="image" class="w-8 h-8 mx-auto"></i>
                            </div>
                            <p class="text-sm font-medium">Images</p>
                            <p class="text-gray-500 text-xs">{{ formatBytes($fileStats['images']['size']) }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $fileStats['images']['count'] }} files</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-purple-500 mb-2">
                                <i data-lucide="video" class="w-8 h-8 mx-auto"></i>
                            </div>
                            <p class="text-sm font-medium">Videos</p>
                            <p class="text-gray-500 text-xs">{{ formatBytes($fileStats['videos']['size']) }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $fileStats['videos']['count'] }} files</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-purple-500 mb-2">
                                <i data-lucide="archive" class="w-8 h-8 mx-auto"></i>
                            </div>
                            <p class="text-sm font-medium">Archives</p>
                            <p class="text-gray-500 text-xs">{{ formatBytes($fileStats['archives']['size']) }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $fileStats['archives']['count'] }} files</p>
                        </div>
                    </div>
                    
                    <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded" onclick="showUpgradePlanModal()">
                        Upgrade Storage Plan
                    </button>
                </div>
                
                <!-- File Preferences -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">File Preferences</h2>
                    
                    <form action="{{ route('file.settings.update') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="default-view">
                                    Default View
                                </label>
                                <select id="default-view" name="default_view" class="w-full p-3 border rounded-lg">
                                    <option value="grid" {{ $userPreferences->default_view == 'grid' ? 'selected' : '' }}>Grid View</option>
                                    <option value="list" {{ $userPreferences->default_view == 'list' ? 'selected' : '' }}>List View</option>
                                    <option value="compact" {{ $userPreferences->default_view == 'compact' ? 'selected' : '' }}>Compact View</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="sort-by">
                                    Sort Files By
                                </label>
                                <select id="sort-by" name="sort_by" class="w-full p-3 border rounded-lg">
                                    <option value="name" {{ $userPreferences->sort_by == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="date" {{ $userPreferences->sort_by == 'date' ? 'selected' : '' }}>Date Modified</option>
                                    <option value="size" {{ $userPreferences->sort_by == 'size' ? 'selected' : '' }}>Size</option>
                                    <option value="type" {{ $userPreferences->sort_by == 'type' ? 'selected' : '' }}>File Type</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox text-purple-600" name="show_hidden_files" {{ $userPreferences->show_hidden_files ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700">Show hidden files</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox text-purple-600" name="auto_organize" {{ $userPreferences->auto_organize ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700">Auto-organize files by type</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="trash-retention">
                                    Trash Retention Period
                                </label>
                                <select id="trash-retention" name="trash_retention" class="w-full p-3 border rounded-lg">
                                    <option value="7" {{ $userPreferences->trash_retention == 7 ? 'selected' : '' }}>7 days</option>
                                    <option value="14" {{ $userPreferences->trash_retention == 14 ? 'selected' : '' }}>14 days</option>
                                    <option value="30" {{ $userPreferences->trash_retention == 30 ? 'selected' : '' }}>30 days</option>
                                    <option value="90" {{ $userPreferences->trash_retention == 90 ? 'selected' : '' }}>90 days</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- File Sharing Settings -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">File Sharing Settings</h2>
                    
                    <form action="{{ route('file.sharing.update') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="default-permission">
                                    Default Permission for Shared Files
                                </label>
                                <select id="default-permission" name="default_permission" class="w-full p-3 border rounded-lg">
                                    <option value="view" {{ $sharingPreferences->default_permission == 'view' ? 'selected' : '' }}>View only</option>
                                    <option value="edit" {{ $sharingPreferences->default_permission == 'edit' ? 'selected' : '' }}>Edit</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" id="notify-on-access" name="notify_on_access" class="form-checkbox text-purple-600" 
                                    {{ $sharingPreferences->notify_on_access ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700">Notify me when files are accessed</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" id="password-protect" name="password_protect_by_default" class="form-checkbox text-purple-600"
                                        {{ $sharingPreferences->password_protect_by_default ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700">Password protect shared files by default</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="default-expiration">
                                    Default Link Expiration
                                </label>
                                <select id="default-expiration" name="default_expiration" class="w-full p-3 border rounded-lg">
                                    <option value="1" {{ $sharingPreferences->default_expiration == 1 ? 'selected' : '' }}>1 day</option>
                                    <option value="7" {{ $sharingPreferences->default_expiration == 7 ? 'selected' : '' }}>7 days</option>
                                    <option value="30" {{ $sharingPreferences->default_expiration == 30 ? 'selected' : '' }}>30 days</option>
                                    <option value="never" {{ $sharingPreferences->default_expiration === null ? 'selected' : '' }}>Never</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                                Save Sharing Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-black text-white p-4 rounded-lg shadow">
                    <h2 class="font-semibold mb-4">Storage Plan</h2>
                    <div class="mb-4">
                        <p class="text-lg font-medium">{{ $userPlan->name }}</p>
                        <p class="text-gray-400">{{ $userPlan->storage_gb }} GB Storage</p>
                    </div>
                    <ul class="space-y-2 text-sm mb-4">
                        @foreach($userPlan->features as $feature)
                            <li class="flex items-start">
                                <i data-lucide="check" class="w-5 h-5 text-purple-400 mr-2 flex-shrink-0"></i>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded" onclick="showUpgradePlanModal()">
                        Upgrade Plan
                    </button>
                </div>
                
                <div class="bg-black text-white p-4 rounded-lg shadow">
                    <h2 class="font-semibold mb-4">Storage Optimizer</h2>
                    <p class="text-sm mb-4">Free up space by removing duplicate and large files.</p>
                    <form action="{{ route('files.scan-duplicates') }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded">
                            Scan for Duplicates
                        </button>
                    </form>
                    <form action="{{ route('files.find-large') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white py-2 rounded">
                            Find Large Files
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Upgrade Plan Modal -->
    <div id="upgrade-plan-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Upgrade Your Plan</h3>
                <button onclick="closeUpgradePlanModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="mb-6">
                <div class="space-y-4">
                    <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-bold">Pro Plan</h4>
                                <p class="text-gray-600">1 TB Storage</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">$9.99</p>
                                <p class="text-gray-600">per month</p>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-bold">Business Plan</h4>
                                <p class="text-gray-600">5 TB Storage</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">$24.99</p>
                                <p class="text-gray-600">per month</p>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-bold">Enterprise Plan</h4>
                                <p class="text-gray-600">Unlimited Storage</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">$49.99</p>
                                <p class="text-gray-600">per month</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeUpgradePlanModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
                <button class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Select Plan</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-xl font-bold">DocuFlow</h3>
                    <p class="text-gray-400">Your secure cloud storage solution</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white">Contact Us</a>
                </div>
            </div>
            <div class="mt-8 text-center text-gray-400 text-sm">
                &copy; {{ date('Y') }} DocuFlow. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Upgrade Plan Modal
        function showUpgradePlanModal() {
            document.getElementById('upgrade-plan-modal').classList.remove('hidden');
        }

        function closeUpgradePlanModal() {
            document.getElementById('upgrade-plan-modal').classList.add('hidden');
        }

        // Close flash messages after 5 seconds
        setTimeout(function() {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');
            
            if (successAlert) {
                successAlert.style.opacity = '0';
                successAlert.style.transition = 'opacity 1s';
                setTimeout(() => successAlert.remove(), 1000);
            }
            
            if (errorAlert) {
                errorAlert.style.opacity = '0';
                errorAlert.style.transition = 'opacity 1s';
                setTimeout(() => errorAlert.remove(), 1000);
            }
        }, 5000);

        // Escape key closes modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUpgradePlanModal();
            }
        });

        // Click outside modal to close
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('upgrade-plan-modal');
            if (e.target === modal) {
                closeUpgradePlanModal();
            }
        });
        
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