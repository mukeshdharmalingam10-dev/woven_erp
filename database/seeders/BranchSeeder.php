<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Main Branch',
                'code' => 'MB001',
                'description' => 'Main branch office',
                'address_line_1' => '123 Main Street',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400001',
                'phone' => '+91-22-12345678',
                'email' => 'main@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Branch Office 1',
                'code' => 'BR001',
                'description' => 'First branch office',
                'address_line_1' => '456 Park Avenue',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'pincode' => '110001',
                'phone' => '+91-11-87654321',
                'email' => 'branch1@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Branch Office 2',
                'code' => 'BR002',
                'description' => 'Second branch office',
                'address_line_1' => '789 Business Park',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode' => '560001',
                'phone' => '+91-80-11223344',
                'email' => 'branch2@company.com',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                ['code' => $branch['code']],
                $branch
            );
        }

        $this->command->info('✅ Branches created successfully!');
        $this->command->info('   Created ' . count($branches) . ' branches');
    }
}
