<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AdminSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates Admin and Super Admin users with all permissions
     *
     * @return void
     */
    public function run(): void
    {
        // Get or create Super Admin role
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Highest level admin with full system access',
            ]
        );

        // Get or create Admin role
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrator with full system access',
            ]
        );

        // Get all active permissions
        $allPermissions = Permission::where('is_active', true)->get();

        // Assign all permissions to Super Admin role with full access (read, write, delete)
        foreach ($allPermissions as $permission) {
            DB::table('role_permission')->updateOrInsert(
                [
                    'role_id' => $superAdminRole->id,
                    'permission_id' => $permission->id,
                ],
                [
                    'read' => true,
                    'write' => true,
                    'delete' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Assign all permissions to Admin role with full access (read, write, delete)
        foreach ($allPermissions as $permission) {
            DB::table('role_permission')->updateOrInsert(
                [
                    'role_id' => $adminRole->id,
                    'permission_id' => $permission->id,
                ],
                [
                    'read' => true,
                    'write' => true,
                    'delete' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Create Super Admin user
        $superAdminData = [
            'name' => 'Super Admin',
            'email' => 'superadmin@crm.com',
            'password' => Hash::make('SuperAdmin@123'),
            'role_id' => $superAdminRole->id,
            'mobile' => null,
            'organization_id' => null,
            'branch_id' => null,
            'entity_id' => null,
        ];
        
        // Add new fields only if migrations have been run
        if (Schema::hasColumn('users', 'status')) {
            $superAdminData['status'] = 'active';
        }
        if (Schema::hasColumn('users', 'created_by')) {
            $superAdminData['created_by'] = null;
        }
        
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@crm.com'],
            $superAdminData
        );

        // Ensure Super Admin has no branch assignments
        $superAdmin->branches()->detach();
        
        // Assign Super Admin role via user_role pivot table
        DB::table('user_role')->updateOrInsert(
            [
                'user_id' => $superAdmin->id,
                'role_id' => $superAdminRole->id,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create Admin user
        $adminData = [
            'name' => 'Admin',
            'email' => 'admin@crm.com',
            'password' => Hash::make('Admin@123'),
            'role_id' => $adminRole->id,
            'mobile' => null,
            'organization_id' => null,
            'branch_id' => null,
            'entity_id' => null,
        ];
        
        // Add new fields only if migrations have been run
        if (Schema::hasColumn('users', 'status')) {
            $adminData['status'] = 'active';
        }
        if (Schema::hasColumn('users', 'created_by')) {
            $adminData['created_by'] = null;
        }
        
        $admin = User::updateOrCreate(
            ['email' => 'admin@crm.com'],
            $adminData
        );

        // Ensure Admin has no branch assignments
        $admin->branches()->detach();
        
        // Assign Admin role via user_role pivot table
        DB::table('user_role')->updateOrInsert(
            [
                'user_id' => $admin->id,
                'role_id' => $adminRole->id,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Display credentials
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('✅ Admin and Super Admin users created successfully!');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📧 SUPER ADMIN CREDENTIALS:');
        $this->command->info('   Email: superadmin@crm.com');
        $this->command->info('   Password: SuperAdmin@123');
        $this->command->info('   Role: Super Admin');
        $this->command->info('   Permissions: All permissions with full access (' . $allPermissions->count() . ' permissions)');
        $this->command->info('');
        $this->command->info('📧 ADMIN CREDENTIALS:');
        $this->command->info('   Email: admin@crm.com');
        $this->command->info('   Password: Admin@123');
        $this->command->info('   Role: Admin');
        $this->command->info('   Permissions: All permissions with full access (' . $allPermissions->count() . ' permissions)');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
    }
}

