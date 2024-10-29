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
        Schema::create('import_records', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('import_id')->constrained()->onDelete('cascade')->comment('ID da importação');
            $table->foreignUuid('import_session_id')->constrained()->onDelete('cascade')->comment('ID da sessão de importação');

            $table->string('num_doc', 255)->nullable()->comment('Número do documento');
            $table->date('date')->nullable()->comment('Data do registro (Campo obrigatório)');
            $table->text('history')->nullable()->comment('Histórico (Campo obrigatório)');
            $table->decimal('debit_value', 15, 2)->nullable()->comment('Valor débito (saída)');
            $table->decimal('credit_value', 15, 2)->nullable()->comment('Valor crédito (entrada)');
            $table->decimal('interest', 15, 2)->nullable()->comment('Juros');
            $table->decimal('fine', 15, 2)->nullable()->comment('Multa');
            $table->decimal('discounts', 15, 2)->nullable()->comment('Descontos');
            $table->decimal('other_values', 15, 2)->nullable()->comment('Outros valores');
            $table->string('client_supplier', 255)->nullable()->comment('Cliente/Fornecedor');
            $table->string('bank', 255)->nullable()->comment('Banco');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_records');
    }
};
