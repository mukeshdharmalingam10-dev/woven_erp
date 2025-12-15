<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'phone',
        'email',
        'admin_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the admin user of the organization.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get all branches for the organization.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get all users for the organization.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
