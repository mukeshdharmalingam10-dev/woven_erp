<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PettyCash extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daily_expenses';

    protected $fillable = [
        'expense_id',
        'date',
        'expense_category',
        'description',
        'amount',
        'payment_method',
        'paid_to',
        'receipt_path',
        'remarks',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

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
