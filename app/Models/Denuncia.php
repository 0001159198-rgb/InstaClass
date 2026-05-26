<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Denuncia extends Model
{
    use HasFactory;

    // 🗄️ Garante que o Laravel use a tabela correta no banco de dados do Render
    protected $table = 'denuncias';

    // 🔒 Campos autorizados para gravação e manipulação de dados
    protected $fillable = [
        'publicacao_id',
        'usuario_id',
        'motivo',
        'status', // Ex: 'pendente', 'analisado'
    ];

    /**
     * 🛡️ Escopo estático essencial para salvar a linha 43 do seu ControladorAdmin.
     * Isso faz o método Denuncia::recentes(5) funcionar e trazer os dados do painel.
     */
    public static function recentes($quantidade = 5)
    {
        return self::orderBy('created_at', 'desc')
            ->take($quantidade)
            ->get();
    }

    /**
     * 👥 Relacionamento: Uma denúncia pertence a um Usuário (Autor da denúncia)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * 📷 Relacionamento: Uma denúncia aponta para uma Publicação específica
     */
    public function publicacao()
    {
        return $this->belongsTo(Publicacao::class, 'publicacao_id');
    }
}
