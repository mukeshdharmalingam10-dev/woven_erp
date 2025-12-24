<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankFieldsToSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('gst_number');
            $table->string('ifsc_code')->nullable()->after('bank_name');
            $table->string('account_number')->nullable()->after('ifsc_code');
            $table->string('branch_name')->nullable()->after('account_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'ifsc_code', 'account_number', 'branch_name']);
        });
    }
}
