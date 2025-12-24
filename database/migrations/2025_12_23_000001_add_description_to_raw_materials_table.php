<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            // Add description as a nullable text field after unit_of_measure for better grouping
            if (!Schema::hasColumn('raw_materials', 'description')) {
                $table->text('description')->nullable()->after('unit_of_measure');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            if (Schema::hasColumn('raw_materials', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};


