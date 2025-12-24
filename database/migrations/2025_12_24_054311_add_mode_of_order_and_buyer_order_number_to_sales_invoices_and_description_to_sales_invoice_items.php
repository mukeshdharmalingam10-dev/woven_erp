<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModeOfOrderAndBuyerOrderNumberToSalesInvoicesAndDescriptionToSalesInvoiceItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->string('mode_of_order')->default('IMMEDIATE')->after('customer_id');
            $table->string('buyer_order_number')->nullable()->after('mode_of_order');
        });

        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn(['mode_of_order', 'buyer_order_number']);
        });

        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
