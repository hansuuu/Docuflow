<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocuFlow - Cloud Storage</title>
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
            @auth
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
                    <button><i data-lucide="settings" class="w-7 h-7"></i></button>
                </a>
                <a href="{{ route('user.settings') }}">
                    <button><i data-lucide="user" class="w-7 h-7"></i></button>
                </a>
            @else
                <a href="{{ route('login') }}" class="text-white hover:text-purple-300">Login</a>
                <a href="{{ route('register') }}" class="text-white hover:text-purple-300">Register</a>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto mt-10 px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to DocuFlow</h1>
            <p class="text-xl text-gray-600">Your secure cloud storage solution</p>
        </div>

        @auth
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-semibold mb-6">Upload Files</h2>
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data" class="mb-6">
                    @csrf
                    <div class="mb-4">
                        <label for="folder" class="block text-gray-700 text-sm font-bold mb-2">
                            Select Folder (Optional)
                        </label>
                        <select id="folder" name="folder_id" class="w-full p-3 border rounded-lg">
                            <option value="">Root Directory</option>
                            @foreach(auth()->user()->folders()->notTrashed()->get() as $folder)
                                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full h-40 flex items-center justify-center bg-purple-50 border-2 border-dashed rounded-xl text-gray-600 mb-4 relative">
                        <input type="file" name="files[]" id="file-upload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" multiple>
                        <div class="text-center">
                            <i data-lucide="upload-cloud" class="w-12 h-12 mx-auto mb-2 text-purple-500"></i>
                            <p>Drag and drop files, or <span class="text-purple-600 font-medium">Browse</span></p>
                            <p class="text-sm text-gray-500 mt-1">Upload multiple files up to 100MB each</p>
                        </div>
                    </div>

                    <div id="file-list" class="mb-4"></div>

                    <div class="flex justify-between items-center">
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                            Upload Files
                        </button>
                        <div class="text-sm text-gray-500">
                            <span id="selected-count">0</span> files selected
                        </div>
                    </div>
                </form>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Recently Uploaded Files</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200 text-left">
                                <tr>
                                    <th class="px-4 py-2">Name</th>
                                    <th class="px-4 py-2">Size</th>
                                    <th class="px-4 py-2">Uploaded</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(auth()->user()->files()->latest()->take(5)->get() as $file)
                                    <tr>
                                        <td class="px-4 py-2 border-t">{{ $file->name }}</td>
                                        <td class="px-4 py-2 border-t">{{ $file->human_readable_size }}</td>
                                        <td class="px-4 py-2 border-t">{{ $file->created_at->diffForHumans() }}</td>
                                        <td class="px-4 py-2 border-t">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('files.download', $file->id) }}" class="text-blue-500">
                                                    <i data-lucide="download" class="w-5 h-5"></i>
                                                </a>
                                                <a href="{{ route('files.preview', $file->id) }}" class="text-green-500">
                                                    <i data-lucide="eye" class="w-5 h-5"></i>
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
                                        <td colspan="4" class="px-4 py-2 border-t text-center text-gray-500">
                                            No files uploaded yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('folders') }}" class="text-purple-600 hover:text-purple-800">
                            View All Files
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left side: Login Form -->
                    <div class="border-r pr-8">
                        <h2 class="text-2xl font-semibold mb-6 text-center">Login to Your Account</h2>
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('username')
                                    <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-6">
                                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                                <input id="password" type="password" name="password" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
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
        @endauth

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