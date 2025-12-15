<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleFormPermission extends Model
{
    use HasFactory;

    // Permission type constants
    public const VIEW = 1;
    public const ADD_EDIT_UPDATE = 2;
    public const FULL_ACCESS = 3;

    protected $fillable = [
        'role_id',
        'form_id',
        'permission_type',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}


