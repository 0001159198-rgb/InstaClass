<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Suporte para as Seeds
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable; // Adicionado Notifiable por boa prática de Auth

    protected $table = 'usuarios';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'nome_usuario',
        'email',
        'senha',
        'tipo'
    ];

    protected $hidden = [
        'senha'
    ];

    /**
     * Informa ao Laravel que a coluna da senha no banco se chama "senha" (e não "password")
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // =========================================================
    // RELACIONAMENTOS (🚨 CRÍTICO: Resolve falhas de perfil/posts)
    // =========================================================

    /**
     * Relacionamento: Um Usuário possui muitas Publicações.
     */
    public function publicacoes()
    {
        return $this->hasMany(Publicacao::class, 'usuario_id');
    }

    // =========================
    // BUSCAS
    // =========================

    public static function buscarPorEmail($email)
    {
        return self::where('email', trim($email))->first();
    }

    public static function buscarPorId($id)
    {
        return self::find($id);
    }

    public static function emailExiste($email)
    {
        return self::where('email', trim($email))->exists();
    }

    public static function nomeUsuarioExiste($nome_usuario)
    {
        return self::where('nome_usuario', trim($nome_usuario))->exists();
    }

    public static function total()
    {
        return self::count();
    }

    public static function todos()
    {
        return self::orderBy('nome', 'asc')->get();
    }

    // =========================
    // CRIAR USUÁRIO
    // =========================

    public static function criar($nome, $nome_usuario, $email, $senhaHash, $tipo)
    {
        return self::create([
            'nome' => trim($nome),
            'nome_usuario' => trim($nome_usuario),
            'email' => trim($email),
            'senha' => $senhaHash,
            'tipo' => $tipo
        ]);
    }
}
