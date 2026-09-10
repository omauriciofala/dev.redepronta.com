<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('depots')) {
            Schema::table('depots', function (Blueprint $table) {
                if (Schema::hasColumn('depots', 'vehicle_plate')) {
                    $table->dropColumn('vehicle_plate');
                }
            });

            // Atualiza tipo do enum para manter apenas depósitos físicos no MariaDB/MySQL
            if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
                DB::statement("ALTER TABLE depots MODIFY COLUMN type ENUM('CENTRAL', 'REGIONAL_BASE', 'LAB_REPAIR') NOT NULL DEFAULT 'REGIONAL_BASE'");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('depots')) {
            Schema::table('depots', function (Blueprint $table) {
                if (!Schema::hasColumn('depots', 'vehicle_plate')) {
                    $table->string('vehicle_plate', 20)->nullable()->comment('Placa do veículo');
                }
            });

            if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
                DB::statement("ALTER TABLE depots MODIFY COLUMN type ENUM('CENTRAL', 'REGIONAL_BASE', 'VEHICLE', 'LAB_REPAIR') NOT NULL DEFAULT 'REGIONAL_BASE'");
            }
        }
    }
};
