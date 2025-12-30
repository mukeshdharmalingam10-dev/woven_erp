<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdvanceDeductionMap extends Model
{
    use HasFactory;

    protected $table = 'salary_advance_deduction_map';

    protected $fillable = [
        'salary_processing_id',
        'advance_id',
        'deducted_amount',
        'created_by',
    ];

    protected $casts = [
        'deducted_amount' => 'decimal:2',
    ];

    // Relationships
    public function salaryProcessing()
    {
        return $this->belongsTo(SalaryProcessing::class, 'salary_processing_id');
    }

    public function advance()
    {
        return $this->belongsTo(SalaryAdvance::class, 'advance_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

