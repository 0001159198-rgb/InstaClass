<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curtidas', function (Blueprint $table) {
            $table->id();
            // Vincula com a tabela de usuários
            $table->unsignedBigInteger('usuario_id');
            // Vincula com a tabela de publicações e remove em cascata se o post for excluído
            $table->unsignedBigInteger('publicacao_id');
            $table->timestamps();

            // Chaves estrangeiras e índice único para evitar que o mesmo usuário curta 2 vezes o mesmo post
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('publicacao_id')->references('id')->on('publicacoes')->onDelete('cascade');
            $table->unique(['usuario_id', 'publicacao_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curtidas');
    }
};
