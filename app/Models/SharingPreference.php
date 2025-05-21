<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharingPreference extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sharing_preferences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'default_permission',
        'notify_on_access',
        'password_protect_by_default',
        'default_expiration',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'notify_on_access' => 'boolean',
        'password_protect_by_default' => 'boolean',
    ];

    /**
     * Get the user that owns the sharing preferences.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
