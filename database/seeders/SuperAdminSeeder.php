<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates the default Super Admin user
     *
     * @return void
     */
    public function run(): void
    {
        // Get or create Super Admin role
        $adminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Highest level admin with full system access',
                'is_active' => true,
                'is_system_role' => true,
            ]
        );

        // Create Super Admin user (no branch assignment needed for Super Admin)
        $userData = [
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'role_id' => $adminRole->id,
            'mobile' => null,
            'organization_id' => null,
            'branch_id' => null,
            'entity_id' => null,
        ];
        
        // Add new fields only if migrations have been run
        if (\Schema::hasColumn('users', 'status')) {
            $userData['status'] = 'active';
        }
        if (\Schema::hasColumn('users', 'created_by')) {
            $userData['created_by'] = null;
        }
        
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            $userData
        );

        // Ensure Super Admin has no branch assignments
        $user->branches()->detach();
        
        // Assign all permissions to Super Admin role (Super Admin should have all permissions)
        $allPermissions = \App\Models\Permission::where('is_active', true)->get();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        $this->command->info('✅ Super Admin user created successfully!');
        $this->command->info('   Email: admin@gmail.com');
        $this->command->info('   Password: Admin@123');
        $this->command->info('   Role: Super Admin (no branch restrictions)');
        $this->command->info('   Permissions: All permissions assigned (' . $allPermissions->count() . ' permissions)');
    }
}

