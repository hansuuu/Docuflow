<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow - Plans & Pricing</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
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
        .plan-card {
            @apply bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 border-2 border-transparent;
        }
        .plan-card:hover {
            @apply shadow-xl transform -translate-y-1;
        }
        .plan-card.popular {
            @apply border-purple-500;
        }
        .plan-header {
            @apply p-6 border-b border-gray-200;
        }
        .plan-features {
            @apply p-6;
        }
        .plan-price {
            @apply text-4xl font-bold;
        }
        .plan-cta {
            @apply p-6 bg-gray-50;
        }
        .btn-primary {
            @apply bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 w-full text-center block;
        }
        .btn-outline {
            @apply bg-white border-2 border-purple-600 text-purple-600 hover:bg-purple-50 px-6 py-3 rounded-lg font-medium transition duration-200 w-full text-center block;
        }
        .feature-check {
            @apply text-green-500 mr-2;
        }
        .notification-dot {
            @apply absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full;
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
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-gray-800">Plans & Pricing</h1>
            <p class="text-gray-600 mt-2 max-w-2xl mx-auto">Choose the perfect plan for your storage needs. All plans include our core features, security, and reliability.</p>
        </div>

        <!-- Current Plan Banner (if applicable) -->
        @if(auth()->user()->is_premium)
            <div class="bg-purple-100 border border-purple-300 rounded-lg p-4 mb-8 flex items-center justify-between">
                <div class="flex items-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-purple-600 mr-3"></i>
                    <div>
                        <h3 class="font-medium text-purple-800">You're currently on the Premium Plan</h3>
                        <p class="text-purple-700 text-sm">Your next billing date is {{ now()->addMonths(1)->format('F d, Y') }}</p>
                    </div>
                </div>
                <a href="#" class="text-purple-700 hover:text-purple-900 text-sm font-medium">Manage Subscription</a>
            </div>
        @endif

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Free Plan -->
            <div class="plan-card {{ !auth()->user()->is_premium ? 'border-gray-300' : '' }}">
                <div class="plan-header">
                    <h3 class="text-xl font-bold text-gray-800">Free</h3>
                    <p class="text-gray-600 mt-1">Basic storage for personal use</p>
                    <div class="mt-4">
                        <span class="plan-price">P0</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                </div>
                <div class="plan-features">
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>5 GB Storage</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Basic file sharing</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Access on all devices</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Secure cloud storage</span>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                            <span>Advanced sharing controls</span>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                            <span>Priority support</span>
                        </li>
                    </ul>
                </div>
                <div class="plan-cta">
                    @if(!auth()->user()->is_premium)
                        <button disabled class="btn-outline opacity-50 cursor-not-allowed">Current Plan</button>
                    @else
                        <button class="btn-outline">Downgrade</button>
                    @endif
                </div>
            </div>
            
            <!-- Premium Plan -->
            <div class="plan-card popular">
                <div class="absolute top-0 right-0 bg-purple-600 text-white px-3 py-1 text-xs font-bold uppercase">Popular</div>
                <div class="plan-header">
                    <h3 class="text-xl font-bold text-gray-800">Premium</h3>
                    <p class="text-gray-600 mt-1">Advanced storage for professionals</p>
                    <div class="mt-4">
                        <span class="plan-price">₱500</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                </div>
                <div class="plan-features">
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>50 GB Storage</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Advanced file sharing</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Access on all devices</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Secure cloud storage</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Advanced sharing controls</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Priority email support</span>
                        </li>
                    </ul>
                </div>
                <div class="plan-cta">
                    @if(auth()->user()->is_premium)
                        <button disabled class="btn-primary opacity-50 cursor-not-allowed">Current Plan</button>
                    @else
                        <button class="btn-primary">Upgrade Now</button>
                    @endif
                </div>
            </div>
            
            <!-- Business Plan -->
            <div class="plan-card">
                <div class="plan-header">
                    <h3 class="text-xl font-bold text-gray-800">Business</h3>
                    <p class="text-gray-600 mt-1">Enterprise-grade solution</p>
                    <div class="mt-4">
                        <span class="plan-price">₱1,500</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                </div>
                <div class="plan-features">
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>100 GB Storage</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Enterprise file sharing</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Access on all devices</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Enhanced security features</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>Advanced admin controls</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="check" class="w-5 h-5 feature-check"></i>
                            <span>24/7 priority support</span>
                        </li>
                    </ul>
                </div>
                <div class="plan-cta">
                    <button class="btn-primary">Upgrade Now</button>
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-8">Frequently Asked Questions</h2>
            
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-lg text-gray-800">What happens if I exceed my storage limit?</h3>
                    <p class="mt-2 text-gray-600">You'll receive a notification when you're approaching your storage limit. Once you reach the limit, you won't be able to upload new files until you free up space or upgrade your plan.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-lg text-gray-800">Can I change plans at any time?</h3>
                    <p class="mt-2 text-gray-600">Yes, you can upgrade or downgrade your plan at any time. When upgrading, the change takes effect immediately. When downgrading, the change will take effect at the end of your current billing cycle.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-lg text-gray-800">Is there a discount for annual billing?</h3>
                    <p class="mt-2 text-gray-600">Yes, we offer a 20% discount when you choose annual billing for any of our paid plans. This option is available during checkout.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-lg text-gray-800">What payment methods do you accept?</h3>
                    <p class="mt-2 text-gray-600">We accept all major credit cards (Visa, Mastercard, ) and PayPal. For Business plans, we also offer invoice payment options.</p>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="mt-16 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-8 text-white text-center">
            <h2 class="text-2xl font-bold">Need a custom solution?</h2>
            <p class="mt-2 max-w-2xl mx-auto">Contact our sales team for custom enterprise solutions with dedicated support, custom integrations, and tailored security features.</p>
            <button class="mt-6 bg-white text-purple-600 hover:bg-gray-100 px-6 py-3 rounded-lg font-medium transition duration-200">Contact Sales</button>
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
