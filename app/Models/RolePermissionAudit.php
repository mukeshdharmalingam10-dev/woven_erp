<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermissionAudit extends Model
{
    use HasFactory;

    protected $table = 'role_permission_audit';

    protected $fillable = [
        'role_id',
        'permission_id',
        'changed_by',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'description',
    ];

    /**
     * Get the role that was changed
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the permission that was changed
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Get the user who made the change
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Log a role permission change
     */
    public static function log($action, $roleId = null, $permissionId = null, $fieldName = null, $oldValue = null, $newValue = null, $description = null)
    {
        return self::create([
            'role_id' => $roleId,
            'permission_id' => $permissionId,
            'changed_by' => auth()->id(),
            'action' => $action,
            'field_name' => $fieldName,
            'old_value' => is_array($oldValue) || is_object($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value' => is_array($newValue) || is_object($newValue) ? json_encode($newValue) : $newValue,
            'description' => $description,
        ]);
    }
}
