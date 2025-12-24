<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'work_order_number',
        'customer_id',
        'product_id',
        'quantity_to_produce',
        'per_kg_weight',
        'work_order_date',
        'status',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'work_order_date' => 'date',
        'quantity_to_produce' => 'decimal:3',
        'per_kg_weight' => 'decimal:3',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_COMPLETED = 'completed';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function materials()
    {
        return $this->hasMany(WorkOrderMaterial::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
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


