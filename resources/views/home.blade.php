<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuFlow - Home</title>
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
        @auth
            <!-- LOGGED IN VIEW: Dashboard Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-3 space-y-6">
                    <h1 class="text-3xl font-bold text-gray-800">Welcome Back, {{ auth()->user()->name ?? 'User' }}!</h1>
                    <input type="text" class="w-full p-3 border rounded-lg" placeholder="Search your files">

                    <!-- Recently Edited -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="font-semibold text-gray-700 text-lg">Recently Edited</h2>
                            <a href="{{ route('folders') }}" class="text-blue-500 text-sm">View All</a>
                        </div>
                        <div class="flex space-x-4 overflow-x-auto">
                            @php
                                $recentFiles = auth()->user()->files()
                                    ->where('is_trashed', false)
                                    ->latest('updated_at')
                                    ->take(5)
                                    ->get();
                            @endphp
                            
                            @forelse($recentFiles as $file)
                                <div class="w-56 h-32 bg-white p-4 shadow rounded-xl">
                                    <div class="font-medium truncate">{{ $file->name }}</div>
                                    <span class="text-sm text-gray-500">Edited {{ $file->updated_at->diffForHumans() }}</span>
                                </div>
                            @empty
                                <div class="w-full h-32 bg-white p-4 shadow rounded-xl flex items-center justify-center">
                                    <p class="text-gray-500">No recently edited files</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Upload Files -->
                    <div>
                        <h2 class="font-semibold text-gray-700 text-lg mb-2">Upload Files</h2>
                        <form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label for="folder" class="block text-gray-700 text-sm font-bold mb-2">
                                    Select Folder (Optional)
                                </label>
                                <select id="folder" name="folder_id" class="w-full p-3 border rounded-lg">
                                    <option value="">Root Directory</option>
                                    @if(Schema::hasTable('docuflow_folders'))
                                        @foreach(auth()->user()->activeFolders()->get() as $folder)
                                            <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                                        @endforeach
                                     @endif
                                </select>
                            </div>
                            <div class="w-full h-32 flex items-center justify-center bg-purple-50 border-2 border-dashed rounded-xl text-gray-600 relative">
                                <input type="file" name="files[]" id="file-upload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" multiple>
                                <div class="text-center">
                                    <i data-lucide="upload-cloud" class="w-6 h-6 mx-auto mb-2"></i>
                                    <p>Drag and drop files, or <span class="text-purple-600 font-medium ml-1">Browse</span></p>
                                </div>
                            </div>
                            <div id="file-list" class="mb-4 mt-2"></div>
                            <div class="mt-2 flex justify-between items-center">
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                                    Upload
                                </button>
                                <div class="text-sm text-gray-500">
                                    <span id="selected-count">0</span> files selected
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Your Files Table -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="font-semibold text-gray-700 text-lg">Your Files</h2>
                            <a href="{{ route('folders') }}" class="text-blue-500 text-sm">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white rounded-lg">
                                <thead class="bg-gray-200 text-left">
                                    <tr>
                                        <th class="px-4 py-2">Name</th>
                                        <th class="px-4 py-2">Shared Users</th>
                                        <th class="px-4 py-2">File Size</th>
                                        <th class="px-4 py-2">Last Modified</th>
                                        <th class="px-4 py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(auth()->user()->files()->latest()->take(5)->get() as $file)
                                        <tr>
                                            <td class="px-4 py-2 border-t">{{ $file->name }}</td>
                                            <td class="px-4 py-2 border-t">{{ $file->shared_users_count ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 border-t">{{ $file->human_readable_size }}</td>
                                            <td class="px-4 py-2 border-t">{{ $file->updated_at->format('F d, Y') }}</td>
                                            <td class="px-4 py-2 border-t">
                                                <div class="flex space-x-2">
                                                    <form action="{{ route('files.toggle-star', $file->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="{{ $file->is_starred ? 'text-yellow-500' : 'text-gray-400' }}">
                                                            <i data-lucide="star" class="w-5 h-5"></i>
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('share') }}?file={{ $file->id }}" class="text-blue-500">
                                                        <i data-lucide="share" class="w-5 h-5"></i>
                                                    </a>
                                                    <form action="{{ route('files.delete', $file->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500">
                                                            <i data-lucide="trash" class="w-5 h-5"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-2 border-t text-center text-gray-500">
                                                No files uploaded yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="space-y-6">
                    <div class="bg-black text-white p-4 rounded-lg shadow">
                        <h2 class="font-semibold mb-2">Storage</h2>
                        <div class="flex justify-between text-sm">
                            <span>{{ auth()->user()->used_storage ?? '0' }} gb / {{ auth()->user()->total_storage ?? '256' }} gb</span>
                            <a href="{{ route('file.settings') }}" class="text-purple-400">View Details</a>
                        </div>
                        <div class="w-full bg-gray-700 h-2 rounded mt-2">
                            <div class="bg-purple-500 h-2 rounded w-1/4"></div>
                        </div>
                        <button class="mt-4 w-full bg-purple-600 hover:bg-purple-700 text-white text-sm py-1.5 rounded">Smart Optimizer</button>
                    </div>
                    <div class="bg-black text-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="font-semibold">File Type</h2>
                            <a href="{{ route('file.settings') }}" class="text-purple-400 text-sm">View Details</a>
                        </div>
                        <ul class="space-y-2 text-sm">
                            <li>Documents - 0 gb</li>
                            <li>Video - 0 </li>
                            <li>Audio - 0 gb</li>
                            <li>Photos - 0 gb</li>
                        </ul>
                        <div class="mt-4 bg-gray-800 text-center p-3 rounded">
                            <p class="text-sm">Get More Space For Files</p>
                            <p class="text-xs text-gray-400">More than 200 GBs for your files</p>
                            <button class="mt-2 bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm">Upgrade Storage</button>
                        </div>
                    </div>
                </aside>
            </div>
        @else
            <!-- NOT LOGGED IN VIEW: Welcome Page -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to DocuFlow</h1>
                <p class="text-xl text-gray-600">Your secure cloud storage solution</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left side: Login Form -->
                    <div class="border-r pr-8">
                        <h2 class="text-2xl font-semibold mb-6 text-center">Login to Your Account</h2>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('username')
                                    <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-6">
                                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                                <input id="password" type="password" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('password')
                                    <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex items-center justify-between mb-6">
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                                    Login
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- Right side: Features -->
                    <div class="pl-8">
                        <h2 class="text-2xl font-semibold mb-6 text-center">Secure Cloud Storage</h2>
                        <div class="text-center mb-6">
                            <i data-lucide="cloud" class="w-16 h-16 mx-auto mb-4 text-purple-500"></i>
                            <p class="text-gray-600">Sign up to start uploading and managing your files in the cloud.</p>
                        </div>
                        <div class="text-center">
                            <p class="mb-4">Don't have an account?</p>
                            <a href="{{ route('register') }}" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded inline-block">
                                Register Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="bg-purple-100 p-3 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="shield" class="w-8 h-8 text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Secure Storage</h3>
                    <p class="text-gray-600">Your files are encrypted and stored securely in the cloud.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="bg-purple-100 p-3 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="share-2" class="w-8 h-8 text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Easy Sharing</h3>
                    <p class="text-gray-600">Share files with others with just a few clicks.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="bg-purple-100 p-3 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="device-mobile" class="w-8 h-8 text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Access Anywhere</h3>
                    <p class="text-gray-600">Access your files from any device, anywhere, anytime.</p>
                </div>
            </div>
        @endauth
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
        
        // File upload preview
        const fileUpload = document.getElementById('file-upload');
        const fileList = document.getElementById('file-list');
        const selectedCount = document.getElementById('selected-count');

        if (fileUpload) {
            fileUpload.addEventListener('change', function() {
                fileList.innerHTML = '';
                selectedCount.textContent = this.files.length;
                
                for (let i = 0; i < this.files.length; i++) {
                    const file = this.files[i];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
                    
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex justify-between items-center p-2 bg-gray-50 rounded mb-2';
                    fileItem.innerHTML = `
                        <div class="flex items-center">
                            <i data-lucide="file" class="w-5 h-5 mr-2 text-gray-500"></i>
                            <span class="text-sm">${file.name}</span>
                        </div>
                        <span class="text-xs text-gray-500">${fileSize} MB</span>
                    `;
                    
                    fileList.appendChild(fileItem);
                }
                
                // Re-initialize Lucide icons for the newly added elements
                lucide.createIcons();
            });
        }
    </script>
</body>
</html>