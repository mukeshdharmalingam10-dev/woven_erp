<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('payment_amount', 15, 2);
            $table->enum('payment_method', ['Cash', 'Cheque', 'Bank Transfer', 'UPI', 'Credit Card', 'Debit Card', 'Other'])->default('Cash');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_trackings');
    }
}
