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
        Schema::create('layouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('code')->unique();
            
            $table->string('name')->nullable(false)->comment('Nome do layout');
            $table->string('format', 50)->nullable(false)->default('Excel')->comment('Formato do layout (ex: Excel, PDF, TXT)');
            $table->enum('sector', ['Contábil', 'Fiscal'])->nullable(false)->comment('Setor do layout');
            $table->enum('movement_type', ['Ambos', 'Pagar', 'Receber'])->nullable(false)->comment('Tipo de movimento');
            $table->integer('start_row')->nullable(false)->default(1)->comment('Linha inicial');
            
            $table->string('num_doc_column', 10)->nullable()->comment('Coluna do número do documento');
            $table->string('parcel_separator', 10)->nullable()->comment('Coluna ou separador de parcela');
            $table->string('date_column', 10)->nullable()->comment('Coluna da data (Campo obrigatório)');
            $table->string('history_column', 10)->nullable()->comment('Coluna do histórico (Campo obrigatório)');
            $table->string('history_2_lines_column', 10)->nullable()->comment('Coluna do histórico em 2 linhas');
            $table->string('debit_value_column', 10)->nullable()->comment('Coluna do valor débito (saída)');
            $table->string('credit_value_column', 10)->nullable()->comment('Coluna do valor crédito (entrada)');
            $table->string('interest_column', 10)->nullable()->comment('Coluna de juros');
            $table->string('fine_column', 10)->nullable()->comment('Coluna de multa');
            $table->string('discounts_column', 10)->nullable()->comment('Coluna de descontos');
            $table->string('other_values_column', 10)->nullable()->comment('Coluna de outros valores');
            $table->string('ignore_history')->nullable()->comment('Palavras-chave para ignorar no histórico');
            $table->string('client_supplier_column', 10)->nullable()->comment('Coluna do cliente/fornecedor');
            $table->string('debit_credit_column', 10)->nullable()->comment('Coluna que identifica D/C');
            $table->string('bank_column', 10)->nullable()->comment('Coluna do banco');

            $table->boolean('consider_previous_date')->default(false)->comment('Considerar data anterior ao encontrar em branco');
            $table->boolean('consider_previous_client_supplier')->default(false)->comment('Considerar cliente/fornecedor anterior ao encontrar em branco');
            $table->boolean('consider_previous_history')->default(false)->comment('Considerar histórico anterior ao encontrar em branco');
            $table->boolean('consider_previous_bank')->default(false)->comment('Considerar banco anterior ao encontrar em branco');
            $table->boolean('is_default_layout')->default(false)->comment('Indica se o layout é o padrão contábil');

            $table->foreignUuid('contract_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });

        DB::statement('CREATE SEQUENCE layouts_code_seq START WITH 10001;');

        DB::statement('ALTER TABLE layouts ALTER COLUMN code SET DEFAULT nextval(\'layouts_code_seq\');');

        DB::statement('ALTER SEQUENCE layouts_code_seq OWNED BY layouts.code;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layouts');
    }
};
