<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Grupos de Pessoas (Vip, Prestador, Fornecedor Especial, etc.)
        if (!Schema::hasTable('person_groups')) {
            Schema::create('person_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
                $table->string('name', 100);
                $table->text('description')->nullable();
                $table->string('color', 20)->default('#FC6714');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['account_id', 'is_active']);
            });
        }

        // 2. Tabela Canônica de CNAEs
        if (!Schema::hasTable('cnaes')) {
            Schema::create('cnaes', function (Blueprint $table) {
                $table->id();
                $table->string('code', 20)->unique()->comment('Código CNAE formatado ou numérico ex: 6110-8/03');
                $table->string('description', 255);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('code');
            });
        }

        // 3. Tabela Canônica de Bairros
        if (!Schema::hasTable('neighborhoods')) {
            Schema::create('neighborhoods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
                $table->string('name', 100);
                $table->string('zone', 50)->nullable()->comment('Zona urbana/rural (ex: Centro, Norte, Sul)');
                $table->timestamps();

                $table->index(['city_id', 'name']);
            });
        }

        // 4. Vinculação de Grupo na Tabela People
        if (Schema::hasTable('people') && !Schema::hasColumn('people', 'group_id')) {
            Schema::table('people', function (Blueprint $table) {
                $table->foreignId('group_id')->nullable()->after('gender_id')->constrained('person_groups')->onDelete('set null');
                $table->index(['account_id', 'group_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('people') && Schema::hasColumn('people', 'group_id')) {
            Schema::table('people', function (Blueprint $table) {
                $table->dropForeign(['group_id']);
                $table->dropColumn('group_id');
            });
        }

        Schema::dropIfExists('neighborhoods');
        Schema::dropIfExists('cnaes');
        Schema::dropIfExists('person_groups');
    }
};
