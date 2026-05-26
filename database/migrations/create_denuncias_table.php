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
        Schema::create('denuncias', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento com a publicação denunciada
            $table->unsignedBigInteger('publicacao_id');
            
            // Relacionamento com o usuário que denunciou (0 se for anônimo)
            $table->unsignedBigInteger('usuario_id')->default(0);
            
            // Dados da denúncia coletados do formulário
            $table->string('motivo'); // Ex: 'Conteúdo impróprio', 'Discurso de ódio'
            $table->string('gravidade')->default('media'); // 'baixa', 'media', 'alta'
            
            $table->timestamps();

            // Configuração das Chaves Estrangeiras (Foreign Keys)
            // Se a publicação for excluída, as denúncias vinculadas a ela somem automaticamente
            $table->foreign('publicacao_id')
                  ->references('id')
                  ->on('publicacoes')
                  ->onDelete('cascade');
                  
            // Nota: Não amarramos foreign key rígida no usuario_id para permitir denúncias 
            // vindas de usuários deslogados/visitantes (onde gravamos id como 0)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};
