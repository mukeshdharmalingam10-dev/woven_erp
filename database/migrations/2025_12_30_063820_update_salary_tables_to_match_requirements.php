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
        // Add missing fields to salary_advances table
        if (Schema::hasTable('salary_advances')) {
            Schema::table('salary_advances', function (Blueprint $table) {
                if (!Schema::hasColumn('salary_advances', 'total_deducted_amount')) {
                    $table->decimal('total_deducted_amount', 12, 2)->default(0)->after('advance_amount');
                }
                if (!Schema::hasColumn('salary_advances', 'status')) {
                    $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN')->after('advance_balance_amount');
                }
            });
        }

        // Add missing fields to salary_processings table
        if (Schema::hasTable('salary_processings')) {
            Schema::table('salary_processings', function (Blueprint $table) {
                if (!Schema::hasColumn('salary_processings', 'leave_days_deductible')) {
                    $table->decimal('leave_days_deductible', 8, 2)->default(0)->after('leave_days');
                }
                if (!Schema::hasColumn('salary_processings', 'leave_override_reason')) {
                    $table->text('leave_override_reason')->nullable()->after('leave_deduction_amount');
                }
                if (!Schema::hasColumn('salary_processings', 'is_leave_overridden')) {
                    $table->boolean('is_leave_overridden')->default(false)->after('leave_override_reason');
                }
                if (!Schema::hasColumn('salary_processings', 'advance_allocation_mode')) {
                    $table->enum('advance_allocation_mode', ['SELECT_REFERENCE', 'OLDEST_FIRST'])->default('OLDEST_FIRST')->after('is_leave_overridden');
                }
                // Rename salary_advance_id to advance_reference_id if needed (keeping both for backward compatibility)
                // Actually, let's keep salary_advance_id as it is, but the logic can use it as advance_reference_id
            });
        }

        // Update salary_setups to use YYYY-MM format for effective_from_month and effective_to_month
        // Since we're using date fields, we'll handle the YYYY-MM format in the application layer
        // But we can add a comment or note that these should be stored as first day of month
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('salary_advances')) {
            Schema::table('salary_advances', function (Blueprint $table) {
                if (Schema::hasColumn('salary_advances', 'total_deducted_amount')) {
                    $table->dropColumn('total_deducted_amount');
                }
                if (Schema::hasColumn('salary_advances', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }

        if (Schema::hasTable('salary_processings')) {
            Schema::table('salary_processings', function (Blueprint $table) {
                if (Schema::hasColumn('salary_processings', 'leave_days_deductible')) {
                    $table->dropColumn('leave_days_deductible');
                }
                if (Schema::hasColumn('salary_processings', 'leave_override_reason')) {
                    $table->dropColumn('leave_override_reason');
                }
                if (Schema::hasColumn('salary_processings', 'is_leave_overridden')) {
                    $table->dropColumn('is_leave_overridden');
                }
                if (Schema::hasColumn('salary_processings', 'advance_allocation_mode')) {
                    $table->dropColumn('advance_allocation_mode');
                }
            });
        }
    }
};
