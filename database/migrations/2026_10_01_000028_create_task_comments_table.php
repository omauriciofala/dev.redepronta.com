<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('task_comments')) {
            Schema::create('task_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('task_id')->constrained('tasks')->onDelete('restrict');
                $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
                $table->text('comment');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'task_id']);
                $table->index(['task_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};
