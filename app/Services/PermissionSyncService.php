<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PermissionSyncService
{
    /**
     * Route name to form name mapping
     */
    protected $routeToFormMap = [
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

    /**
     * Route name to module mapping
     */
    protected $routeToModuleMap = [
        'organizations' => 'System Admin',
        'users' => 'System Admin',
        'roles' => 'System Admin',
        'permissions' => 'System Admin',
        'role-permissions' => 'System Admin',
        'branches' => 'System Admin',
        'company-information' => 'Settings',
        'suppliers' => 'Masters',
        'raw-materials' => 'Masters',
        'products' => 'Masters',
        'customers' => 'Masters',
        'employees' => 'Masters',
        'purchase-orders' => 'Transactions',
        'material-inwards' => 'Transactions',
        'sales-invoices' => 'Transactions',
        'stock-transactions' => 'Transactions',
        'work-orders' => 'Productions',
        'productions' => 'Productions',
        'notes' => 'CRM',
        'tasks' => 'CRM',
    ];

    /**
     * Sync permissions from application routes
     * Automatically creates/updates permissions based on resource routes
     */
    public function syncFromRoutes(): array
    {
        $created = [];
        $updated = [];
        $skipped = [];

        // Get all routes
        $routes = Route::getRoutes();

        // Extract unique resource route names (remove .index, .create, etc.)
        $resourceNames = [];

        foreach ($routes as $route) {
            $routeName = $route->getName();
            
            if (!$routeName) {
                continue;
            }

            // Skip dashboard and account settings (always visible)
            if (in_array($routeName, ['dashboard', 'account.change-password'])) {
                continue;
            }

            // Extract base resource name (remove .index, .create, .edit, etc.)
            $baseName = preg_replace('/\.(index|create|edit|show|store|update|destroy)$/', '', $routeName);
            
            // Skip if it's not a resource route or is a nested route
            if ($baseName === $routeName && !str_contains($routeName, '.')) {
                // This might be a resource route without action suffix
                if (!in_array($routeName, ['login', 'logout', 'register', 'password', 'home'])) {
                    $resourceNames[] = $routeName;
                }
            } elseif ($baseName !== $routeName) {
                // This is a resource route with action
                if (!in_array($baseName, $resourceNames)) {
                    $resourceNames[] = $baseName;
                }
            }
        }

        // Remove duplicates
        $resourceNames = array_unique($resourceNames);

        // Process each resource
        foreach ($resourceNames as $routeName) {
            $formName = $this->getFormNameFromRoute($routeName);
            $module = $this->getModuleFromRoute($routeName);

            // Skip if no module mapping (likely not a main resource)
            if (!$module) {
                $skipped[] = $routeName;
                continue;
            }

            // Check if permission already exists (by slug, which is unique)
            $existing = Permission::where('slug', $formName)->first();

            $data = [
                'form_name' => $formName,
                'name' => $this->formatName($formName),
                'slug' => $formName,
                'description' => "Permission for {$this->formatName($formName)} resource",
                'module' => $module,
                'is_active' => true,
            ];

            // Use updateOrCreate with slug as unique identifier (slug is unique in database)
            $permission = Permission::updateOrCreate(
                ['slug' => $formName],
                $data
            );
            
            if ($existing) {
                $updated[] = $formName;
            } else {
                $created[] = $formName;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * Get form name from route name
     */
    protected function getFormNameFromRoute(string $routeName): string
    {
        // Remove action suffixes
        $formName = preg_replace('/\.(index|create|edit|show|store|update|destroy)$/', '', $routeName);
        
        // Use mapping if available, otherwise use route name as-is
        return $this->routeToFormMap[$formName] ?? $formName;
    }

    /**
     * Get module from route name
     */
    protected function getModuleFromRoute(string $routeName): ?string
    {
        // Remove action suffixes
        $baseName = preg_replace('/\.(index|create|edit|show|store|update|destroy)$/', '', $routeName);
        
        return $this->routeToModuleMap[$baseName] ?? null;
    }

    /**
     * Format form name for display
     */
    protected function formatName(string $formName): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $formName));
    }

    /**
     * Manually add a new permission
     */
    public function addPermission(string $formName, string $module, ?string $displayName = null): Permission
    {
        $data = [
            'form_name' => $formName,
            'name' => $displayName ?? $this->formatName($formName),
            'slug' => $formName,
            'description' => "Permission for {$this->formatName($formName)} resource",
            'module' => $module,
            'is_active' => true,
        ];

        return Permission::updateOrCreate(
            ['form_name' => $formName],
            $data
        );
    }
}

