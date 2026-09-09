<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->boolean('commercial_same_as_residential')->default(true)->after('neighborhood');
            $table->string('commercial_postal_code', 10)->nullable()->after('commercial_same_as_residential');
            $table->string('commercial_street', 150)->nullable()->after('commercial_postal_code');
            $table->string('commercial_number', 30)->nullable()->after('commercial_street');
            $table->string('commercial_complement', 100)->nullable()->after('commercial_number');
            $table->string('commercial_neighborhood', 100)->nullable()->after('commercial_complement');
            $table->foreignId('commercial_city_id')->nullable()->after('commercial_neighborhood')->constrained('cities')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropForeign(['commercial_city_id']);
            $table->dropColumn([
                'commercial_same_as_residential',
                'commercial_postal_code',
                'commercial_street',
                'commercial_number',
                'commercial_complement',
                'commercial_neighborhood',
                'commercial_city_id',
            ]);
        });
    }
};
