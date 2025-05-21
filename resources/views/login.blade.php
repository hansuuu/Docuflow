<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DocuFlow - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-black text-white py-4 px-6">
            <h2 class="text-2xl font-bold">DocuFlow</h2>
            <p class="text-sm">Secure cloud storage solution</p>
        </div>
        
        <div class="p-6">
            <h3 class="text-xl font-semibold mb-4">Login to your account</h3>
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                        Username
                    </label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600" 
                           id="username" 
                           type="text" 
                           name="username" 
                           value="{{ old('username') }}" 
                           required 
                           autofocus>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600" 
                           id="password" 
                           type="password" 
                           name="password" 
                           required>
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" class="form-checkbox text-purple-600" name="remember">
                            <span class="ml-2 text-gray-700">Remember me</span>
                        </label>
                    </div>
                    
                    <div>
                        <a class="text-sm text-purple-600 hover:text-purple-800" href="#">
                            Forgot password?
                        </a>
                    </div>
                </div>
                
                <div>
                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Login
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-gray-100 py-4 px-6 text-center">
            <p class="text-gray-600">Don't have an account? <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-800 font-semibold">Register</a></p>
        </div>
    </div>
</body>
</html>