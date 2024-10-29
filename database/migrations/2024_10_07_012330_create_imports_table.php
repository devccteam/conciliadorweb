<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255)->nullable(false)->comment('Nome da importação');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade')->comment('Usuário que realizou a importação');
            $table->foreignUuid('layout_id')->constrained()->onDelete('cascade')->comment('ID do layout de importação');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->comment('Status da importação');
            $table->integer('total_files')->nullable(false)->comment('Total de arquivos importados');
            $table->text('error_message')->nullable()->comment('Mensagem de erro da importação');
            $table->foreignUuid('contract_id')->constrained()->onDelete('cascade')->comment('ID do contrato');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
