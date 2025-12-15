<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'submenu_id',
        'name',
        'code',
        'route_name',
        'is_active',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function submenu()
    {
        return $this->belongsTo(Submenu::class);
    }

    public function roleFormPermissions()
    {
        return $this->hasMany(RoleFormPermission::class);
    }
}


