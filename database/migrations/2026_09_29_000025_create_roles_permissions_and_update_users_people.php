<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela de Papéis de Usuários (Roles) por Tenant
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->string('name', 100);
                $table->string('slug', 100);
                $table->text('description')->nullable();
                $table->boolean('is_system')->default(false);
                $table->timestamps();

                $table->unique(['account_id', 'slug']);
            });
        }

        // 2. Tabela de Permissões Canônicas do Sistema
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('module', 50)->index();
                $table->string('name', 100);
                $table->string('slug', 100)->unique();
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });
        }

        // 3. Tabela Pivot: Permissões de cada Papel
        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
                $table->primary(['role_id', 'permission_id']);
            });
        }

        // 4. Tabela Pivot: Permissões Específicas / Customizadas por Usuário
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
                $table->primary(['user_id', 'permission_id']);
            });
        }

        // 5. Adicionar colunas na tabela users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'person_id')) {
                $table->foreignId('person_id')->nullable()->after('account_id')->constrained('people')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->after('person_id')->constrained('roles')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'is_super_admin')) {
                $table->boolean('is_super_admin')->default(false)->after('password');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('active')->after('is_super_admin');
            }
        });

        // 6. Adicionar coluna is_user na tabela people (Papéis do Cadastro)
        Schema::table('people', function (Blueprint $table) {
            if (!Schema::hasColumn('people', 'is_user')) {
                $table->boolean('is_user')->default(false)->after('is_carrier')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            if (Schema::hasColumn('people', 'is_user')) {
                $table->dropIndex(['is_user']);
                $table->dropColumn('is_user');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
            if (Schema::hasColumn('users', 'person_id')) {
                $table->dropForeign(['person_id']);
                $table->dropColumn('person_id');
            }
            if (Schema::hasColumn('users', 'is_super_admin')) {
                $table->dropColumn('is_super_admin');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
