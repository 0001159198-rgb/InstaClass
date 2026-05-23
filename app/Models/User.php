<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios'; // Altere para o nome real da sua tabela se não for 'usuarios'

    protected $fillable = ['nome', 'nome_usuario', 'email', 'senha', 'tipo'];

    public $timestamps = false; // Desativa as colunas created_at e updated_at se você não as tiver na tabela

    // ========== MÉTODOS PERSONALIZADOS QUE SEU CONTROLADOR EXIGE ==========

    public static function buscarPorEmail($email) {
        // Busca usando o Query Builder do Laravel
        $usuario = DB::table('usuarios')->where('email', $email)->first();
        return $usuario ? (array) $usuario : null;
    }

    public static function buscarPorId($id) {
        $usuario = DB::table('usuarios')->where('id', $id)->first();
        return $usuario ? (array) $usuario : null;
    }

    public static function emailExiste($email) {
        return DB::table('usuarios')->where('email', $email)->exists();
    }

    public static function nomeUsuarioExiste($nome_usuario) {
        return DB::table('usuarios')->where('nome_usuario', $nome_usuario)->exists();
    }

    public static function total() {
        return DB::table('usuarios')->count();
    }

    public static function todos() {
        return DB::table('usuarios')->get()->toArray();
    }

    public static function criar($nome, $nome_usuario, $email, $senhaHash, $tipo) {
        return DB::table('usuarios')->insert([
            'nome' => $nome,
            'nome_usuario' => $nome_usuario,
            'email' => $email,
            'senha' => $senhaHash,
            'tipo' => $tipo
        ]);
    }
}