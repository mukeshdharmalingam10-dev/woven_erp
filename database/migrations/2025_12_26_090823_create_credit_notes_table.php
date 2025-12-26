<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('credit_note_number')->unique();
            $table->date('credit_note_date');
            $table->enum('reference_document_type', ['Purchase Invoice', 'Sales Invoice', 'Dispatch', 'Manual'])->nullable();
            $table->string('reference_document_number')->nullable();
            $table->unsignedBigInteger('reference_document_id')->nullable(); // ID of the referenced document
            $table->enum('party_type', ['Supplier', 'Customer'])->nullable();
            $table->unsignedBigInteger('party_id')->nullable(); // Supplier or Customer ID
            $table->string('party_name')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('currency')->default('INR');
            $table->enum('gst_classification', ['CGST_SGST', 'IGST'])->nullable();
            $table->decimal('gst_percentage', 5, 2)->default(18);
            $table->enum('credit_note_reason', ['Sales Return', 'Rate Difference', 'Excess Billing', 'Service Cancellation', 'Damage Compensation', 'Others'])->nullable();
            $table->text('remarks')->nullable();
            
            // Line item totals
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('igst_amount', 15, 2)->default(0);
            $table->decimal('adjustments', 15, 2)->default(0);
            $table->decimal('total_credit_amount', 15, 2)->default(0);
            
            // Status and workflow
            $table->enum('status', ['Draft', 'Submitted', 'Cancelled'])->default('Draft');
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->text('cancel_reason')->nullable();
            
            // Audit fields
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('credit_note_number');
            $table->index('reference_document_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_notes');
    }
}
