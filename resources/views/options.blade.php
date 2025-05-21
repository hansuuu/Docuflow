<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow - Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="flex justify-center items-center space-x-8 py-5 bg-black text-white shadow relative">
        <div class="flex items-center space-x-2 absolute left-4">
            <div class="w-10 h-10 bg-white rounded-full border border-gray-500"></div>
            <span class="font-bold text-xl">DocuFlow</span>
        </div>
        <div class="flex space-x-8">
            <a href="{{ route('dashboard') }}">
                <button><i data-lucide="home" class="w-7 h-7"></i></button>
            </a>
            <a href="{{ route('starred') }}">
                <button><i data-lucide="star" class="w-7 h-7"></i></button>
            </a>
            <a href="{{ route('folders') }}">
                <button><i data-lucide="folder" class="w-7 h-7"></i></button>
            </a>
            <a href="{{ route('share') }}">
                <button><i data-lucide="send" class="w-7 h-7"></i></button>
            </a>
            <a href="{{ route('trash') }}">
                <button><i data-lucide="trash" class="w-7 h-7"></i></button>
            </a>
            <a href="{{ route('file.settings') }}">
                <button class="bg-purple-600 rounded-full p-1">
                    <i data-lucide="settings" class="w-6 h-6"></i>
                </button>
            </a>
            <a href="{{ route('user.settings') }}">
                <button><i data-lucide="user" class="w-7 h-7"></i></button>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto mt-10 px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
            <p class="text-gray-600 mt-2">Configure your DocuFlow experience</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <!-- General Settings -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">General Settings</h2>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="#" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="language">
                                    Language
                                </label>
                                <select id="language" name="language" class="w-full p-3 border rounded-lg">
                                    <option value="en">English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                    <option value="de">German</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="timezone">
                                    Timezone
                                </label>
                                <select id="timezone" name="timezone" class="w-full p-3 border rounded-lg">
                                    <option value="UTC">UTC</option>
                                    <option value="America/New_York">Eastern Time (US & Canada)</option>
                                    <option value="America/Chicago">Central Time (US & Canada)</option>
                                    <option value="America/Denver">Mountain Time (US & Canada)</option>
                                    <option value="America/Los_Angeles">Pacific Time (US & Canada)</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_format">
                                    Date Format
                                </label>
                                <select id="date_format" name="date_format" class="w-full p-3 border rounded-lg">
                                    <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                                    <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                                    <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                </select>
                            </div>
                            
                            <div class="flex items-center">
                                <input id="dark_mode" name="dark_mode" type="checkbox" class="form-checkbox text-purple-600 rounded">
                                <label for="dark_mode" class="ml-2 block text-gray-700 text-sm font-bold">
                                    Enable Dark Mode
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Notification Settings -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Notification Settings</h2>
                    <form action="#" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="email_notifications" name="email_notifications" type="checkbox" class="form-checkbox text-purple-600 rounded" checked>
                                <label for="email_notifications" class="ml-2 block text-gray-700 text-sm font-bold">
                                    Email Notifications
                                </label>
                            </div>
                            <div class="ml-6 text-gray-600 text-sm mb-4">
                                Receive notifications about file shares, comments, and updates
                            </div>
                            
                            <div class="flex items-center">
                                <input id="browser_notifications" name="browser_notifications" type="checkbox" class="form-checkbox text-purple-600 rounded" checked>
                                <label for="browser_notifications" class="ml-2 block text-gray-700 text-sm font-bold">
                                    Browser Notifications
                                </label>
                            </div>
                            <div class="ml-6 text-gray-600 text-sm mb-4">
                                Receive notifications in your browser when you're online
                            </div>
                            
                            <div class="flex items-center">
                                <input id="activity_digest" name="activity_digest" type="checkbox" class="form-checkbox text-purple-600 rounded">
                                <label for="activity_digest" class="ml-2 block text-gray-700 text-sm font-bold">
                                    Weekly Activity Digest
                                </label>
                            </div>
                            <div class="ml-6 text-gray-600 text-sm">
                                Receive a weekly summary of all activity in your account
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save Notification Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Storage Settings -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Storage Settings</h2>
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Storage Usage</h3>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-purple-600 h-2.5 rounded-full" style="width: 25%"></div>
                        </div>
                        <div class="flex justify-between text-sm mt-2">
                            <span>64 GB used</span>
                            <span>256 GB total</span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Storage Breakdown</h3>
                        <ul class="space-y-2">
                            <li class="flex justify-between">
                                <span>Documents</span>
                                <span>2.8 GB</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Images</span>
                                <span>9 GB</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Videos</span>
                                <span>16 GB</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Audio</span>
                                <span>4.4 GB</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Other</span>
                                <span>31.8 GB</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Upgrade Storage Plan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <div class="bg-black text-white p-4 rounded-lg shadow mb-6">
                    <h2 class="font-semibold mb-4">Account Information</h2>
                    <div class="space-y-2">
                        <div>
                            <p class="text-gray-400 text-sm">Account Type</p>
                            <p>Premium</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Billing Cycle</p>
                            <p>Monthly</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Next Billing Date</p>
                            <p>May 15, 2025</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded text-sm">
                            Manage Subscription
                        </button>
                        <button class="w-full bg-gray-700 hover:bg-gray-600 text-white py-2 rounded text-sm">
                            Billing History
                        </button>
                    </div>
                </div>

                <div class="bg-black text-white p-4 rounded-lg shadow">
                    <h2 class="font-semibold mb-4">Security</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Two-Factor Authentication</p>
                            <div class="flex items-center">
                                <span class="text-red-500 mr-2">Disabled</span>
                                <button class="text-sm bg-purple-600 hover:bg-purple-700 text-white px-2 py-1 rounded">
                                    Enable
                                </button>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Password</p>
                            <div class="flex items-center">
                                <span class="text-green-500 mr-2">Strong</span>
                                <a href="{{ route('user.settings') }}#password" class="text-sm text-purple-400 hover:text-purple-300">
                                    Change
                                </a>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Login History</p>
                            <a href="#" class="text-sm text-purple-400 hover:text-purple-300">
                                View Recent Logins
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>