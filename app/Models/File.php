<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'docuflow_files';

    protected $fillable = [
        'name',
        'path',
        'mime_type',
        'size',
        'user_id',
        'folder_id',
        'is_starred',
        'is_trashed',
    ];

    protected $casts = [
        'is_starred' => 'boolean',
        'is_trashed' => 'boolean',
        'size' => 'integer',
    ];

    /**
     * Get the user that owns the file.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the folder that contains the file.
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    /**
     * Get human readable file size.
     */
    public function getHumanReadableSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->size;
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope for non-trashed files.
     */
    public function scopeActive($query)
    {
        return $query->where('is_trashed', false);
    }

    /**
     * Scope for starred files.
     */
    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    /**
     * Soft delete the file.
     */
    public function trash(): bool
    {
        $this->is_trashed = true;
        return $this->save();
    }

    /**
     * Restore the file.
     */
    public function restore(): bool
    {
        $this->is_trashed = false;
        return $this->save();
    }

    /**
     * Toggle starred status.
     */
    public function toggleStar(): bool
    {
        $this->is_starred = !$this->is_starred;
        return $this->save();
    }
}