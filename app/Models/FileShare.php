<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileShare extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'docuflow_file_shares';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_id',
        'user_id',
        'shared_with',
        'permission',
        'token',
        'password',
        'expires_at',
        'is_public',
        'message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    /**
     * Get the file that is shared.
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Get the user who shared the file.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the share has expired.
     *
     * @return bool
     */
    public function hasExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the share is password protected.
     *
     * @return bool
     */
    public function isPasswordProtected()
    {
        return !is_null($this->password);
    }
}