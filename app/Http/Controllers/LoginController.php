<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Convert username field to name for authentication
        $authCredentials = [
            'name' => $credentials['username'],
            'password' => $credentials['password'],
        ];

        Log::info('Login attempt', ['username' => $credentials['username']]);

        if (Auth::attempt($authCredentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            Log::info('Login successful', ['user_id' => Auth::id(), 'username' => Auth::user()->name]);
            
            return redirect()->intended(route('dashboard'));
        }

        Log::warning('Login failed', ['username' => $credentials['username']]);

        return back()
            ->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])
            ->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}