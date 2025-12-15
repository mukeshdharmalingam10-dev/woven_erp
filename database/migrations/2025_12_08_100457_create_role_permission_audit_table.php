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
        Schema::create('role_permission_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('permission_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // created, updated, deleted
            $table->string('field_name')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permission_audit');
    }
};
