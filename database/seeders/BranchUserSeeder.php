<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BranchUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates a test Branch User with assigned branch
     */
    public function run(): void
    {
        // Get Branch User role
        $branchUserRole = Role::where('slug', 'branch-user')->first();
        
        if (!$branchUserRole) {
            $this->command->warn('Branch User role not found. Please run RoleSeeder first.');
            return;
        }

        // Get first active branch
        $branch = Branch::where('is_active', true)->first();
        
        if (!$branch) {
            $this->command->warn('No active branches found. Please run BranchSeeder first.');
            return;
        }

        // Create test Branch User
        $user = User::updateOrCreate(
            ['email' => 'branchuser@test.com'],
            [
                'name' => 'Branch User',
                'email' => 'branchuser@test.com',
                'password' => Hash::make('Branch@123'),
                'role_id' => $branchUserRole->id,
                'status' => 'active',
                'mobile' => '+91-9876543210',
                'organization_id' => null,
                'branch_id' => null,
                'entity_id' => null,
                'created_by' => null,
            ]
        );

        // Assign branch to user
        $user->branches()->sync([$branch->id]);

        $this->command->info('✅ Test Branch User created successfully!');
        $this->command->info('   Email: branchuser@test.com');
        $this->command->info('   Password: Branch@123');
        $this->command->info('   Assigned Branch: ' . $branch->name);
    }
}
