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
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->string('advance_reference_no')->unique(); // Auto-generated
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('advance_date');
            $table->decimal('advance_amount', 12, 2);
            $table->enum('advance_deduction_mode', ['Full Deduction', 'Monthly Installment', 'Variable Installment']);
            $table->date('installment_start_month')->nullable(); // Mandatory when installment mode selected
            $table->decimal('installment_amount', 12, 2)->nullable(); // Only for Monthly Installment
            $table->text('remarks')->nullable();
            $table->decimal('advance_balance_amount', 12, 2)->default(0); // Auto-calculated (Advance Amount - Total Deducted)
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_advances');
    }
};
