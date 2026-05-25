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
     * Chamado em: ControladorCliente.php (linha 36)
     */
    public static function aprovadas()
    {
        return self::where('status', 'aprovada')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Busca todas as publicações criadas por um usuário específico.
     * Chamado em: ControladorCliente.php (linha 73)
     */
    public static function porUsuario($usuario_id)
    {
        return self::where('usuario_id', $usuario_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Cria e salva uma nova publicação vinda do formulário do cliente.
     * Chamado em: ControladorCliente.php (linha 57)
     */
    public static function criar($usuario_id, $legenda, $url_imagem)
    {
        return self::create([
            'usuario_id' => $usuario_id,
            'legenda' => $legenda,
            'url_imagem' => $url_imagem,
            'status' => 'pendente' // Fica pendente até o administrador aprovar no painel
        ]);
    }

    /**
     * Sistema de busca por termo/palavra na legenda da publicação.
     * Chamado em: ControladorCliente.php (linha 89)
     */
    public static function buscar($termo)
    {
        return self::where('legenda', 'LIKE', '%' . $termo . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Relacionamento: Uma publicação pertence a um Usuário.
     * Útil caso precise exibir o nome de quem postou usando $publicacao->usuario->nome nas views.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
