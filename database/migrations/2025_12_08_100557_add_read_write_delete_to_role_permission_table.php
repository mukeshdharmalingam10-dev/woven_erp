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
        Schema::table('role_permission', function (Blueprint $table) {
            $table->boolean('read')->default(false)->after('permission_id');
            $table->boolean('write')->default(false)->after('read');
            $table->boolean('delete')->default(false)->after('write');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_permission', function (Blueprint $table) {
            $table->dropColumn(['read', 'write', 'delete']);
        });
    }
};
