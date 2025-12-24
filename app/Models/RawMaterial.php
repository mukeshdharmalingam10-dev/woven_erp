<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'raw_material_name',
        'code',
        'unit_of_measure',
        'description',
        'reorder_level',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'reorder_level' => 'decimal:2',
    ];

    /**
     * Get the organization that owns the raw material.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the branch that owns the raw material.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the raw material.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
