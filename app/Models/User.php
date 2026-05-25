<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
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

    // Informa ao Laravel que a senha está no campo "senha"
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // =========================
    // BUSCAS
    // =========================

    public static function buscarPorEmail($email)
    {
        return self::where('email', $email)->first();
    }

    public static function buscarPorId($id)
    {
        return self::find($id);
    }

    public static function emailExiste($email)
    {
        return self::where('email', $email)->exists();
    }

    public static function nomeUsuarioExiste($nome_usuario)
    {
        return self::where('nome_usuario', $nome_usuario)->exists();
    }

    public static function total()
    {
        return self::count();
    }

    public static function todos()
    {
        return self::all();
    }

    // =========================
    // CRIAR USUÁRIO
    // =========================

    public static function criar($nome, $nome_usuario, $email, $senhaHash, $tipo)
    {
        return self::create([
            'nome' => $nome,
            'nome_usuario' => $nome_usuario,
            'email' => $email,
            'senha' => $senhaHash,
            'tipo' => $tipo
        ]);
    }
}
