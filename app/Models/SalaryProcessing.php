<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryProcessing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'salary_month',
        'employee_id',
        'monthly_salary_amount',
        'attendance_source_month',
        'total_working_days',
        'present_days',
        'leave_days',
        'leave_days_deductible',
        'per_day_salary',
        'leave_deduction_amount',
        'leave_override_reason',
        'is_leave_overridden',
        'advance_deduction_amount',
        'advance_allocation_mode',
        'net_payable_salary',
        'salary_advance_id',
        'payment_status',
        'paid_date',
        'notes',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'salary_month' => 'date',
        'attendance_source_month' => 'date',
        'paid_date' => 'date',
        'monthly_salary_amount' => 'decimal:2',
        'leave_days_deductible' => 'decimal:2',
        'per_day_salary' => 'decimal:2',
        'leave_deduction_amount' => 'decimal:2',
        'is_leave_overridden' => 'boolean',
        'advance_deduction_amount' => 'decimal:2',
        'net_payable_salary' => 'decimal:2',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryAdvance()
    {
        return $this->belongsTo(SalaryAdvance::class);
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

    public function advanceDeductionMaps()
    {
        return $this->hasMany(SalaryAdvanceDeductionMap::class, 'salary_processing_id');
    }
}
