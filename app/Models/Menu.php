<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    public function submenus()
    {
        return $this->hasMany(Submenu::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }
}


