<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->string('role')->default('Staff Picker');
            $table->string('status')->default('Active');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->decimal('base_price', 15, 2)->nullable();
            $table->string('photo')->nullable();
            $table->text('description')->nullable();
            $table->string('unit')->default('Pcs');
        });

        Schema::table('inbound_transactions', function (Blueprint $table) {
            $table->string('receipt_id')->nullable()->unique();
            $table->string('condition')->default('Good');
        });

        Schema::table('outbound_transactions', function (Blueprint $table) {
            $table->string('customer_name')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('courier')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->string('receipt_id')->nullable()->unique();
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->string('zone_name')->nullable();
            $table->string('storage_type')->default('Dry');
            $table->integer('capacity_percentage')->default(0);
            $table->string('status')->default('Active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'role', 'status']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'photo', 'description', 'unit']);
        });

        Schema::table('inbound_transactions', function (Blueprint $table) {
            $table->dropColumn(['receipt_id', 'condition']);
        });

        Schema::table('outbound_transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_address', 'courier', 'estimated_delivery_date', 'receipt_id']);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['zone_name', 'storage_type', 'capacity_percentage', 'status']);
        });
    }
};
