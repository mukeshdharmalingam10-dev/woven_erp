<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_name',
        'task_description',
        'comments_updates',
        'notification_enabled',
        'notification_time',
        'created_by',
        'organization_id',
        'branch_id',
    ];

    protected $casts = [
        'notification_enabled' => 'boolean',
    ];

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function relatedCustomer()
    {
        return $this->belongsTo(Customer::class, 'related_customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

