<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use HasFactory;

    protected $table = 'docuflow_folders';

    protected $fillable = [
        'name',
        'user_id',
        'parent_id',
        'is_starred',
        'is_trashed',
    ];

    protected $casts = [
        'is_starred' => 'boolean',
        'is_trashed' => 'boolean',
    ];

    /**
     * Get the owner of the folder.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the parent folder.
     */
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Get child folders.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /**
     * Get files in this folder.
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'folder_id');
    }

    /**
     * Scope for active (non-trashed) folders.
     */
    public function scopeActive($query)
    {
        return $query->where('is_trashed', false);
    }

    /**
     * Scope for starred folders.
     */
    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    /**
     * Soft delete the folder and its contents.
     */
    public function trash(): bool
    {
        \DB::transaction(function () {
            // Trash all child folders
            $this->children()->update(['is_trashed' => true]);
            
            // Trash all files in this folder
            $this->files()->update(['is_trashed' => true]);
            
            // Trash the folder itself
            $this->is_trashed = true;
            $this->save();
        });

        return true;
    }

    /**
     * Restore the folder and its contents.
     */
    public function restore(): bool
    {
        \DB::transaction(function () {
            // Restore all child folders
            $this->children()->update(['is_trashed' => false]);
            
            // Restore all files in this folder
            $this->files()->update(['is_trashed' => false]);
            
            // Restore the folder itself
            $this->is_trashed = false;
            $this->save();
        });

        return true;
    }

    /**
     * Toggle starred status.
     */
    public function toggleStar(): bool
    {
        $this->is_starred = !$this->is_starred;
        return $this->save();
    }

    /**
     * Calculate total size of all files in folder (including subfolders).
     */
    public function getTotalSizeAttribute(): int
    {
        $totalSize = $this->files()->sum('size');
        
        foreach ($this->children as $child) {
            $totalSize += $child->total_size;
        }
        
        return $totalSize;
    }
}