<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseOrderIdColumnToMaterialInwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('material_inwards', 'purchase_order_id')) {
        Schema::table('material_inwards', function (Blueprint $table) {
                $table->foreignId('purchase_order_id')->nullable()->after('supplier_id')->constrained('purchase_orders')->onDelete('set null');
        });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('material_inwards', 'purchase_order_id')) {
        Schema::table('material_inwards', function (Blueprint $table) {
                $table->dropForeign(['purchase_order_id']);
                $table->dropColumn('purchase_order_id');
        });
        }
    }
}
