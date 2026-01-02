<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('salary_month');
            $table->decimal('monthly_salary_amount', 15, 2);
            $table->boolean('advance_applicable')->default(false);
            $table->enum('deduction_mode', ['Full Deduction', 'Monthly Installment', 'Variable Installment'])->nullable();
            $table->date('full_deduction_month')->nullable();
            $table->decimal('full_deduction_amount', 15, 2)->nullable();
            $table->date('installment_start_month')->nullable();
            $table->decimal('installment_amount', 15, 2)->nullable();
            $table->decimal('variable_deduction_amount', 15, 2)->nullable();
            $table->integer('days_present')->default(0);
            $table->integer('days_in_month')->default(0);
            $table->integer('absent_days')->default(0);
            $table->decimal('per_day_salary', 15, 2)->default(0);
            $table->decimal('salary_as_per_attendance', 15, 2)->default(0);
            $table->decimal('deduction_amount', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint to prevent duplicate salary records for same employee and month
            $table->unique(['employee_id', 'salary_month', 'deleted_at'], 'unique_employee_salary_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_masters');
    }
}
