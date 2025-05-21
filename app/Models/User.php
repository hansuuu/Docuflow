<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'docuflow_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'total_storage',
        'used_storage',
        'used_storage_percentage',
        'is_active',
        'is_deleted',
        'is_premium',
        'two_factor_enabled',
        'password_updated_at',
        'deactivated_at',
        'auto_reactivate_at',
        'deactivation_reason',
        'deleted_at',
        'deletion_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'total_storage' => 'float',
        'used_storage' => 'float',
        'used_storage_percentage' => 'float',
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
        'is_premium' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'password_updated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'auto_reactivate_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            // Set default values for new users
            $user->total_storage = $user->total_storage ?? 5; // 5GB default
            $user->used_storage = $user->used_storage ?? 0;
            $user->used_storage_percentage = $user->used_storage_percentage ?? 0;
            $user->is_active = $user->is_active ?? true;
            $user->is_deleted = $user->is_deleted ?? false;
            $user->is_premium = $user->is_premium ?? false;
            $user->two_factor_enabled = $user->two_factor_enabled ?? false;
        });
    }
    
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'name';
    }

    /**
     * Get the folders for the user.
     */
    public function folders()
    {
        return $this->hasMany(Folder::class, 'user_id');   
    }

    /**
     * Get the root folders for the user.
     */
    public function rootFolders()
    {
        return $this->folders()->whereNull('parent_id');
    }

    /**
     * Get the files for the user.
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Get the file shares for the user.
     */
    public function fileShares()
    {
        return $this->hasMany(FileShare::class);
    }

    /**
     * Get the starred folders for the user.
     */
    public function starredFolders()
    {
        return $this->hasMany(Folder::class)->where('is_starred', true);
    }

    /**
     * Get the starred files for the user.
     */
    public function starredFiles()
    {
        return $this->files()->where('is_starred', true);
    }

    /**
     * Get the trashed folders for the user.
     */
    public function trashedFolders()
    {
        return $this->folders()->where('is_trashed', true);
    }

    /**
     * Get the active (non-trashed) folders for the user.
     */
    public function activeFolders()
    {
        return $this->folders()->where('is_trashed', false);
    }
    
    /**
     * Get the trashed files for the user.
     */
    public function trashedFiles()
    {
        return $this->files()->where('is_trashed', true);
    }

    /**
     * Get the user's notification preferences.
     */
    public function notificationPreferences()
    {
        return $this->hasOne(NotificationPreference::class, 'user_id');
    }

    /**
     * Get the user's sharing preferences.
     */
    public function sharingPreferences()
    {
        return $this->hasOne(SharingPreference::class, 'user_id');
    }

    /**
     * Get the user's activities.
     */
    public function activities()
    {
        return $this->hasMany(UserActivity::class, 'user_id');
    }

    /**
     * Get the user's recent activities.
     */
    public function recentActivities()
    {
        return $this->activities()->orderBy('created_at', 'desc');
    }

    /**
     * Check if the user account is deactivated.
     *
     * @return bool
     */
    public function isDeactivated()
    {
        return !$this->is_active && $this->deactivated_at !== null;
    }

    /**
     * Check if the user account should be auto-reactivated.
     *
     * @return bool
     */
    public function shouldAutoReactivate()
    {
        return $this->isDeactivated() && 
               $this->auto_reactivate_at !== null && 
               $this->auto_reactivate_at <= Carbon::now();
    }

    /**
     * Reactivate the user account.
     *
     * @return void
     */
    public function reactivate()
    {
        $this->is_active = true;
        $this->deactivated_at = null;
        $this->auto_reactivate_at = null;
        $this->deactivation_reason = null;
        $this->save();
    }

    /**
     * Update the user's storage usage.
     *
     * @param int|null $size Size in bytes to add (positive) or remove (negative)
     * @return void
     */
    public function updateStorageUsage($size = null)
    {
        if ($size !== null) {
            // If size is provided, add/subtract it from used_storage
            // Convert total_storage from GB to bytes for calculation
            $totalStorageBytes = $this->total_storage * 1024 * 1024 * 1024;
            
            // Update used storage in bytes
            $this->used_storage += $size / (1024 * 1024 * 1024); // Convert bytes to GB
            
            // Calculate percentage
            $this->used_storage_percentage = ($this->used_storage * 1024 * 1024 * 1024) / $totalStorageBytes * 100;
        } else {
            // If no size is provided, recalculate based on all files
            $totalBytes = $this->files()->sum('size');
            
            // Convert to GB
            $this->used_storage = $totalBytes / (1024 * 1024 * 1024);
            
            // Calculate percentage
            $totalStorageBytes = $this->total_storage * 1024 * 1024 * 1024;
            $this->used_storage_percentage = ($totalBytes / $totalStorageBytes) * 100;
        }
        
        // Ensure values don't go below zero
        if ($this->used_storage < 0) {
            $this->used_storage = 0;
        }
        
        if ($this->used_storage_percentage < 0) {
            $this->used_storage_percentage = 0;
        }
        
        $this->save();
    }

    /**
     * Get the user's storage usage in a human-readable format.
     *
     * @return string
     */
    public function getFormattedUsedStorage()
    {
        $usedGB = $this->used_storage;
        
        if ($usedGB < 0.001) {
            return round($usedGB * 1024 * 1024, 2) . ' KB';
        } elseif ($usedGB < 1) {
            return round($usedGB * 1024, 2) . ' MB';
        } else {
            return round($usedGB, 2) . ' GB';
        }
    }

    /**
     * Get the user's total storage in a human-readable format.
     *
     * @return string
     */
    public function getFormattedTotalStorage()
    {
        return round($this->total_storage, 2) . ' GB';
    }

    /**
     * Check if the user has enough storage space for a file of the given size.
     *
     * @param int $sizeInBytes Size of the file in bytes
     * @return bool
     */
    public function hasEnoughStorage($sizeInBytes)
    {
        $sizeInGB = $sizeInBytes / (1024 * 1024 * 1024);
        return ($this->used_storage + $sizeInGB) <= $this->total_storage;
    }

    /**
     * Get the remaining storage space in GB.
     *
     * @return float
     */
    public function getRemainingStorage()
    {
        return $this->total_storage - $this->used_storage;
    }

    /**
     * Get the remaining storage space in a human-readable format.
     *
     * @return string
     */
    public function getFormattedRemainingStorage()
    {
        $remainingGB = $this->getRemainingStorage();
        
        if ($remainingGB < 0.001) {
            return round($remainingGB * 1024 * 1024, 2) . ' KB';
        } elseif ($remainingGB < 1) {
            return round($remainingGB * 1024, 2) . ' MB';
        } else {
            return round($remainingGB, 2) . ' GB';
        }
    }

    /**
     * Get the user's storage usage percentage as a formatted string.
     *
     * @return string
     */
    public function getFormattedStoragePercentage()
    {
        return round($this->used_storage_percentage, 1) . '%';
    }

    /**
     * Get the user's storage usage color based on percentage.
     * Returns a CSS color class.
     *
     * @return string
     */
    public function getStorageColorClass()
    {
        if ($this->used_storage_percentage < 50) {
            return 'bg-green-500';
        } elseif ($this->used_storage_percentage < 80) {
            return 'bg-yellow-500';
        } else {
            return 'bg-red-500';
        }
    }

    /**
     * Get the user's MinIO folder path.
     *
     * @return string
     */
    public function getMinioFolderPath()
    {
        return "docuflow_users/{$this->name}";
    }

    /**
     * Get the user's MinIO files path.
     *
     * @return string
     */
    public function getMinioFilesPath()
    {
        return "docuflow_users/{$this->name}/files";
    }
    
    /**
     * Get the user's MinIO documents path.
     *
     * @return string
     */
    public function getMinioDocumentsPath()
    {
        return "docuflow_users/{$this->name}/documents";
    }
    
    /**
     * Get the user's MinIO images path.
     *
     * @return string
     */
    public function getMinioImagesPath()
    {
        return "docuflow_users/{$this->name}/images";
    }

    /**
     * Log user activity.
     *
     * @param string $type Activity type
     * @param string $description Activity description
     * @param string|null $icon Icon name (Lucide icon)
     * @return UserActivity
     */
    public function logActivity($type, $description, $icon = null)
    {
        return $this->activities()->create([
            'type' => $type,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'icon' => $icon ?? 'activity'
        ]);
    }

    /**
     * Check if the user's password needs to be updated (older than 90 days).
     *
     * @return bool
     */
    public function passwordNeedsUpdate()
    {
        if (!$this->password_updated_at) {
            return true;
        }

        return $this->password_updated_at->diffInDays(now()) >= 90;
    }

    /**
     * Get the days since the password was last updated.
     *
     * @return int
     */
    public function daysSincePasswordUpdate()
    {
        if (!$this->password_updated_at) {
            return 999; // A large number to indicate it's never been updated
        }

        return $this->password_updated_at->diffInDays(now());
    }

    

        // Add this to the User model
    /**
    * Get the user's notifications.
    */
    public function notifications()
    {
    return $this->hasMany(Notification::class);
    }

    /**
    * Get the user's unread notifications.
    */
    public function unreadNotifications()
    {
    return $this->notifications()->where('is_read', false);
    }
}
