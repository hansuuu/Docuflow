<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow - Files & Folders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .nav-item {
            @apply relative flex items-center justify-center w-10 h-10 rounded-full transition-all duration-200;
        }
        .nav-item:hover {
            @apply bg-gray-800;
        }
        .nav-item.active {
            @apply bg-purple-600;
        }
        .nav-item .tooltip {
            @apply absolute invisible opacity-0 -bottom-10 bg-gray-900 text-white text-xs py-1 px-2 rounded transition-all duration-200;
            min-width: 80px;
        }
        .nav-item:hover .tooltip {
            @apply visible opacity-100;
        }
        .notification-dot {
            @apply absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full;
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
    <main class="max-w-7xl mx-auto mt-6 px-4 pb-12">
        <!-- Breadcrumb and Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div class="mb-4 md:mb-0">
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <a href="{{ route('dashboard') }}" class="hover:text-purple-600">Home</a>
                    <i data-lucide="chevron-right" class="w-4 h-4 mx-1"></i>
                    @if(isset($currentFolder))
                        @foreach($breadcrumbs as $breadcrumb)
                            <a href="{{ route('folders.show', $breadcrumb['id']) }}" class="hover:text-purple-600">{{ $breadcrumb['name'] }}</a>
                            <i data-lucide="chevron-right" class="w-4 h-4 mx-1"></i>
                        @endforeach
                        <span>{{ $currentFolder->name }}</span>
                    @else
                        <span>All Folders</span>
                    @endif
                </div>
                <h1 class="text-2xl font-bold text-gray-800">
                    @if(isset($currentFolder))
                        {{ $currentFolder->name }}
                    @else
                        All Folders
                    @endif
                </h1>
            </div>
            
            <div class="flex space-x-2">
                <button onclick="showCreateFolderModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded flex items-center">
                    <i data-lucide="folder-plus" class="w-4 h-4 mr-2"></i>
                    New Folder
                </button>
                <button onclick="document.getElementById('file-upload').click()" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded flex items-center">
                    <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                    Upload Files
                </button>
                <form id="upload-form" action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" id="file-upload" name="files[]" multiple onchange="document.getElementById('upload-form').submit()">
                    @if(isset($currentFolder))
                        <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
                    @endif
                </form>
            </div>
        </div>
        
        <!-- View Controls -->
        <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow">
            <div class="flex items-center space-x-2">
                <button class="p-2 rounded hover:bg-gray-100 active" id="grid-view-btn" onclick="switchView('grid')">
                    <i data-lucide="grid" class="w-5 h-5"></i>
                </button>
                <button class="p-2 rounded hover:bg-gray-100" id="list-view-btn" onclick="switchView('list')">
                    <i data-lucide="list" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="flex items-center">
                <label for="sort-by" class="mr-2 text-sm text-gray-600">Sort by:</label>
                <select id="sort-by" class="border rounded p-2 text-sm" onchange="sortItems(this.value)">
                    <option value="name">Name</option>
                    <option value="date">Date Modified</option>
                    <option value="size">Size</option>
                    <option value="type">Type</option>
                </select>
            </div>
        </div>
        
        <!-- Folders Grid View -->
        <div id="grid-view" class="mb-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Folders</h2>
            
            @if(count($folders) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($folders as $folder)
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 overflow-hidden">
                            <div class="relative p-4">
                                <div class="flex justify-center mb-2">
                                    <i data-lucide="folder" class="w-16 h-16 text-yellow-400"></i>
                                </div>
                                <h3 class="font-medium text-center truncate" title="{{ $folder->name }}">{{ $folder->name }}</h3>
                                <p class="text-xs text-gray-500 text-center">{{ $folder->files_count }} files</p>
                                
                                <div class="absolute top-2 right-2">
                                    <button class="text-gray-400 hover:text-gray-600" onclick="showFolderMenu({{ $folder->id }})">
                                        <i data-lucide="more-vertical" class="w-5 h-5"></i>
                                    </button>
                                    <div id="folder-menu-{{ $folder->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                        <a href="{{ route('folders.show', $folder->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Open</a>
                                        <button onclick="showRenameFolderModal({{ $folder->id }}, '{{ $folder->name }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Rename</button>
                                        <a href="{{ route('files.share', $folder->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Share</a>
                                        <form action="{{ route('folders.toggle-star', $folder->id) }}" method="POST" class="block">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                {{ $folder->is_starred ? 'Unstar' : 'Star' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('folders.delete', $folder->id) }}" method="POST" class="block" onsubmit="return confirm('Are you sure you want to move this folder to trash?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Move to Trash</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('folders.show', $folder->id) }}" class="block bg-gray-50 p-2 text-center text-sm text-purple-600 hover:bg-gray-100 transition-colors duration-200">Open Folder</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg p-8 text-center">
                    <i data-lucide="folder" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No folders yet</h3>
                    <p class="text-gray-500 mb-4">Create a new folder to organize your files</p>
                    <button onclick="showCreateFolderModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                        Create Folder
                    </button>
                </div>
            @endif
        </div>
        
        <!-- Files Grid View -->
        <div id="grid-view-files" class="mb-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Files</h2>
            
            @if(count($files) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($files as $file)
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 overflow-hidden">
                            <div class="relative p-4">
                                <div class="flex justify-center mb-2">
                                    @if(in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/gif']))
                                        <img src="{{ route('files.thumbnail', $file->id) }}" alt="{{ $file->name }}" class="w-16 h-16 object-cover rounded">
                                    @elseif(strpos($file->mime_type, 'pdf') !== false)
                                        <i data-lucide="file-text" class="w-16 h-16 text-red-500"></i>
                                    @elseif(strpos($file->mime_type, 'word') !== false)
                                        <i data-lucide="file-text" class="w-16 h-16 text-blue-500"></i>
                                    @elseif(strpos($file->mime_type, 'excel') !== false || strpos($file->mime_type, 'spreadsheet') !== false)
                                        <i data-lucide="file-text" class="w-16 h-16 text-green-500"></i>
                                    @elseif(strpos($file->mime_type, 'video') !== false)
                                        <i data-lucide="video" class="w-16 h-16 text-purple-500"></i>
                                    @elseif(strpos($file->mime_type, 'audio') !== false)
                                        <i data-lucide="music" class="w-16 h-16 text-pink-500"></i>
                                    @else
                                        <i data-lucide="file" class="w-16 h-16 text-gray-500"></i>
                                    @endif
                                </div>
                                <h3 class="font-medium text-center truncate" title="{{ $file->name }}">{{ $file->name }}</h3>
                                <p class="text-xs text-gray-500 text-center">{{ formatBytes($file->size) }}</p>
                                
                                <div class="absolute top-2 right-2">
                                    <button class="text-gray-400 hover:text-gray-600" onclick="showFileMenu({{ $file->id }})">
                                        <i data-lucide="more-vertical" class="w-5 h-5"></i>
                                    </button>
                                    <div id="file-menu-{{ $file->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                        <a href="{{ route('files.show', $file->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View</a>
                                        <a href="{{ route('files.download', $file->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Download</a>
                                        <a href="{{ route('files.share', $file->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Share</a>
                                        <button onclick="showRenameFileModal({{ $file->id }}, '{{ $file->name }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Rename</button>
                                        <form action="{{ route('files.toggle-star', $file->id) }}" method="POST" class="block">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                {{ $file->is_starred ? 'Unstar' : 'Star' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('files.delete', $file->id) }}" method="POST" class="block" onsubmit="return confirm('Are you sure you want to move this file to trash?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Move to Trash</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('files.show', $file->id) }}" class="block bg-gray-50 p-2 text-center text-sm text-purple-600 hover:bg-gray-100 transition-colors duration-200">View File</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg p-8 text-center">
                    <i data-lucide="file" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No files yet</h3>
                    <p class="text-gray-500 mb-4">Upload files to get started</p>
                    <button onclick="document.getElementById('file-upload').click()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                        Upload Files
                    </button>
                </div>
            @endif
        </div>
        
        <!-- List View (Hidden by default) -->
        <div id="list-view" class="hidden">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50 text-gray-600 text-sm">
                        <tr>
                            <th class="py-3 px-4 text-left">Name</th>
                            <th class="py-3 px-4 text-left">Type</th>
                            <th class="py-3 px-4 text-left">Size</th>
                            <th class="py-3 px-4 text-left">Modified</th>
                            <th class="py-3 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($folders as $folder)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <a href="{{ route('folders.show', $folder->id) }}" class="flex items-center text-purple-600 hover:text-purple-800">
                                        <i data-lucide="folder" class="w-5 h-5 text-yellow-400 mr-2"></i>
                                        <span>{{ $folder->name }}</span>
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-gray-500">Folder</td>
                                <td class="py-3 px-4 text-gray-500">-</td>
                                <td class="py-3 px-4 text-gray-500">{{ $folder->updated_at->format('M d, Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <button class="text-gray-500 hover:text-gray-700" onclick="showFolderMenu({{ $folder->id }})">
                                            <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        
                        @foreach($files as $file)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <a href="{{ route('files.show', $file->id) }}" class="flex items-center text-gray-700 hover:text-purple-600">
                                        @if(in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/gif']))
                                            <i data-lucide="image" class="w-5 h-5 text-blue-500 mr-2"></i>
                                        @elseif(strpos($file->mime_type, 'pdf') !== false)
                                            <i data-lucide="file-text" class="w-5 h-5 text-red-500 mr-2"></i>
                                        @elseif(strpos($file->mime_type, 'word') !== false)
                                            <i data-lucide="file-text" class="w-5 h-5 text-blue-500 mr-2"></i>
                                        @elseif(strpos($file->mime_type, 'excel') !== false || strpos($file->mime_type, 'spreadsheet') !== false)
                                            <i data-lucide="file-text" class="w-5 h-5 text-green-500 mr-2"></i>
                                        @elseif(strpos($file->mime_type, 'video') !== false)
                                            <i data-lucide="video" class="w-5 h-5 text-purple-500 mr-2"></i>
                                        @elseif(strpos($file->mime_type, 'audio') !== false)
                                            <i data-lucide="music" class="w-5 h-5 text-pink-500 mr-2"></i>
                                        @else
                                            <i data-lucide="file" class="w-5 h-5 text-gray-500 mr-2"></i>
                                        @endif
                                        <span>{{ $file->name }}</span>
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-gray-500">{{($file->mime_type) }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ formatBytes($file->size) }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $file->updated_at->format('M d, Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('files.download', $file->id) }}" class="text-gray-500 hover:text-gray-700" title="Download">
                                            <i data-lucide="download" class="w-5 h-5"></i>
                                        </a>
                                        <a href="{{ route('files.share', $file->id) }}" class="text-gray-500 hover:text-gray-700" title="Share">
                                            <i data-lucide="share" class="w-5 h-5"></i>
                                        </a>
                                        <button class="text-gray-500 hover:text-gray-700" onclick="showFileMenu({{ $file->id }})" title="More">
                                            <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        
                        @if(count($folders) === 0 && count($files) === 0)
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">
                                    <i data-lucide="folder" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                                    <p class="text-lg font-medium">This folder is empty</p>
                                    <p class="text-sm">Upload files or create folders to get started</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Create Folder Modal -->
    <div id="create-folder-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Create New Folder</h3>
                <button onclick="hideCreateFolderModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="{{ route('folders.store') }}" method="POST">
                @csrf
                @if(isset($currentFolder))
                    <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}">
                @endif
                <div class="mb-4">
                    <label for="folder-name" class="block text-gray-700 text-sm font-bold mb-2">Folder Name</label>
                    <input type="text" id="folder-name" name="name" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideCreateFolderModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Create Folder</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rename Folder Modal -->
    <div id="rename-folder-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Rename Folder</h3>
                <button onclick="hideRenameFolderModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form id="rename-folder-form" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="rename-folder-name" class="block text-gray-700 text-sm font-bold mb-2">New Folder Name</label>
                    <input type="text" id="rename-folder-name" name="name" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRenameFolderModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Rename Folder</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rename File Modal -->
    <div id="rename-file-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Rename File</h3>
                <button onclick="hideRenameFileModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form id="rename-file-form" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="rename-file-name" class="block text-gray-700 text-sm font-bold mb-2">New File Name</label>
                    <input type="text" id="rename-file-name" name="name" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRenameFileModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Rename File</button>
                </div>
            </form>
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

        // Helper function to format bytes
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Helper function to get file type
        function getFileType(mimeType) {
            if (mimeType.includes('image')) return 'Image';
            if (mimeType.includes('pdf')) return 'PDF';
            if (mimeType.includes('word')) return 'Word';
            if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'Excel';
            if (mimeType.includes('video')) return 'Video';
            if (mimeType.includes('audio')) return 'Audio';
            return 'Document';
        }

        // Switch between grid and list view
        function switchView(view) {
            if (view === 'grid') {
                document.getElementById('grid-view').classList.remove('hidden');
                document.getElementById('grid-view-files').classList.remove('hidden');
                document.getElementById('list-view').classList.add('hidden');
                document.getElementById('grid-view-btn').classList.add('active');
                document.getElementById('list-view-btn').classList.remove('active');
            } else {
                document.getElementById('grid-view').classList.add('hidden');
                document.getElementById('grid-view-files').classList.add('hidden');
                document.getElementById('list-view').classList.remove('hidden');
                document.getElementById('grid-view-btn').classList.remove('active');
                document.getElementById('list-view-btn').classList.add('active');
            }
        }

        // Sort items
        function sortItems(sortBy) {
            // This would typically be handled by reloading the page with a sort parameter
            // For now, we'll just show a message
            alert('Sorting by ' + sortBy + ' would be implemented here.');
        }

        // Show/hide folder menu
        function showFolderMenu(folderId) {
            // Hide all other menus first
            document.querySelectorAll('[id^="folder-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
            document.querySelectorAll('[id^="file-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
            
            // Show this menu
            const menu = document.getElementById('folder-menu-' + folderId);
            menu.classList.remove('hidden');
            
            // Add click outside listener
            setTimeout(() => {
                document.addEventListener('click', function closeMenu(e) {
                    if (!menu.contains(e.target) && e.target.id !== 'folder-menu-' + folderId) {
                        menu.classList.add('hidden');
                        document.removeEventListener('click', closeMenu);
                    }
                });
            }, 0);
        }

        // Show/hide file menu
        function showFileMenu(fileId) {
            // Hide all other menus first
            document.querySelectorAll('[id^="folder-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
            document.querySelectorAll('[id^="file-menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
            
            // Show this menu
            const menu = document.getElementById('file-menu-' + fileId);
            menu.classList.remove('hidden');
            
            // Add click outside listener
            setTimeout(() => {
                document.addEventListener('click', function closeMenu(e) {
                    if (!menu.contains(e.target) && e.target.id !== 'file-menu-' + fileId) {
                        menu.classList.add('hidden');
                        document.removeEventListener('click', closeMenu);
                    }
                });
            }, 0);
        }

        // Create folder modal
        function showCreateFolderModal() {
            document.getElementById('create-folder-modal').classList.remove('hidden');
        }

        function hideCreateFolderModal() {
            document.getElementById('create-folder-modal').classList.add('hidden');
        }

        // Rename folder modal
        function showRenameFolderModal(folderId, folderName) {
            document.getElementById('rename-folder-form').action = '/folders/' + folderId + '/rename';
            document.getElementById('rename-folder-name').value = folderName;
            document.getElementById('rename-folder-modal').classList.remove('hidden');
        }

        function hideRenameFolderModal() {
            document.getElementById('rename-folder-modal').classList.add('hidden');
        }

        // Rename file modal
        function showRenameFileModal(fileId, fileName) {
            document.getElementById('rename-file-form').action = '/files/' + fileId + '/rename';
            document.getElementById('rename-file-name').value = fileName;
            document.getElementById('rename-file-modal').classList.remove('hidden');
        }

        function hideRenameFileModal() {
            document.getElementById('rename-file-modal').classList.add('hidden');
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

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            const createFolderModal = document.getElementById('create-folder-modal');
            const renameFolderModal = document.getElementById('rename-folder-modal');
            const renameFileModal = document.getElementById('rename-file-modal');
            
            if (e.target === createFolderModal) {
                hideCreateFolderModal();
            }
            
            if (e.target === renameFolderModal) {
                hideRenameFolderModal();
            }
            
            if (e.target === renameFileModal) {
                hideRenameFileModal();
            }
        });
        
        // Toggle notifications dropdown
        function toggleNotifications() {
            const dropdown = document.getElementById('notification-dropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close notifications dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notification-dropdown');
            const notificationButton = event.target.closest('.nav-item');
            
            if (!dropdown) return;
            
            if (notificationButton && notificationButton.querySelector('[data-lucide="bell"]')) {
                return;
            }
            
            if (!dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
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
