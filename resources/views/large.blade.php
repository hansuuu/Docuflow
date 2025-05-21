<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DocuFlow - Large Files</title>
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
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Large Files</h1>
            <p class="text-gray-600 mt-2">Review and manage large files to free up storage space</p>
        </div>
        <a href="{{ route('file.settings') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded flex items-center">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Settings
        </a>
    </div>

    @if(count($largeFiles) > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Found {{ count($largeFiles) }} large files</h2>
                <p class="text-gray-600 mt-1">These files are using a significant amount of your storage</p>
            </div>
            
            <form action="{{ route('files.bulk-delete') }}" method="POST">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-left">
                            <tr>
                                <th class="px-4 py-2 w-12">
                                    <input type="checkbox" id="select-all" class="form-checkbox text-purple-600">
                                </th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Type</th>
                                <th class="px-4 py-2">Size</th>
                                <th class="px-4 py-2">Last Modified</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($largeFiles as $file)
                                <tr>
                                    <td class="px-4 py-2 border-t">
                                        <input type="checkbox" name="files_to_delete[]" value="{{ $file->id }}" class="form-checkbox text-purple-600 file-checkbox">
                                    </td>
                                    <td class="px-4 py-2 border-t">{{ $file->name }}</td>
                                    <td class="px-4 py-2 border-t">{{ ucfirst(substr($file->mime_type, strpos($file->mime_type, '/') + 1)) }}</td>
                                    <td class="px-4 py-2 border-t">{{ formatBytes($file->size) }}</td>
                                    <td class="px-4 py-2 border-t">{{ date('M d, Y', strtotime($file->updated_at)) }}</td>
                                    <td class="px-4 py-2 border-t">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('files.download', $file->id) }}" class="text-blue-500" title="Download file">
                                                <i data-lucide="download" class="w-5 h-5"></i>
                                            </a>
                                            <a href="{{ route('files.show', $file->id) }}" class="text-green-500" title="View file">
                                                <i data-lucide="eye" class="w-5 h-5"></i>
                                            </a>
                                            <form action="{{ route('files.delete', $file->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500" title="Move to trash">
                                                    <i data-lucide="trash" class="w-5 h-5"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <div class="text-gray-600 text-sm">
                        <span id="selected-count">0</span> files selected (<span id="selected-size">0 B</span>)
                    </div>
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                        Move Selected to Trash
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Size Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4">Storage Usage by File Size</h2>
            <div class="h-64">
                <canvas id="sizeDistributionChart"></canvas>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-gray-400 mb-4">
                <i data-lucide="file-check" class="w-16 h-16 mx-auto"></i>
            </div>
            <h2 class="text-2xl font-semibold mb-2">No Large Files Found</h2>
            <p class="text-gray-600 mb-6">You don't have any files larger than {{ $threshold ?? '100 MB' }} in your storage.</p>
            <a href="{{ route('file.settings') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                Back to Settings
            </a>
        </div>
    @endif
</main>

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

<!-- Chart.js for visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

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

    // Select all functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const fileCheckboxes = document.querySelectorAll('.file-checkbox');
    const selectedCount = document.getElementById('selected-count');
    const selectedSize = document.getElementById('selected-size');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            fileCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedStats();
        });
    }
    
    fileCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedStats);
    });
    
    function updateSelectedStats() {
        const selected = document.querySelectorAll('.file-checkbox:checked');
        let totalSize = 0;
        
        selected.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const sizeText = row.querySelector('td:nth-child(4)').textContent;
            const sizeMatch = sizeText.match(/([0-9.]+)\s*([KMGT]?B)/i);
            if (sizeMatch) {
                const value = parseFloat(sizeMatch[1]);
                const unit = sizeMatch[2].toUpperCase();
                
                // Convert to bytes
                switch (unit) {
                    case 'KB': totalSize += value * 1024; break;
                    case 'MB': totalSize += value * 1024 * 1024; break;
                    case 'GB': totalSize += value * 1024 * 1024 * 1024; break;
                    case 'TB': totalSize += value * 1024 * 1024 * 1024 * 1024; break;
                    default: totalSize += value; break;
                }
            }
        });
        
        selectedCount.textContent = selected.length;
        selectedSize.textContent = formatBytes(totalSize);
    }

    // Initialize charts if there are large files
    @if(count($largeFiles ?? []) > 0)
        const ctx = document.getElementById('sizeDistributionChart').getContext('2d');
        
        // Prepare data
        const fileNames = {!! json_encode($largeFiles->pluck('name')) !!};
        const fileSizes = {!! json_encode($largeFiles->pluck('size')) !!};
        
        // Create chart
        const sizeDistributionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'File Size',
                    data: fileSizes,
                    backgroundColor: 'rgba(126, 34, 206, 0.5)',
                    borderColor: 'rgba(126, 34, 206, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatBytes(value);
                            }
                        }
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function(context) {
                            return formatBytes(context.raw);
                        }
                    }
                }
            }
        });
    @endif

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