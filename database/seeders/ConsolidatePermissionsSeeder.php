<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsolidatePermissionsSeeder extends Seeder
{
    /**
     * Consolidate permissions from action-based to resource-based
     * 
     * Example:
     * OLD: "Branches - View", "Branches - Create", "Branches - Edit", "Branches - Delete"
     * NEW: "Branches" (with Read/Write/Delete flags in pivot table)
     */
    public function run(): void
    {
        // Define resources that should exist (single entry per resource)
        $resources = [
            'branches',
            'users',
            'roles',
            'permissions',
            'units',
            'customers',
            'products',
            'quotations',
            'proforma-invoices',
            'tax',
            'company-info',
            'reports',
            'raw-material-categories',
            'raw-material-sub-categories',
            'raw-materials',
            'product-categories',
            'processes',
            'bom-processes',
            'departments',
            'designations',
            'production-departments',
            'employees',
            'billing-addresses',
            'tenders',
            'discounts',
        ];

        DB::beginTransaction();
        
        try {
            // Step 1: Create/Update resource-based permissions
            $resourcePermissions = [];
            foreach ($resources as $resource) {
                $permission = Permission::updateOrCreate(
                    ['form_name' => $resource],
                    [
                        'form_name' => $resource,
                        'name' => ucfirst(str_replace('-', ' ', $resource)),
                        'slug' => $resource,
                        'description' => "Permission for {$resource} resource",
                        'module' => $resource,
                        'is_active' => true,
                    ]
                );
                
                $resourcePermissions[$resource] = $permission;
                $this->command->info("✓ Created/Updated permission: {$resource}");
            }

            // Step 2: Consolidate role_permission mappings
            $roles = Role::all();
            
            foreach ($roles as $role) {
                $role->load('permissions');
                
                // Group old permissions by resource and extract flags
                $permissionsByResource = [];
                
                foreach ($role->permissions as $permission) {
                    $permissionName = $permission->form_name ?? $permission->name ?? '';
                    $resourceName = $this->extractResourceName($permissionName);
                    
                    if ($resourceName && isset($resourcePermissions[$resourceName])) {
                        if (!isset($permissionsByResource[$resourceName])) {
                            $permissionsByResource[$resourceName] = [
                                'read' => false,
                                'write' => false,
                                'delete' => false,
                            ];
                        }
                        
                        // Map old permission to new flags based on pivot or name
                        $oldRead = $permission->pivot->read ?? false;
                        $oldWrite = $permission->pivot->write ?? false;
                        $oldDelete = $permission->pivot->delete ?? false;
                        
                        // If pivot has flags, use them; otherwise extract from name
                        if ($oldRead || $oldWrite || $oldDelete) {
                            $permissionsByResource[$resourceName]['read'] = $permissionsByResource[$resourceName]['read'] || $oldRead;
                            $permissionsByResource[$resourceName]['write'] = $permissionsByResource[$resourceName]['write'] || $oldWrite;
                            $permissionsByResource[$resourceName]['delete'] = $permissionsByResource[$resourceName]['delete'] || $oldDelete;
                        } else {
                            // Extract from permission name
                            $action = $this->extractAction($permissionName);
                            if ($action === 'view' || $action === 'read') {
                                $permissionsByResource[$resourceName]['read'] = true;
                            } elseif ($action === 'create' || $action === 'edit' || $action === 'write') {
                                $permissionsByResource[$resourceName]['write'] = true;
                            } elseif ($action === 'delete' || $action === 'destroy') {
                                $permissionsByResource[$resourceName]['delete'] = true;
                            }
                        }
                    }
                }
                
                // Build sync data with new consolidated permissions
                $newPermissionIds = [];
                foreach ($permissionsByResource as $resourceName => $flags) {
                    if (isset($resourcePermissions[$resourceName]) && ($flags['read'] || $flags['write'] || $flags['delete'])) {
                        $newPermissionIds[$resourcePermissions[$resourceName]->id] = $flags;
                    }
                }
                
                // Sync with new consolidated permissions
                if (!empty($newPermissionIds)) {
                    $role->permissions()->sync($newPermissionIds);
                    $this->command->info("  ✓ Consolidated permissions for role: {$role->name}");
                }
            }

            // Step 3: Delete old duplicate permissions (keep only resource-based ones)
            $resourceFormNames = array_column($resourcePermissions, 'form_name');
            $deletedCount = Permission::whereNotIn('form_name', $resourceFormNames)
                ->where(function($query) use ($resources) {
                    // Also check for old format permissions
                    foreach ($resources as $resource) {
                        $query->orWhere('form_name', 'LIKE', "%{$resource}%")
                              ->orWhere('name', 'LIKE', "%{$resource}%");
                    }
                })
                ->where(function($query) {
                    // Match old patterns
                    $query->where('form_name', 'LIKE', '% - %')
                          ->orWhere('name', 'LIKE', '% - %')
                          ->orWhere('form_name', 'LIKE', 'View %')
                          ->orWhere('form_name', 'LIKE', 'Create %')
                          ->orWhere('form_name', 'LIKE', 'Edit %')
                          ->orWhere('form_name', 'LIKE', 'Delete %');
                })
                ->delete();
            
            if ($deletedCount > 0) {
                $this->command->info("  ✓ Deleted {$deletedCount} old duplicate permissions");
            }

            DB::commit();
            $this->command->info("\n✅ Permissions consolidated successfully!");
            $this->command->info("   Created/Updated: " . count($resourcePermissions) . " resource-based permissions");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error consolidating permissions: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Extract resource name from permission name
     */
    private function extractResourceName($permissionName)
    {
        $permissionName = strtolower(trim($permissionName));
        
        // Handle formats:
        // "Branches - View" -> "branches"
        // "View Branches" -> "branches"
        // "branches" -> "branches"
        
        // Remove common action prefixes/suffixes
        $patterns = [
            '/^view\s+/i',
            '/^create\s+/i',
            '/^edit\s+/i',
            '/^delete\s+/i',
            '/\s*-\s*(view|create|edit|delete|approve|export|print)$/i',
            '/\s+(view|create|edit|delete|approve|export|print)$/i',
        ];
        
        $resource = preg_replace($patterns, '', $permissionName);
        $resource = trim($resource);
        
        // Normalize resource names
        $resourceMap = [
            'proforma-invoices' => 'proforma-invoices',
            'proforma invoices' => 'proforma-invoices',
            'proforma_invoices' => 'proforma-invoices',
            'raw-material-categories' => 'raw-material-categories',
            'raw-material-sub-categories' => 'raw-material-sub-categories',
            'raw-materials' => 'raw-materials',
            'product-categories' => 'product-categories',
            'bom-processes' => 'bom-processes',
            'production-departments' => 'production-departments',
            'billing-addresses' => 'billing-addresses',
            'company-info' => 'company-info',
            'company information' => 'company-info',
            'company_information' => 'company-info',
        ];
        
        if (isset($resourceMap[$resource])) {
            return $resourceMap[$resource];
        }
        
        // Convert spaces/underscores to hyphens and normalize
        $resource = str_replace([' ', '_'], '-', $resource);
        
        return $resource ?: null;
    }

    /**
     * Extract action from permission name
     */
    private function extractAction($permissionName)
    {
        $permissionName = strtolower($permissionName);
        
        if (preg_match('/\b(view|read)\b/i', $permissionName)) {
            return 'view';
        } elseif (preg_match('/\b(create|add)\b/i', $permissionName)) {
            return 'create';
        } elseif (preg_match('/\b(edit|update|modify|write)\b/i', $permissionName)) {
            return 'edit';
        } elseif (preg_match('/\b(delete|remove|destroy)\b/i', $permissionName)) {
            return 'delete';
        }
        
        return null;
    }
}
