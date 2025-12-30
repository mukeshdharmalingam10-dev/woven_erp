<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryAdvance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'advance_reference_no',
        'employee_id',
        'advance_date',
        'advance_amount',
        'advance_deduction_mode',
        'installment_start_month',
        'installment_amount',
        'remarks',
        'total_deducted_amount',
        'advance_balance_amount',
        'status',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'advance_date' => 'date',
        'installment_start_month' => 'date',
        'advance_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'total_deducted_amount' => 'decimal:2',
        'advance_balance_amount' => 'decimal:2',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
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

    public function salaryProcessings()
    {
        return $this->hasMany(SalaryProcessing::class);
    }

    public function deductionMaps()
    {
        return $this->hasMany(SalaryAdvanceDeductionMap::class, 'advance_id');
    }

    /**
     * Boot method to generate advance reference number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($salaryAdvance) {
            if (empty($salaryAdvance->advance_reference_no)) {
                $salaryAdvance->advance_reference_no = static::generateAdvanceReferenceNo();
            }
        });
    }

    /**
     * Generate unique advance reference number in format ADV-YYYYMM-0001
     */
    protected static function generateAdvanceReferenceNo()
    {
        $yearMonth = date('Ym'); // YYYYMM format
        $prefix = 'ADV-' . $yearMonth . '-';
        
        // Find the last advance for this month
        $lastAdvance = static::withTrashed()
            ->where('advance_reference_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastAdvance) {
            // Extract the number part after the last dash
            $parts = explode('-', $lastAdvance->advance_reference_no);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
