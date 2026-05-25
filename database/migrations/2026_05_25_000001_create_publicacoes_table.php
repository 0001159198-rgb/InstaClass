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
        Schema::create('publicacoes', function (Blueprint $table) {
            $table->id();
            // Cria a coluna usuario_id e liga com a tabela de usuarios que você já tem
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->text('legenda');
            $table->string('url_imagem')->nullable();
            $table->string('status')->default('pendente'); // pendente, aprovada, bloqueada
            $table->timestamps(); // Cria as colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicacoes');
    }
};
