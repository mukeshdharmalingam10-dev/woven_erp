<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename per_kg_weight to per_bag_weight, or add per_bag_weight if missing.
     */
    public function up(): void
    {
        if (Schema::hasColumn('work_orders', 'per_kg_weight')) {
            DB::statement('ALTER TABLE work_orders CHANGE per_kg_weight per_bag_weight DECIMAL(10,3) NULL');
        } elseif (!Schema::hasColumn('work_orders', 'per_bag_weight')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->decimal('per_bag_weight', 10, 3)->nullable()->after('quantity_to_produce');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('work_orders', 'per_bag_weight')) {
            DB::statement('ALTER TABLE work_orders CHANGE per_bag_weight per_kg_weight DECIMAL(10,3) NULL');
        }
    }
};
