<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'purchase_date',
        'delivery_date',
        'gst_percentage_overall',
        'gst_classification',
        'total_raw_material_amount',
        'total_gst_amount',
        'grand_total',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'delivery_date' => 'date',
        'gst_percentage_overall' => 'decimal:2',
        'total_raw_material_amount' => 'decimal:2',
        'total_gst_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
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


