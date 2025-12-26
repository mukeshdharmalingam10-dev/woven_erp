<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'credit_note_number',
        'credit_note_date',
        'reference_document_type',
        'reference_document_number',
        'reference_document_id',
        'party_type',
        'party_id',
        'party_name',
        'gst_number',
        'currency',
        'gst_classification',
        'gst_percentage',
        'credit_note_reason',
        'remarks',
        'subtotal',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'adjustments',
        'total_credit_amount',
        'status',
        'submitted_by',
        'submitted_at',
        'cancel_reason',
        'organization_id',
        'branch_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'credit_note_date' => 'date',
        'gst_percentage' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'adjustments' => 'decimal:2',
        'total_credit_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(CreditNoteItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'party_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'party_id');
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

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function isDraft()
    {
        return $this->status === 'Draft';
    }

    public function isSubmitted()
    {
        return $this->status === 'Submitted';
    }

    public function isCancelled()
    {
        return $this->status === 'Cancelled';
    }
}
