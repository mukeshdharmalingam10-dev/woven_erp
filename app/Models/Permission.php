<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['form_name', 'name', 'slug', 'description', 'module', 'action', 'is_active'];

    /**
     * Accessor for form_name - use form_name if exists, otherwise use name
     */
    public function getFormNameAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }
        // Fallback to name if form_name doesn't exist
        return $this->attributes['name'] ?? null;
    }

    /**
     * Roles that have this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission')
                    ->withPivot('read', 'write', 'delete')
                    ->withTimestamps();
    }
}
