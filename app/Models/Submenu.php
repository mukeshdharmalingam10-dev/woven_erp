<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'name',
        'code',
        'is_active',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }
}


