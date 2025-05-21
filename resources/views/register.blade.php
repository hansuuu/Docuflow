<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DocuFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="flex justify-center items-center space-x-8 py-5 bg-black text-white shadow relative">
        <div class="flex items-center space-x-2 absolute left-4">
            <div class="w-10 h-10 bg-white rounded-full border border-gray-500"></div>
            <span class="font-bold text-xl">DocuFlow</span>
        </div>
        <div class="flex space-x-8">
            <a href="{{ route('login') }}" class="text-white hover:text-purple-300">Login</a>
            <a href="{{ route('register') }}" class="text-white hover:text-purple-300">Register</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto mt-10 px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to DocuFlow</h1>
            <p class="text-xl text-gray-600">Your secure cloud storage solution</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left side: Register Form -->
                <div class="border-r pr-8">
                    <h2 class="text-2xl font-semibold mb-6 text-center">Create Your Account</h2>
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <!-- Display validation errors -->
                        @if ($errors->any())
                            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 4px;">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <!-- Display debug info if available -->
                        @if (session('debug_info'))
                            <div style="background-color: #d1ecf1; color: #0c5460; padding: 10px; margin-bottom: 15px; border: 1px solid #bee5eb; border-radius: 4px;">
                                <h4>Debug Information:</h4>
                                <pre>{{ json_encode(session('debug_info'), JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @endif
                        <div class="mb-4">
                            <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                            <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('username')
                                <span class="text-red-500 text-xs italic">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('email')
                                <span class="text-red-500 text-xs italic">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                            <input id="password" type="password" name="password" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('password')
                                <span class="text-red-500 text-xs italic">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        
                        <div class="flex items-center justify-between mb-6">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                                Register
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Right side: Features -->
                <div class="pl-8">
                    <h2 class="text-2xl font-semibold mb-6 text-center">Secure Cloud Storage</h2>
                    <div class="text-center mb-6">
                        <i data-lucide="cloud" class="w-16 h-16 mx-auto mb-4 text-purple-500"></i>
                        <p class="text-gray-600 mb-4">Sign up to start uploading and managing your files in the cloud.</p>
                        <p class="text-gray-600">Get started with free storage and upgrade anytime.</p>
                    </div>
                    <div class="text-center mt-8">
                        <p class="mb-4">Already have an account?</p>
                        <a href="{{ route('login') }}" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded inline-block">
                            Login Now
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
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
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
    </script>
</body>
</html>