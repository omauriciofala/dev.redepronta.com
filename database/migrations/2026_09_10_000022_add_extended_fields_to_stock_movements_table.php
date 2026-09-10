<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('document_number', 100)->nullable()->after('document_ref')->comment('Número do Documento (NF, OS, Requisição)');
            $table->date('document_date')->nullable()->after('document_number')->comment('Data de emissão do documento');
            $table->dateTime('movement_date')->nullable()->after('document_date')->comment('Data e hora física da movimentação');
            $table->foreignId('receiver_person_id')->nullable()->after('movement_date')->constrained('people')->onDelete('set null')->comment('Pessoa recebedora dos materiais');
            $table->foreignId('driver_person_id')->nullable()->after('receiver_person_id')->constrained('people')->onDelete('set null')->comment('Pessoa motorista responsável pelo transporte');

            $table->index(['account_id', 'document_number']);
            $table->index(['account_id', 'movement_date']);
        });

        if (!Schema::hasTable('stock_movement_attachments')) {
            Schema::create('stock_movement_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('stock_movement_id')->constrained('stock_movements')->onDelete('cascade');
                $table->string('file_name', 255)->comment('Nome original do arquivo');
                $table->string('file_path', 500)->comment('Caminho no storage');
                $table->string('file_type', 100)->nullable()->comment('MIME type ou extensão');
                $table->unsignedBigInteger('file_size')->nullable()->comment('Tamanho em bytes');
                $table->string('description', 255)->nullable()->comment('Legenda ou descrição do documento/foto');
                $table->timestamp('created_at')->useCurrent();

                $table->index(['account_id', 'stock_movement_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movement_attachments');

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['receiver_person_id']);
            $table->dropForeign(['driver_person_id']);
            $table->dropColumn([
                'document_number',
                'document_date',
                'movement_date',
                'receiver_person_id',
                'driver_person_id',
            ]);
        });
    }
};
