<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoragePlan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'docuflow_storage_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'storage_gb',
        'price',
        'features',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'storage_gb' => 'float',
        'price' => 'float',
        'features' => 'array',
    ];

    /**
     * Get the users with this storage plan.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'storage_plan_id');
    }
}