<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * NOTE: This seeder now creates resource-based permissions (one per resource)
     * instead of action-based permissions. The Read/Write/Delete flags are managed
     * in the role_permission pivot table.
     */
    public function run(): void
    {
        $resources = [
            'branches',
            'users',
            'roles',
            'permissions',
        ];

        foreach ($resources as $resource) {
            Permission::updateOrCreate(
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
        }

        $this->command->info('Resource-based permissions seeded successfully.');
    }
}
