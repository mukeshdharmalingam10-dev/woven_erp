<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Permissions associated with the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
                    ->withPivot('read', 'write', 'delete')
                    ->withTimestamps();
    }

    /**
     * Users that have this role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }
}
