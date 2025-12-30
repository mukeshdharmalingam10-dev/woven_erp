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
        Schema::create('salary_processings', function (Blueprint $table) {
            $table->id();
            $table->date('salary_month'); // Month/Year picker
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('monthly_salary_amount', 12, 2); // Auto-populated from Salary Setup
            $table->date('attendance_source_month'); // Auto = Salary Month
            $table->integer('total_working_days'); // Auto-calculated
            $table->integer('present_days')->default(0); // Auto-populated from Attendance
            $table->integer('leave_days')->default(0); // Auto-populated from Attendance (editable)
            $table->decimal('per_day_salary', 12, 2)->default(0); // Auto-calculated
            $table->decimal('leave_deduction_amount', 12, 2)->default(0); // Auto-calculated
            $table->decimal('advance_deduction_amount', 12, 2)->default(0); // Editable based on rules
            $table->decimal('net_payable_salary', 12, 2)->default(0); // Auto-calculated
            $table->foreignId('salary_advance_id')->nullable()->constrained('salary_advances')->nullOnDelete(); // Selected advance reference
            $table->enum('payment_status', ['Pending', 'Paid'])->default('Pending');
            $table->date('paid_date')->nullable(); // Mandatory when status = Paid
            $table->text('notes')->nullable();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint: one salary processing per employee per month (handled in application for soft deletes)
            $table->unique(['salary_month', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_processings');
    }
};
