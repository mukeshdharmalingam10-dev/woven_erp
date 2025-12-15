<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        $entities = [
            ['name' => 'Head Office', 'code' => 'HO', 'description' => 'Main headquarters'],
            ['name' => 'Branch Office 1', 'code' => 'BR1', 'description' => 'First branch office'],
            ['name' => 'Branch Office 2', 'code' => 'BR2', 'description' => 'Second branch office'],
        ];

        foreach ($entities as $entity) {
            Entity::updateOrCreate(
                ['code' => $entity['code']],
                $entity
            );
        }
    }
}

