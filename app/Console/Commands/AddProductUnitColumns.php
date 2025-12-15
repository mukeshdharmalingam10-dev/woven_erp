<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddProductUnitColumns extends Command
{
    protected $signature = 'db:add-product-unit-columns';
    protected $description = 'Add product_id and unit_id columns to customer_order_items table';

    public function handle()
    {
        $this->info('Checking customer_order_items table...');

        // Add product_id column
        if (!Schema::hasColumn('customer_order_items', 'product_id')) {
            $this->info('Adding product_id column...');
            try {
                DB::statement('ALTER TABLE customer_order_items ADD COLUMN product_id BIGINT UNSIGNED NULL AFTER tender_item_id');
                $this->info('✓ product_id column added successfully');
            } catch (\Exception $e) {
                $this->error('Error adding product_id: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('product_id column already exists');
        }

        // Add foreign key for product_id
        try {
            $fkExists = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'customer_order_items' 
                AND CONSTRAINT_NAME = 'customer_order_items_product_id_foreign'
            ");
            
            if (empty($fkExists)) {
                $this->info('Adding foreign key for product_id...');
                DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE');
                $this->info('✓ Foreign key for product_id added successfully');
            } else {
                $this->info('Foreign key for product_id already exists');
            }
        } catch (\Exception $e) {
            $this->warn('Foreign key for product_id: ' . $e->getMessage());
        }

        // Add unit_id column
        if (!Schema::hasColumn('customer_order_items', 'unit_id')) {
            $this->info('Adding unit_id column...');
            try {
                DB::statement('ALTER TABLE customer_order_items ADD COLUMN unit_id BIGINT UNSIGNED NULL AFTER product_id');
                $this->info('✓ unit_id column added successfully');
            } catch (\Exception $e) {
                $this->error('Error adding unit_id: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('unit_id column already exists');
        }

        // Add foreign key for unit_id
        try {
            $fkExists = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'customer_order_items' 
                AND CONSTRAINT_NAME = 'customer_order_items_unit_id_foreign'
            ");
            
            if (empty($fkExists)) {
                $this->info('Adding foreign key for unit_id...');
                DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL');
                $this->info('✓ Foreign key for unit_id added successfully');
            } else {
                $this->info('Foreign key for unit_id already exists');
            }
        } catch (\Exception $e) {
            $this->warn('Foreign key for unit_id: ' . $e->getMessage());
        }

        $this->info('');
        $this->info('Done! Columns should now be available.');
        return 0;
    }
}

