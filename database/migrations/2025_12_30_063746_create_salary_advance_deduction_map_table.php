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
        if (!Schema::hasTable('salary_advance_deduction_map')) {
            Schema::create('salary_advance_deduction_map', function (Blueprint $table) {
                $table->id();
                $table->foreignId('salary_processing_id')->constrained('salary_processings')->onDelete('cascade');
                $table->foreignId('advance_id')->constrained('salary_advances')->onDelete('cascade');
                $table->decimal('deducted_amount', 12, 2)->default(0);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_advance_deduction_map');
    }
};
