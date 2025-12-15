<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'description',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'pincode',
        'phone',
        'email',
        'admin_id',
        'is_active',
    ];

    /**
     * Get the full address as a formatted string.
     */
    public function getAddressAttribute()
    {
        $address = $this->address_line_1 ?? '';
        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }
        if ($this->city) {
            $address .= ', ' . $this->city;
        }
        if ($this->state) {
            $address .= ', ' . $this->state;
        }
        if ($this->pincode) {
            $address .= ' - ' . $this->pincode;
        }
        return $address;
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the organization that owns the branch.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the admin user of the branch.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get all users for the branch (many-to-many).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_branch');
    }

    /**
     * Get users directly assigned to this branch (legacy single branch).
     */
    public function directUsers()
    {
        return $this->hasMany(User::class, 'branch_id');
    }
}
