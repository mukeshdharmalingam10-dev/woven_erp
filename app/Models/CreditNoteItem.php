<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_note_id',
        'product_id',
        'item_name',
        'description',
        'quantity',
        'unit_of_measure',
        'rate',
        'amount',
        'cgst_percentage',
        'cgst_amount',
        'sgst_percentage',
        'sgst_amount',
        'igst_percentage',
        'igst_amount',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'cgst_percentage' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_percentage' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_percentage' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
