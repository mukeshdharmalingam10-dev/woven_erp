<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'customer_id',
        'mode_of_order',
        'buyer_order_number',
        'billing_address',
        'shipping_address',
        'gst_percentage_overall',
        'gst_classification',
        'total_sales_amount',
        'total_gst_amount',
        'grand_total',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'gst_percentage_overall' => 'decimal:2',
        'total_sales_amount' => 'decimal:2',
        'total_gst_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

