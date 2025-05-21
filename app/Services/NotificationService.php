<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a welcome notification for a new user.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\Notification
     */
    public static function createWelcomeNotification(User $user)
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => 'welcome',
            'message' => 'Welcome to DocuFlow! Thank you for creating an account.',
            'data' => [
                'action_url' => route('dashboard'),
                'action_text' => 'Get Started',
            ],
        ]);
    }

    /**
     * Create a file shared notification.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $sharedBy
     * @param  mixed  $file
     * @return \App\Models\Notification
     */
    public static function createFileSharedNotification(User $user, User $sharedBy, $file)
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => 'file_shared',
            'message' => "{$sharedBy->name} shared a file with you: {$file->name}",
            'data' => [
                'file_id' => $file->id,
                'shared_by' => $sharedBy->id,
                'action_url' => route('files.show', $file->id),
                'action_text' => 'View File',
            ],
        ]);
    }

    /**
     * Create a folder shared notification.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $sharedBy
     * @param  mixed  $folder
     * @return \App\Models\Notification
     */
    public static function createFolderSharedNotification(User $user, User $sharedBy, $folder)
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => 'folder_shared',
            'message' => "{$sharedBy->name} shared a folder with you: {$folder->name}",
            'data' => [
                'folder_id' => $folder->id,
                'shared_by' => $sharedBy->id,
                'action_url' => route('folders.show', $folder->id),
                'action_text' => 'View Folder',
            ],
        ]);
    }

    /**
     * Create a file uploaded notification.
     *
     * @param  \App\Models\User  $user
     * @param  mixed  $file
     * @return \App\Models\Notification
     */
    public static function createFileUploadedNotification(User $user, $file)
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => 'file_uploaded',
            'message' => "File uploaded successfully: {$file->name}",
            'data' => [
                'file_id' => $file->id,
                'action_url' => route('files.show', $file->id),
                'action_text' => 'View File',
            ],
        ]);
    }

    /**
     * Create a storage limit warning notification.
     *
     * @param  \App\Models\User  $user
     * @param  float  $percentageUsed
     * @return \App\Models\Notification
     */
    public static function createStorageLimitWarningNotification(User $user, $percentageUsed)
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => 'storage_warning',
            'message' => "Your storage is {$percentageUsed}% full. Consider upgrading your plan or freeing up space.",
            'data' => [
                'percentage_used' => $percentageUsed,
                'action_url' => route('plans'),
                'action_text' => 'Upgrade Plan',
            ],
        ]);
    }
}