<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_serials') && DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE stock_serials MODIFY COLUMN status ENUM('IN_STOCK', 'IN_TRANSIT', 'INSTALLED_CUSTOMER', 'DEFECTIVE', 'DISCARDED', 'RETURNED') NOT NULL DEFAULT 'IN_STOCK'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_serials') && DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE stock_serials MODIFY COLUMN status ENUM('IN_STOCK', 'IN_TRANSIT', 'INSTALLED_CUSTOMER', 'DEFECTIVE', 'DISCARDED') NOT NULL DEFAULT 'IN_STOCK'");
        }
    }
};
