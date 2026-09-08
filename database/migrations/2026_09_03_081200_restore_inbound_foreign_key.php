<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // Recreate the table WITHOUT the unique index on receipt_id but
            // preserving the foreign key on item_id and its index.
            Schema::disableForeignKeyConstraints();

            DB::statement('DROP TABLE IF EXISTS inbound_transactions_new');

            DB::statement('CREATE TABLE inbound_transactions_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                item_id INTEGER NOT NULL,
                transaction_date DATE NOT NULL,
                quantity INTEGER NOT NULL,
                supplier VARCHAR(255) NULL,
                notes TEXT NULL,
                status VARCHAR(255) NOT NULL DEFAULT "completed",
                receipt_id VARCHAR(255) NULL,
                condition VARCHAR(255) NOT NULL DEFAULT "Good",
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                CONSTRAINT inbound_transactions_item_id_foreign FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE CASCADE
            )');

            DB::statement('CREATE INDEX inbound_transactions_item_id_index ON inbound_transactions_new (item_id)');

            DB::statement('INSERT INTO inbound_transactions_new (id, item_id, transaction_date, quantity, supplier, notes, status, receipt_id, condition, created_at, updated_at)
                SELECT id, item_id, transaction_date, quantity, supplier, notes, status, receipt_id, condition, created_at, updated_at FROM inbound_transactions');

            DB::statement('DROP TABLE inbound_transactions');
            DB::statement('ALTER TABLE inbound_transactions_new RENAME TO inbound_transactions');

            Schema::enableForeignKeyConstraints();
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
