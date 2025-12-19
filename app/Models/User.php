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

    /**
     * Check if user has permission for a form/action.
     * 
     * Permissions follow a hierarchy:
     * - Read: View only (list and details)
     * - Write: Includes Read + Edit/Add capabilities (no delete)
     * - Delete: Includes Read + Write + Delete (full access)
     * 
     * @param string $form The form name (e.g., 'suppliers', 'products')
     * @param string $type The permission type: 'read', 'write', 'delete', 'view', 'create', 'edit'
     * @return bool
     */
    public function hasPermission(string $form, string $type = 'read'): bool {
        if ($this->isSuperAdmin() || $this->isAdmin()) return true;
        
        $typeMap = ['view' => 'read', 'read' => 'read', 'create' => 'write', 'edit' => 'write', 'write' => 'write', 'delete' => 'delete', 'destroy' => 'delete'];
        $checkColumn = $typeMap[strtolower($type)] ?? 'read';
        
        // Always reload roles with permissions to ensure fresh data with pivot
        // Clear existing relationships and reload to bypass cache
        $this->unsetRelation('roles');
        $this->load(['roles.permissions']);
        
        // Check if user has any roles
        if ($this->roles->isEmpty()) {
            return false;
        }
        
        // Normalize form name for comparison (lowercase, trim, handle spaces/hyphens/underscores)
        $formNormalized = strtolower(trim(str_replace([' ', '_'], '-', $form)));
        
        // Check permissions across all user roles
        foreach ($this->roles as $role) {
            if (!$role->permissions || $role->permissions->isEmpty()) {
                continue;
            }
            
            // Find permission by form_name or name (check both for compatibility)
            foreach ($role->permissions as $perm) {
                // Get form_name - check multiple sources
                $permFormName = null;
                
                // Try getOriginal first (raw database value)
                try {
                    $permFormName = $perm->getOriginal('form_name');
                } catch (\Exception $e) {
                    // If getOriginal fails, try attributes
                    $permFormName = $perm->attributes['form_name'] ?? null;
                }
                
                // Fallback to accessor and name
                if (empty($permFormName)) {
                    $permFormName = $perm->form_name ?? $perm->name ?? null;
                }
                
                if (empty($permFormName)) {
                    continue; // Skip if no form name found
                }
                
                // Normalize permission form name for comparison
                // Handle spaces vs hyphens vs underscores (e.g., "Raw Materials" vs "raw-materials" vs "raw_materials")
                $permFormNameNormalized = strtolower(trim(str_replace([' ', '_'], '-', $permFormName)));
                
                // Check if this permission matches the form we're looking for (case-insensitive)
                if ($permFormNameNormalized === $formNormalized && $perm->pivot) {
                    $pivot = $perm->pivot;
                    
                    // Pivot values are stored as integers (0 or 1) or booleans
                    // Cast to int then to bool for consistency
                    $pivotRead = (bool)(int)($pivot->read ?? 0);
                    $pivotWrite = (bool)(int)($pivot->write ?? 0);
                    $pivotDelete = (bool)(int)($pivot->delete ?? 0);
                    
                    $hasPermission = false;
                    switch ($checkColumn) {
                        // Read: granted if user has read, write, or delete permission
                        case 'read': 
                            $hasPermission = $pivotRead || $pivotWrite || $pivotDelete; 
                            break;
                        // Write: granted if user has write or delete permission (hierarchy: delete includes write)
                        case 'write': 
                            $hasPermission = $pivotWrite || $pivotDelete; 
                            break;
                        // Delete: only granted if user has explicit delete permission
                        case 'delete': 
                            $hasPermission = $pivotDelete; 
                            break;
                        default: 
                            $hasPermission = (bool)(int)($pivot->$checkColumn ?? 0); 
                            break;
                    }
                    
                    if ($hasPermission) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Check if user has read permission (view only).
     * Read permission allows viewing list and details but no modifications.
     *
     * @param string $form Form name
     * @return bool
     */
    public function canRead(string $form): bool {
        return $this->hasPermission($form, 'read');
    }

    /**
     * Check if user has write permission (edit/add).
     * Write permission allows editing existing records or adding new ones, but not deleting.
     *
     * @param string $form Form name
     * @return bool
     */
    public function canWrite(string $form): bool {
        if ($this->isSuperAdmin() || $this->isAdmin()) return true;
        return $this->hasPermission($form, 'write');
    }

    /**
     * Check if user has delete permission (full access).
     * Delete permission allows read, write, and delete operations (full control).
     *
     * @param string $form Form name
     * @return bool
     */
    public function canDelete(string $form): bool {
        if ($this->isSuperAdmin() || $this->isAdmin()) return true;
        return $this->hasPermission($form, 'delete');
    }

    /**
     * Get form name from route name for permission checking.
     * Helper method for views to determine form name from route.
     *
     * @param string $routeName Route name (e.g., 'suppliers.index', 'raw-materials.create')
     * @return string Form name (e.g., 'suppliers', 'raw-materials')
     */
    public function getFormNameFromRoute(string $routeName): string {
        // Remove .index, .create, .edit, .show suffixes to get base form name
        $formName = preg_replace('/\.(index|create|edit|show|store|update|destroy)$/', '', $routeName);
        
        // Handle special route name to form name mappings
        $routeToFormMap = [
            'organizations' => 'organizations',
            'users' => 'users',
            'roles' => 'roles',
            'permissions' => 'permissions',
            'role-permissions' => 'role-permissions',
            'company-information' => 'company-info',
            'raw-materials' => 'raw-materials',
            'purchase-orders' => 'purchase-orders',
            'material-inwards' => 'material-inwards',
            'sales-invoices' => 'sales-invoices',
            'daily-expenses' => 'daily-expenses',
            'stock-transactions' => 'stock-transactions',
            'work-orders' => 'work-orders',
            'productions' => 'productions',
            'notes' => 'notes',
            'tasks' => 'tasks',
            'suppliers' => 'suppliers',
            'products' => 'products',
            'customers' => 'customers',
            'employees' => 'employees',
        ];
        
        if (isset($routeToFormMap[$formName])) {
            $formName = $routeToFormMap[$formName];
        }
        
        return $formName;
    }

    /**
     * Check if user can access a page (based on route name).
     * This checks read permission for page access.
     */
    public function canAccessPageRoute(string $routeName): bool {
        // Convert route name to form name
        $formName = preg_replace('/\.(index|create|edit|show|store|update|destroy)$/', '', $routeName);
        
        // Handle special mappings
        $routeToFormMap = [
            'organizations' => 'organizations',
            'users' => 'users',
            'roles' => 'roles',
            'permissions' => 'permissions',
            'role-permissions' => 'role-permissions',
            'company-information' => 'company-info',
            'raw-materials' => 'raw-materials',
            'purchase-orders' => 'purchase-orders',
            'material-inwards' => 'material-inwards',
            'sales-invoices' => 'sales-invoices',
            'daily-expenses' => 'daily-expenses',
            'stock-transactions' => 'stock-transactions',
            'work-orders' => 'work-orders',
            'productions' => 'productions',
            'notes' => 'notes',
            'tasks' => 'tasks',
            'suppliers' => 'suppliers',
            'products' => 'products',
            'customers' => 'customers',
            'employees' => 'employees',
        ];
        
        if (isset($routeToFormMap[$formName])) {
            $formName = $routeToFormMap[$formName];
        }
        
        return $this->canRead($formName);
    }

    /**
     * Check if user can access a page based on route name.
     * Converts route names like 'suppliers.index' to form name 'suppliers'.
     * Super Admin and Admin always have access.
     * Dashboard and Account Settings are always accessible.
     *
     * @param string $routeName The route name (e.g., 'suppliers.index', 'dashboard')
     * @return bool
     */
    public function canAccessPage(string $routeName): bool {
        // Super Admin and Admin have access to all pages
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        // Dashboard and Account Settings are always accessible
        if (in_array($routeName, ['dashboard', 'account.change-password'])) {
            return true;
        }

        // Convert route name to form name
        // Examples: 'suppliers.index' -> 'suppliers', 'raw-materials.index' -> 'raw-materials'
        $formName = $routeName;
        
        // Remove .index, .create, .edit, .show suffixes to get base form name
        $formName = preg_replace('/\.(index|create|edit|show|store|update|destroy)$/', '', $formName);
        
        // Handle special route name to form name mappings
        $routeToFormMap = [
            'organizations' => 'organizations',
            'users' => 'users',
            'roles' => 'roles',
            'permissions' => 'permissions',
            'role-permissions' => 'role-permissions',
            'company-information' => 'company-info', // Note: form_name is 'company-info' not 'company-information'
            'raw-materials' => 'raw-materials',
            'purchase-orders' => 'purchase-orders',
            'material-inwards' => 'material-inwards',
            'sales-invoices' => 'sales-invoices',
            'daily-expenses' => 'daily-expenses',
            'stock-transactions' => 'stock-transactions',
            'work-orders' => 'work-orders',
            'productions' => 'productions',
            'notes' => 'notes',
            'tasks' => 'tasks',
            'suppliers' => 'suppliers',
            'products' => 'products',
            'customers' => 'customers',
            'employees' => 'employees',
        ];
        
        if (isset($routeToFormMap[$formName])) {
            $formName = $routeToFormMap[$formName];
        }
        
        // Check permission for the form
        return $this->hasPermission($formName, 'read');
    }
}
