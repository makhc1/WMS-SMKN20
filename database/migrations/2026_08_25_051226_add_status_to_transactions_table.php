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
        Schema::table('inbound_transactions', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('notes');
        });

        Schema::table('outbound_transactions', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inbound_transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('outbound_transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
