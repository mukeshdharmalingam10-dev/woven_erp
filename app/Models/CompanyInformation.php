<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'company_name',
        'logo_path',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'pincode',
        'gstin',
        'email',
        'phone',
    ];

    /**
     * Get the branch that owns the company information.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the full address as a formatted string.
     */
    public function getFullAddressAttribute()
    {
        $address = $this->address_line_1;
        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }
        $address .= ', ' . $this->city;
        $address .= ', ' . $this->state;
        $address .= ' - ' . $this->pincode;
        return $address;
    }
}
