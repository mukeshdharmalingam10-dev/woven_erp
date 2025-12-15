<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'role_id', 'entity_id',
        'organization_id', 'branch_id', 'status', 'last_login_at', 'created_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function role() { return $this->belongsTo(Role::class); }
    public function roles() { return $this->belongsToMany(Role::class, 'user_role')->withTimestamps(); }
    public function entity() { return $this->belongsTo(Entity::class); }
    public function organization() { return $this->belongsTo(Organization::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function branches() { return $this->belongsToMany(Branch::class, 'user_branch'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function isSuperAdmin(): bool { return $this->role && $this->role->slug === 'super-admin'; }
    public function isAdmin(): bool { return $this->role && $this->role->slug === 'admin'; }
    public function isBranchUser(): bool { return $this->role && $this->role->slug === 'branch-user'; }

    public function hasAccessToBranch(int $branchId): bool {
        if ($this->isSuperAdmin() || $this->isAdmin()) return true;
        return $this->branches()->where('branches.id', $branchId)->exists();
    }

    public function isActive(): bool {
        if (!isset($this->status) || $this->status === null) return true;
        return $this->status === 'active';
    }

    public function isLocked(): bool { return $this->status === 'locked'; }
    public function updateLastLogin(): void { $this->update(['last_login_at' => now()]); }

    public function hasPermission(string $form, string $type = 'read'): bool {
        if ($this->isSuperAdmin() || $this->isAdmin()) return true;
        $typeMap = ['view' => 'read', 'read' => 'read', 'create' => 'write', 'edit' => 'write', 'write' => 'write', 'delete' => 'delete', 'destroy' => 'delete'];
        $checkColumn = $typeMap[strtolower($type)] ?? 'read';
        $this->loadMissing(['roles.permissions']);
        foreach ($this->roles as $role) {
            $permission = $role->permissions->firstWhere('form_name', $form);
            if ($permission) {
                $pivot = $permission->pivot;
                $hasPermission = false;
                switch ($checkColumn) {
                    case 'read': $hasPermission = ($pivot->read ?? false) || ($pivot->write ?? false) || ($pivot->delete ?? false); break;
                    case 'write': $hasPermission = ($pivot->write ?? false) || ($pivot->delete ?? false); break;
                    case 'delete': $hasPermission = ($pivot->delete ?? false); break;
                    default: $hasPermission = ($pivot->$checkColumn ?? false); break;
                }
                if ($hasPermission) return true;
            }
        }
        return false;
    }
}
