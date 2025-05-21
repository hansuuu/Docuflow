<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if account is deleted
            if ($user->is_deleted) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'This account has been deleted.');
            }
            
            // Check if account is deactivated
            if (!$user->is_active && $user->deactivated_at) {
                // Check if account should be auto-reactivated
                if ($user->auto_reactivate_at && $user->auto_reactivate_at <= Carbon::now()) {
                    $user->reactivate();
                    $user->logActivity('account_reactivation', 'Account automatically reactivated', 'refresh-cw');
                    return $next($request);
                }
                
                // If this is a login attempt, reactivate the account
                if ($request->route()->getName() === 'login') {
                    $user->reactivate();
                    $user->logActivity('account_reactivation', 'Account reactivated by login', 'refresh-cw');
                    return $next($request);
                }
                
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Your account is deactivated. Please log in again to reactivate it.');
            }
        }
        
        return $next($request);
    }
}
