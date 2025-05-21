<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow - Help & Support</title>
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
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }
        .card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .faq-item {
            border-bottom: 1px solid #e5e7eb;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            text-align: left;
            font-weight: 500;
            color: #111827;
            transition: color 0.2s;
        }
        .faq-question:hover {
            color: #7e22ce;
        }
        .faq-answer {
            margin-top: 0.5rem;
            color: #4b5563;
            display: none;
        }
        .faq-answer.active {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
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
    <main class="flex-grow max-w-7xl mx-auto mt-8 px-4 pb-12">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Help & Support</h1>
            <p class="text-gray-600 mt-2">Find answers to common questions and get support</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Help Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Search -->
                <div class="card">
                    <div class="relative">
                        <input type="text" placeholder="Search for help topics..." class="w-full p-4 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition duration-200">
                        <button class="absolute right-4 top-4 text-gray-500 hover:text-purple-600">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                
                <!-- FAQ Section -->
                <div class="card">
                    <h2 class="text-xl font-semibold mb-6 flex items-center">
                        <i data-lucide="help-circle" class="w-5 h-5 mr-2 text-purple-600"></i>
                        Frequently Asked Questions
                    </h2>
                    
                    <div class="space-y-1">
                        <div class="faq-item">
                            <button class="faq-question" onclick="toggleFAQ(this)">
                                <span>How do I upload files?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5"></i>
                            </button>
                            <div class="faq-answer">
                                <p>You can upload files by clicking the "Upload" button on the dashboard or by dragging and dropping files directly into the browser window. DocuFlow supports files up to 10GB in size for premium accounts.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <button class="faq-question" onclick="toggleFAQ(this)">
                                <span>How do I share files with others?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5"></i>
                            </button>
                            <div class="faq-answer">
                                <p>To share files, navigate to the file you want to share, click the "Share" button, and enter the email address of the person you want to share with. You can set permissions (view or edit) and add an optional message. Alternatively, you can generate a public link that anyone can access.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <button class="faq-question" onclick="toggleFAQ(this)">
                                <span>How do I recover deleted files?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5"></i>
                            </button>
                            <div class="faq-answer">
                                <p>Deleted files are moved to the Trash. To recover them, go to the Trash section, select the files you want to restore, and click the "Restore" button. Files remain in the trash for 30 days (or according to your settings) before being permanently deleted.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <button class="faq-question" onclick="toggleFAQ(this)">
                                <span>How do I organize my files?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5"></i>
                            </button>
                            <div class="faq-answer">
                                <p>You can organize your files by creating folders and subfolders. To create a folder, click the "New Folder" button on the dashboard. You can drag and drop files into folders, and you can star important files and folders for quick access.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <button class="faq-question" onclick="toggleFAQ(this)">
                                <span>How secure are my files?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5"></i>
                            </button>
                            <div class="faq-answer">
                                <p>DocuFlow uses industry-standard encryption to protect your files both in transit and at rest. Your files are stored in secure data centers with multiple layers of protection. We also offer two-factor authentication for additional account security.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <button class="faq-question" onclick="toggleFAQ(this)">
                                <span>How do I upgrade my account?</span>
                                <i data-lucide="chevron-down" class="w-5 h-5"></i>
                            </button>
                            <div class="faq-answer">
                                <p>To upgrade your account, go to your Account Settings and click on the "Upgrade Account" button. You can choose from different plans based on your storage needs and additional features. Payment can be made via credit card or PayPal.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Support -->
                <div class="card">
                    <h2 class="text-xl font-semibold mb-6 flex items-center">
                        <i data-lucide="message-circle" class="w-5 h-5 mr-2 text-purple-600"></i>
                        Contact Support
                    </h2>
                    
                    <form action="#" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="subject">
                                Subject
                            </label>
                            <input class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition duration-200" id="subject" type="text" name="subject" required>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="message">
                                Message
                            </label>
                            <textarea class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition duration-200" id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="attachment">
                                Attachment (Optional)
                            </label>
                            <input class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition duration-200" id="attachment" type="file" name="attachment">
                            <p class="text-xs text-gray-500 mt-1">Max file size: 10MB</p>
                        </div>
                        
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded transition duration-200">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Links -->
                <div class="card">
                    <h2 class="font-semibold mb-4 flex items-center">
                        <i data-lucide="link" class="w-5 h-5 mr-2 text-purple-600"></i>
                        Quick Links
                    </h2>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('user.settings') }}" class="flex items-center text-gray-700 hover:text-purple-600 transition duration-200">
                                <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                                Account Settings
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('plans') }}" class="flex items-center text-gray-700 hover:text-purple-600 transition duration-200">
                                <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                                Billing & Plans
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="card">
                    <h2 class="font-semibold mb-4 flex items-center">
                        <i data-lucide="phone" class="w-5 h-5 mr-2 text-purple-600"></i>
                        Contact Information
                    </h2>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i data-lucide="mail" class="w-5 h-5 text-gray-500 mr-2 flex-shrink-0"></i>
                            <div>
                                <p class="font-medium">Email Support</p>
                                <p class="text-gray-600 text-sm">support@docuflow.com</p>
                                <p class="text-gray-500 text-xs">Response within 24 hours</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="phone" class="w-5 h-5 text-gray-500 mr-2 flex-shrink-0"></i>
                            <div>
                                <p class="font-medium">Phone Support</p>
                                <p class="text-gray-600 text-sm">Premium users only</p>
                                <p class="text-gray-500 text-xs">Mon-Fri, 9am-5pm EST</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
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
                    <a href="#" class="text-gray-400 hover:text-white transition duration-200">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition duration-200">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition duration-200">Contact Us</a>
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

        // FAQ toggle
        function toggleFAQ(button) {
            const answer = button.nextElementSibling;
            const icon = button.querySelector('[data-lucide="chevron-down"]');
            
            if (answer.classList.contains('active')) {
                answer.classList.remove('active');
                icon.style.transform = 'rotate(0deg)';
            } else {
                answer.classList.add('active');
                icon.style.transform = 'rotate(180deg)';
            }
        }
        
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
