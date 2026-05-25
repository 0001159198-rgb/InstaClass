<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
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

    // =========================
    // BUSCAS
    // =========================

    public static function buscarPorEmail($email)
    {
        return DB::table('usuarios')->where('email', $email)->first();
    }

    public static function buscarPorId($id)
    {
        return DB::table('usuarios')->where('id', $id)->first();
    }

    public static function emailExiste($email)
    {
        return DB::table('usuarios')->where('email', $email)->exists();
    }

    public static function nomeUsuarioExiste($nome_usuario)
    {
        return DB::table('usuarios')->where('nome_usuario', $nome_usuario)->exists();
    }

    public static function total()
    {
        return DB::table('usuarios')->count();
    }

    public static function todos()
    {
        return DB::table('usuarios')->get();
    }

    // =========================
    // CRIAR USUÁRIO
    // =========================

    public static function criar($nome, $nome_usuario, $email, $senhaHash, $tipo)
    {
        return DB::table('usuarios')->insert([
            'nome' => $nome,
            'nome_usuario' => $nome_usuario,
            'email' => $email,
            'senha' => $senhaHash,
            'tipo' => $tipo
        ]);
    }
}
