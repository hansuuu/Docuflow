<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\NotificationPreference;
use App\Models\UserActivity;
use Carbon\Carbon;

class UserSettingsController extends Controller
{
    /**
     * Display the user settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('user-settings');
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Update user profile
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->save();

        // Log activity
        UserActivity::create([
            'user_id' => $user->id,
            'type' => 'profile_update',
            'description' => 'Updated profile information',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'icon' => 'user'
        ]);

        return redirect()->route('user.settings')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->password_updated_at = now();
        $user->save();

        // Log activity
        UserActivity::create([
            'user_id' => $user->id,
            'type' => 'password_update',
            'description' => 'Changed account password',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'icon' => 'lock'
        ]);

        return redirect()->route('user.settings')->with('success', 'Password updated successfully!');
    }

    /**
     * Update the user's notification preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        // Get or create notification preferences
        $notificationPreferences = NotificationPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_notifications' => true,
                'security_alerts' => true,
                'marketing_emails' => false,
            ]
        );

        // Update preferences
        $notificationPreferences->email_notifications = $request->has('email_notifications');
        $notificationPreferences->security_alerts = $request->has('security_alerts');
        $notificationPreferences->marketing_emails = $request->has('marketing_emails');
        $notificationPreferences->save();

        // Log activity
        UserActivity::create([
            'user_id' => $user->id,
            'type' => 'notification_update',
            'description' => 'Updated notification preferences',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'icon' => 'bell'
        ]);

        return redirect()->route('user.settings')->with('success', 'Notification preferences updated successfully!');
    }

    /**
     * Download the user's data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadData(Request $request)
    {
        // Log activity
        UserActivity::create([
            'user_id' => Auth::id(),
            'type' => 'data_download',
            'description' => 'Requested data download',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'icon' => 'download'
        ]);

        // In a real application, you would generate a data export file
        // For now, we'll just redirect with a success message
        return redirect()->route('user.settings')->with('success', 'Your data export has been scheduled. You will receive an email when it\'s ready to download.');
    }

    /**
     * Deactivate the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('The password is incorrect.');
                }
            }],
            'reactivation_type' => ['required', 'in:manual,automatic'],
            'reactivation_date' => ['nullable', 'date', 'after:today'],
            'deactivation_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        
        // Set deactivation details
        $user->is_active = false;
        $user->deactivated_at = now();
        $user->deactivation_reason = $request->deactivation_reason;
        
        // Set reactivation details
        if ($request->reactivation_type === 'automatic' && $request->reactivation_date) {
            $user->auto_reactivate_at = Carbon::parse($request->reactivation_date);
        } else {
            $user->auto_reactivate_at = null;
        }
        
        $user->save();

        // Log activity
        UserActivity::create([
            'user_id' => $user->id,
            'type' => 'account_deactivation',
            'description' => 'Deactivated account',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'icon' => 'power'
        ]);

        // Logout the user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your account has been deactivated. We hope to see you again soon!');
    }

    /**
     * Delete the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('The password is incorrect.');
                }
            }],
            'confirm_deletion' => ['required', 'accepted'],
            'deletion_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        
        // In a real application, you would:
        // 1. Delete or anonymize user data
        // 2. Delete files
        // 3. Delete related records
        
        // For now, we'll just mark the account as deleted
        $user->is_deleted = true;
        $user->deleted_at = now();
        $user->deletion_reason = $request->deletion_reason;
        $user->save();

        // Log activity (for admin purposes)
        UserActivity::create([
            'user_id' => $user->id,
            'type' => 'account_deletion',
            'description' => 'Deleted account',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'icon' => 'trash-2'
        ]);

        // Logout the user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Your account has been deleted. We\'re sorry to see you go!');
    }
}
