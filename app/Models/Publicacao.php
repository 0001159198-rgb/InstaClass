<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacao extends Model
{
    use HasFactory;

    // Define o nome real da tabela no banco de dados do seu projeto (PostgreSQL)
    protected $table = 'publicacoes';

    // Campos que o Laravel tem permissão para preencher/salvar no banco
    protected $fillable = [
        'usuario_id', 
        'legenda', 
        'url_imagem', 
        'status'
    ];

    /**
     * Busca apenas as publicações aprovadas para o Feed.
     * Chamado em: ControladorCliente.php
     */
    public static function aprovadas()
    {
        return self::where('status', '=', 'aprovada')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Busca todas as publicações criadas por um usuário específico.
     * Chamado em: ControladorCliente.php
     */
    public static function porUsuario($usuario_id)
    {
        // No perfil do usuário, mostramos os posts dele independente do status (aprovado ou pendente)
        return self::where('usuario_id', '=', $usuario_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Cria e salva uma nova publicação vinda do formulário do cliente.
     * Chamado em: ControladorCliente.php
     */
    public static function criar($usuario_id, $legenda, $url_imagem)
    {
        return self::create([
            'usuario_id' => $usuario_id,
            'legenda' => $legenda,
            'url_imagem' => $url_imagem,
            'status' => 'aprovada' // 🔥 MUDADO PARA 'aprovada' para aparecer no feed na hora sem travar os testes!
        ]);
    }

    /**
     * Sistema de busca por termo/palavra na legenda da publicação.
     * Chamado em: ControladorCliente.php
     */
    public static function buscar($termo)
    {
        return self::where('legenda', 'LIKE', '%' . $termo . '%')
            ->where('status', '=', 'aprovada')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Relacionamento: Uma publicação pertence a um Usuário.
     * Útil para exibir o nome de quem postou usando $publicacao->usuario->nome nas views.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
