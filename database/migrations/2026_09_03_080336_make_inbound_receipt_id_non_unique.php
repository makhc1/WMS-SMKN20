<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('CREATE TABLE inbound_transactions_new (id INTEGER PRIMARY KEY AUTOINCREMENT, item_id INTEGER NOT NULL, transaction_date DATE NOT NULL, quantity INTEGER NOT NULL, supplier VARCHAR(255) NULL, notes TEXT NULL, status VARCHAR(255) NOT NULL DEFAULT "completed", receipt_id VARCHAR(255) NULL, condition VARCHAR(255) NOT NULL DEFAULT "Good", created_at DATETIME NULL, updated_at DATETIME NULL)');

            DB::statement('INSERT INTO inbound_transactions_new (id, item_id, transaction_date, quantity, supplier, notes, status, receipt_id, condition, created_at, updated_at) SELECT id, item_id, transaction_date, quantity, supplier, notes, status, receipt_id, condition, created_at, updated_at FROM inbound_transactions');

            DB::statement('DROP TABLE inbound_transactions');
            DB::statement('ALTER TABLE inbound_transactions_new RENAME TO inbound_transactions');
        } else {
            Schema::table('inbound_transactions', function (Blueprint $table) {
                $table->dropUnique(['receipt_id']);
            });
        }
    }

    public function down(): void
    {
        //
    }
};
