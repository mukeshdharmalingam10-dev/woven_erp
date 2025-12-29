<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'quotation_date',
        'customer_id',
        'contact_person_name',
        'contact_number',
        'postal_code',
        'company_name',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'validity',
        'payment_terms',
        'inspection',
        'taxes',
        'freight',
        'special_condition',
        'total_amount',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the quotation.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the organization that owns the quotation.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the branch that owns the quotation.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the quotation.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the items for the quotation.
     */
    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }
}

