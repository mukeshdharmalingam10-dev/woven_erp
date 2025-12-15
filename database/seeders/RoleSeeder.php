<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Highest level admin who manages all branches and users',
                'is_system_role' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Branch User',
                'slug' => 'branch-user',
                'description' => 'User with access restricted to specific branches',
                'is_system_role' => false,
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}

