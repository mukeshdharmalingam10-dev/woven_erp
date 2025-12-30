<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalarySetup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'salary_type',
        'monthly_salary_amount',
        'salary_effective_from',
        'salary_effective_to',
        'status',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'salary_effective_from' => 'date',
        'salary_effective_to' => 'date',
        'monthly_salary_amount' => 'decimal:2',
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
        return $this->hasMany(SalaryProcessing::class, 'employee_id', 'employee_id');
    }
}
