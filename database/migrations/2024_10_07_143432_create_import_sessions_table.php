<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('import_id')->constrained()->onDelete('cascade')->comment('ID da importação');
            $table->string('file_name')->nullable(false)->comment('Nome do arquivo importado');
            $table->integer('size')->nullable()->comment('Tamanho do arquivo importado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_sessions');
    }
};
