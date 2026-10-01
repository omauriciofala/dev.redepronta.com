<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->string('name', 150);
                $table->string('code', 50)->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'is_active']);
                $table->index(['account_id', 'name']);
            });
        }

        if (!Schema::hasTable('ticket_categories')) {
            Schema::create('ticket_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'department_id', 'is_active'], 'idx_tk_cat_dept_active');
            });
        }

        if (!Schema::hasTable('ticket_reasons')) {
            Schema::create('ticket_reasons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('restrict');
                $table->string('name', 150);
                $table->enum('default_priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('MEDIUM');
                $table->unsignedInteger('default_sla_hours')->default(24);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'category_id', 'is_active'], 'idx_tk_rsn_cat_active');
                $table->index(['account_id', 'default_priority']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_reasons');
        Schema::dropIfExists('ticket_categories');
        Schema::dropIfExists('departments');
    }
};
