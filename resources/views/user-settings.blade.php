<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow - User Settings</title>
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
        
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-input:focus {
            outline: none;
            border-color: #7e22ce;
            box-shadow: 0 0 0 2px rgba(126, 34, 206, 0.1);
        }
        .btn {
            padding: 10px 16px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #7e22ce;
            color: white;
        }
        .btn-primary:hover {
            background-color: #6b21a8;
        }
        .btn-outline {
            background-color: transparent;
            border: 1px solid #7e22ce;
            color: #7e22ce;
        }
        .btn-outline:hover {
            background-color: rgba(126, 34, 206, 0.1);
        }
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .card-header i {
            margin-right: 12px;
            color: #7e22ce;
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .avatar-upload {
            position: relative;
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }
        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            background-color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar-edit {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 30px;
            height: 30px;
            background-color: #7e22ce;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .avatar-edit i {
            color: white;
        }
        .avatar-upload input {
            display: none;
        }
        .progress-bar {
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            background-color: #7e22ce;
        }
        .status-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .status-item i {
            margin-right: 8px;
        }
        .status-success {
            color: #10b981;
        }
        .status-warning {
            color: #f59e0b;
        }
        .status-danger {
            color: #ef4444;
        }
        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-icon {
            margin-right: 12px;
            color: #7e22ce;
        }
        .activity-content {
            flex: 1;
        }
        .activity-time {
            font-size: 12px;
            color: #6b7280;
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
    <div class="max-w-7xl mx-auto mt-10 px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">User Settings</h1>
            <p class="text-gray-600 mt-2">Manage your account and preferences</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert" id="success-alert">
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
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert" id="error-alert">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - User Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Information -->
                <div class="card">
                    <div class="card-header">
                        <i data-lucide="user" class="w-6 h-6"></i>
                        <h2 class="card-title">Profile Information</h2>
                    </div>
                    
                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="avatar-upload">
                            <label for="avatar-upload">
                                <div class="avatar-preview">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile" id="avatar-preview-img" class="w-full h-full object-cover">
                                    @else
                                        <i data-lucide="user" class="w-12 h-12 text-gray-400" id="avatar-default-icon"></i>
                                    @endif
                                </div>
                                <div class="avatar-edit">
                                    <i data-lucide="camera" class="w-4 h-4"></i>
                                </div>
                            </label>
                            <input type="file" name="avatar" id="avatar-upload" accept="image/*">
                            <p class="text-xs text-gray-500 mt-2">JPG, GIF or PNG. Max size 2MB</p>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name</label>
                            <input class="form-input" id="name" type="text" name="name" value="{{ auth()->user()->name ?? 'John Doe' }}">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="username">Username</label>
                            <input class="form-input" id="username" type="text" name="username" value="{{ auth()->user()->username ?? 'johndoe' }}">
                            @error('username')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input class="form-input" id="email" type="email" name="email" value="{{ auth()->user()->email ?? 'john@example.com' }}">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                    </form>
                </div>
                
                <!-- Change Password -->
                <div class="card">
                    <div class="card-header">
                        <i data-lucide="lock" class="w-6 h-6"></i>
                        <h2 class="card-title">Change Password</h2>
                    </div>
                    
                    <form action="{{ route('user.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label class="form-label" for="current-password">Current Password</label>
                            <input class="form-input" id="current-password" type="password" name="current_password">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="new-password">New Password</label>
                            <input class="form-input" id="new-password" type="password" name="password">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="confirm-password">Confirm New Password</label>
                            <input class="form-input" id="confirm-password" type="password" name="password_confirmation">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
                
                <!-- Notification Settings -->
                <div class="card">
                    <div class="card-header">
                        <i data-lucide="bell" class="w-6 h-6"></i>
                        <h2 class="card-title">Notification Settings</h2>
                    </div>
                    
                    <form action="{{ route('user.notifications.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900">Email Notifications</h3>
                                    <p class="text-sm text-gray-500">Receive email notifications about file shares, comments, and updates</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="email_notifications" class="sr-only peer" {{ auth()->user()->notification_preferences->email_notifications ?? true ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900">Security Alerts</h3>
                                    <p class="text-sm text-gray-500">Receive alerts about suspicious account activity</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="security_alerts" class="sr-only peer" {{ auth()->user()->notification_preferences->security_alerts ?? true ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900">Marketing Emails</h3>
                                    <p class="text-sm text-gray-500">Receive updates about new features and promotions</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="marketing_emails" class="sr-only peer" {{ auth()->user()->notification_preferences->marketing_emails ?? false ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-6">Save Notification Settings</button>
                    </form>
                </div>
            </div>
            
            <!-- Right Column - Account Information -->
            <div class="space-y-6">
                <!-- Account Status -->
                <div class="card">
                    <div class="card-header">
                        <i data-lucide="user-check" class="w-6 h-6"></i>
                        <h2 class="card-title">Account Status</h2>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-lg font-medium">{{ auth()->user()->is_premium ? 'Premium Account' : 'Standard Account' }}</p>
                        <p class="text-gray-500 text-sm">Active since {{ auth()->user()->created_at ? auth()->user()->created_at->format('F Y') : 'May 2025' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Storage Used</span>
                            <span>{{ auth()->user()->used_storage_percentage ?? '0' }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill" style="width: {{ auth()->user()->used_storage_percentage ?? '0' }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ auth()->user()->getFormattedUsedStorage() ?? '0 KB' }} of {{ auth()->user()->getFormattedTotalStorage() ?? '256 GB' }}</p>
                    </div>
                    
                </div>
                
                <!-- Security Status -->
                <div class="card">
                    <div class="card-header">
                        <i data-lucide="shield" class="w-6 h-6"></i>
                        <h2 class="card-title">Security Status</h2>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="status-item">
                            <i data-lucide="check-circle" class="w-5 h-5 status-success"></i>
                            <span>Email verified</span>
                        </div>
                        
                        @if(auth()->user()->two_factor_enabled ?? false)
                            <div class="status-item">
                                <i data-lucide="check-circle" class="w-5 h-5 status-success"></i>
                                <span>Two-factor authentication enabled</span>
                            </div>
                        @else
                            <div class="status-item">
                                <i data-lucide="x-circle" class="w-5 h-5 status-danger"></i>
                                <span>Two-factor authentication not enabled</span>
                            </div>
                        @endif
                        
                        @if(auth()->user()->password_updated_at && auth()->user()->password_updated_at->diffInDays(now()) < 90)
                            <div class="status-item">
                                <i data-lucide="check-circle" class="w-5 h-5 status-success"></i>
                                <span>Password updated recently</span>
                            </div>
                        @else
                            <div class="status-item">
                                <i data-lucide="alert-circle" class="w-5 h-5 status-warning"></i>
                                <span>Password last changed 90+ days ago</span>
                            </div>
                        @endif
                    </div>
                    
                    <button class="btn btn-primary w-full mt-4" onclick="window.location.href='{{ route('user.2fa.setup') }}'">
                        {{ auth()->user()->two_factor_enabled ?? false ? 'Manage Two-Factor Auth' : 'Enable Two-Factor Auth' }}
                    </button>
                </div>
                
                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <i data-lucide="activity" class="w-6 h-6"></i>
                        <h2 class="card-title">Recent Activity</h2>
                    </div>
                    
                    <div class="space-y-1">
                        @forelse(auth()->user()->recentActivities()->take(3)->get() ?? [] as $activity)
                            <div class="activity-item">
                                <i data-lucide="{{ $activity->icon }}" class="w-5 h-5 activity-icon"></i>
                                <div class="activity-content">
                                    <p>{{ $activity->description }}</p>
                                    <p class="activity-time">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="activity-item">
                                <i data-lucide="log-in" class="w-5 h-5 activity-icon"></i>
                                <div class="activity-content">
                                    <p>Login from Chrome on Windows</p>
                                    <p class="activity-time">Today, 4:05 PM</p>
                                </div>
                            </div>
                            <div class="activity-item">
                                <i data-lucide="upload" class="w-5 h-5 activity-icon"></i>
                                <div class="activity-content">
                                    <p>Uploaded 5 files</p>
                                    <p class="activity-time">Yesterday, 3:30 PM</p>
                                </div>
                            </div>
                            <div class="activity-item">
                                <i data-lucide="share" class="w-5 h-5 activity-icon"></i>
                                <div class="activity-content">
                                    <p>Shared file with testuser@example.com</p>
                                    <p class="activity-time">Apr 28, 2025</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    
                    <a href="{{ route('user.activity') }}" class="block text-center text-purple-600 hover:text-purple-800 text-sm mt-4 transition duration-200">
                        View All Activity
                    </a>
                </div>
                
                <!-- Account Actions -->
                <div class="card">
                    <div class="card-header">
                        <i data-lucide="settings" class="w-6 h-6"></i>
                        <h2 class="card-title">Account Actions</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <form action="{{ route('user.data.download') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline w-full flex items-center justify-center">
                                <i data-lucide="download" class="w-5 h-5 mr-2"></i>
                                Download Your Data
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-outline w-full flex items-center justify-center text-red-600 border-red-600 hover:bg-red-50" onclick="showDeactivateModal()">
                            <i data-lucide="power" class="w-5 h-5 mr-2"></i>
                            Deactivate Account
                        </button>
                        
                        <button type="button" class="btn btn-outline w-full flex items-center justify-center text-red-600 border-red-600 hover:bg-red-50" onclick="showDeleteModal()">
                            <i data-lucide="trash-2" class="w-5 h-5 mr-2"></i>
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivate Account Modal -->
    <div id="deactivate-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Deactivate Your Account</h3>
                <button onclick="closeDeactivateModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="{{ route('user.deactivate') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <p class="text-gray-600 mb-4">Your account will be temporarily deactivated. During this time, your profile and content will not be visible to others.</p>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">
                            Reactivation Options
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="reactivation_type" value="manual" class="mr-2 text-purple-600" checked>
                                <span>Manual reactivation (login to reactivate)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="reactivation_type" value="automatic" class="mr-2 text-purple-600">
                                <span>Automatic reactivation on a specific date</span>
                            </label>
                        </div>
                    </div>
                    
                    <div id="reactivation-date-container" class="hidden mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="reactivation-date">
                            Reactivation Date
                        </label>
                        <input type="date" id="reactivation-date" name="reactivation_date" class="form-input" min="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="deactivation-reason">
                            Reason for Deactivation (Optional)
                        </label>
                        <select id="deactivation-reason" name="deactivation_reason" class="form-input">
                            <option value="">Select a reason</option>
                            <option value="temporary_break">Taking a temporary break</option>
                            <option value="privacy_concerns">Privacy concerns</option>
                            <option value="too_many_emails">Receiving too many emails</option>
                            <option value="not_useful">Not finding the service useful</option>
                            <option value="other">Other reason</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="deactivation-password">
                            Enter your password to confirm
                        </label>
                        <input type="password" id="deactivation-password" name="password" class="form-input" required>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeactivateModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200">
                        Deactivate Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Delete Your Account</h3>
                <button onclick="closeDeleteModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="{{ route('user.delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="mb-6">
                    <div class="p-4 bg-red-50 text-red-700 rounded-lg mb-4">
                        <p class="font-bold">Warning: This action cannot be undone</p>
                        <p class="mt-1">All your data, files, and account information will be permanently deleted.</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="deletion-reason">
                            Reason for Deletion (Optional)
                        </label>
                        <select id="deletion-reason" name="deletion_reason" class="form-input">
                            <option value="">Select a reason</option>
                            <option value="privacy_concerns">Privacy concerns</option>
                            <option value="data_concerns">Data usage concerns</option>
                            <option value="not_useful">Not finding the service useful</option>
                            <option value="found_alternative">Found an alternative service</option>
                            <option value="other">Other reason</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="confirm_deletion" class="mr-2 text-red-600" required>
                            <span>I understand that this action is permanent and cannot be undone</span>
                        </label>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="deletion-password">
                            Enter your password to confirm
                        </label>
                        <input type="password" id="deletion-password" name="password" class="form-input" required>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-200">
                        Delete Account Permanently
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Avatar preview
        document.getElementById('avatar-upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('avatar-preview-img');
                    const defaultIcon = document.getElementById('avatar-default-icon');
                    
                    if (!previewImg) {
                        const img = document.createElement('img');
                        img.id = 'avatar-preview-img';
                        img.className = 'w-full h-full object-cover';
                        img.src = e.target.result;
                        
                        const preview = document.querySelector('.avatar-preview');
                        if (defaultIcon) {
                            preview.removeChild(defaultIcon);
                        }
                        preview.appendChild(img);
                    } else {
                        previewImg.src = e.target.result;
                    }
                }
                reader.readAsDataURL(file);
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

        // Deactivation modal
        function showDeactivateModal() {
            document.getElementById('deactivate-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeactivateModal() {
            document.getElementById('deactivate-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Delete account modal
        function showDeleteModal() {
            document.getElementById('delete-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Toggle reactivation date field
        const reactivationTypeInputs = document.querySelectorAll('input[name="reactivation_type"]');
        const reactivationDateContainer = document.getElementById('reactivation-date-container');
        
        reactivationTypeInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value === 'automatic') {
                    reactivationDateContainer.classList.remove('hidden');
                } else {
                    reactivationDateContainer.classList.add('hidden');
                }
            });
        });

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
                closeDeactivateModal();
                closeDeleteModal();
            }
        });

        // Click outside modal to close
        window.addEventListener('click', function(e) {
            const deactivateModal = document.getElementById('deactivate-modal');
            const deleteModal = document.getElementById('delete-modal');
            
            if (e.target === deactivateModal) {
                closeDeactivateModal();
            }
            
            if (e.target === deleteModal) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>