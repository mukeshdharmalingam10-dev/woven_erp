<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeSupplierIdToCustomerIdInQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['supplier_id']);
        });

        // Rename the column
        DB::statement('ALTER TABLE quotations CHANGE supplier_id customer_id BIGINT UNSIGNED NOT NULL');

        Schema::table('quotations', function (Blueprint $table) {
            // Add the new foreign key constraint
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['customer_id']);
        });

        // Rename the column back
        DB::statement('ALTER TABLE quotations CHANGE customer_id supplier_id BIGINT UNSIGNED NOT NULL');

        Schema::table('quotations', function (Blueprint $table) {
            // Add the old foreign key constraint
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnUpdate()->restrictOnDelete();
        });
    }
}
