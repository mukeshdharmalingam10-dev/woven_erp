<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            ModuleActionPermissionSeeder::class, // Module-action based permissions (primary)
            PermissionSeeder::class, // Legacy permissions (if any additional ones needed)
            EntitySeeder::class,
            BranchSeeder::class, // Create branches
            SuperAdminSeeder::class, // Create Super Admin first
            UserSeeder::class,
            BranchUserSeeder::class, // Create test Branch User with branch assigned
            MenuFormSeeder::class, // New menu / submenu / form structure
        ]);
    }
}
