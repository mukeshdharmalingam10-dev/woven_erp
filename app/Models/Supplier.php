<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_name',
        'code',
        'contact_name',
        'phone_number',
        'email',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'gst_number',
        'tax_type',
        'bank_name',
        'ifsc_code',
        'account_number',
        'branch_name',
        'organization_id',
        'branch_id',
        'created_by',
    ];


    /**
     * Get the organization that owns the supplier.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the branch that owns the supplier.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the supplier.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

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
        if ($this->postal_code) {
            $address .= ' - ' . $this->postal_code;
        }
        if ($this->country) {
            $address .= ', ' . $this->country;
        }
        return $address;
    }
}
