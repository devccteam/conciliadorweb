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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->comment('Tipo da notificação (classe do evento)');
            $table->uuidMorphs('notifiable');
            $table->json('data')->comment('Dados da notificação em formato JSON');
            $table->timestamp('read_at')->nullable()->comment('Timestamp de leitura da notificação (nulo se não lida)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
