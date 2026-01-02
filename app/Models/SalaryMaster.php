<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'salary_month',
        'monthly_salary_amount',
        'advance_applicable',
        'deduction_mode',
        'full_deduction_month',
        'full_deduction_amount',
        'installment_start_month',
        'installment_amount',
        'variable_deduction_amount',
        'days_present',
        'days_in_month',
        'absent_days',
        'per_day_salary',
        'salary_as_per_attendance',
        'deduction_amount',
        'net_salary',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'salary_month' => 'date',
        'full_deduction_month' => 'date',
        'installment_start_month' => 'date',
        'monthly_salary_amount' => 'decimal:2',
        'full_deduction_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'variable_deduction_amount' => 'decimal:2',
        'per_day_salary' => 'decimal:2',
        'salary_as_per_attendance' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'advance_applicable' => 'boolean',
        'days_present' => 'integer',
        'days_in_month' => 'integer',
        'absent_days' => 'integer',
    ];

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
}

