<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Entity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $userRole = Role::where('slug', 'user')->first();

        $headOffice = Entity::where('code', 'HO')->first();
        $branch1 = Entity::where('code', 'BR1')->first();

        // Super Admin User (Default)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('Admin@123'),
                'role_id' => $adminRole->id ?? null,
                'entity_id' => $headOffice->id ?? null,
            ]
        );

        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@erp.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@erp.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id ?? null,
                'entity_id' => $headOffice->id ?? null,
            ]
        );

        // Manager User
        User::updateOrCreate(
            ['email' => 'manager@erp.com'],
            [
                'name' => 'Manager User',
                'email' => 'manager@erp.com',
                'password' => Hash::make('password'),
                'role_id' => $managerRole->id ?? null,
                'entity_id' => $branch1->id ?? null,
            ]
        );

        // Regular User
        User::updateOrCreate(
            ['email' => 'user@erp.com'],
            [
                'name' => 'Test User',
                'email' => 'user@erp.com',
                'password' => Hash::make('password'),
                'role_id' => $userRole->id ?? null,
                'entity_id' => $branch1->id ?? null,
            ]
        );
    }
}

