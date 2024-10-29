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
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('cpf_cnpj', 14)->unique();
            $table->string('corporate_name')->nullable();
            $table->string('street')->nullable();
            $table->string('number', 50)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('complement')->nullable();
            $table->char('state', 2)->nullable();
            $table->string('activity_branch')->nullable();
            
            $table->boolean('has_observations')->default(0);
            $table->boolean('require_justification')->default(0);
            $table->boolean('has_checked_field')->default(0);
            $table->enum('tax_regime', ['Lucro Real', 'Lucro Presumido', 'Simples Nacional', 'Outros']);
            $table->integer('layout_reason_id')->nullable();
            $table->integer('layout_financial_id')->nullable();
            $table->boolean('require_approver')->default(0);
            $table->foreignUuid('approver_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('approx_value_enabled')->default(0);
            $table->decimal('approx_value_percentage', 5, 2)->nullable();
            $table->boolean('has_fixed_account')->default(0);
            $table->string('active_interest_account', 50)->nullable();
            $table->string('passive_interest_account', 50)->nullable();
            $table->string('discounts_obtained_account', 50)->nullable();
            $table->string('discounts_given_account', 50)->nullable();
            $table->boolean('require_documents')->default(0);

            $table->foreignUuid('contract_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
