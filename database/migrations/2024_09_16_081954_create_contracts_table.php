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
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('cpf_cnpj', 14)->unique();
            $table->string('corporate_name')->nullable();
            $table->string('street')->nullable();
            $table->string('number', 50)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('complement')->nullable();
            $table->char('state', 2)->nullable();
            $table->string('activity_branch')->nullable();

            $table->string('name')->nullable(false);
            $table->string('email')->unique();

            $table->enum('contractor_type', ['individual', 'company']);
            $table->integer('company_count');
            $table->integer('user_count')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
