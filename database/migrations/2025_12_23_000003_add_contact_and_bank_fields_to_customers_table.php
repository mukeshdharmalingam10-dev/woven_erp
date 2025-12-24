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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'contact_name_1')) {
                $table->string('contact_name_1')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('customers', 'contact_name_2')) {
                $table->string('contact_name_2')->nullable()->after('contact_name_1');
            }

            if (!Schema::hasColumn('customers', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('gst_number');
            }
            if (!Schema::hasColumn('customers', 'ifsc_code')) {
                $table->string('ifsc_code')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('customers', 'account_number')) {
                $table->string('account_number')->nullable()->after('ifsc_code');
            }
            if (!Schema::hasColumn('customers', 'bank_branch_name')) {
                $table->string('bank_branch_name')->nullable()->after('account_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'contact_name_1')) {
                $table->dropColumn('contact_name_1');
            }
            if (Schema::hasColumn('customers', 'contact_name_2')) {
                $table->dropColumn('contact_name_2');
            }
            if (Schema::hasColumn('customers', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
            if (Schema::hasColumn('customers', 'ifsc_code')) {
                $table->dropColumn('ifsc_code');
            }
            if (Schema::hasColumn('customers', 'account_number')) {
                $table->dropColumn('account_number');
            }
            if (Schema::hasColumn('customers', 'bank_branch_name')) {
                $table->dropColumn('bank_branch_name');
            }
        });
    }
};


