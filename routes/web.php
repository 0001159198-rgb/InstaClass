<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControladorCliente;
use App\Http\Controllers\ControladorAdmin;

// Autenticação
Route::get('/login', [ControladorCliente::class, 'showLogin']);
Route::post('/logar', [ControladorCliente::class, 'login']);
Route::get('/logout', [ControladorCliente::class, 'logout']);
Route::get('/registrar', [ControladorCliente::class, 'showRegistro']);
Route::post('/cadastrar', [ControladorCliente::class, 'registrar']);

// Área do Cliente
Route::get('/', [ControladorCliente::class, 'welcome']);
Route::get('/feed', [ControladorCliente::class, 'inicio']);
Route::get('/buscar', [ControladorCliente::class, 'buscarPublicacoes']);

// Área Administrativa
Route::prefix('admin')->group(function () {
    Route::get('/', [ControladorAdmin::class, 'dashboard']);
    Route::get('/usuarios', [ControladorAdmin::class, 'listarUsuarios']);
    Route::get('/denuncias', [ControladorAdmin::class, 'listarDenuncias']);
    
    Route::get('/publicacoes', [ControladorAdmin::class, 'listarPublicacoes']);
    Route::get('/publicacoes/{id}', [ControladorAdmin::class, 'verPublicacao']);
    Route::post('/publicacoes/{id}/aprovar', [ControladorAdmin::class, 'aprovarPublicacao']);
    Route::post('/publicacoes/{id}/bloquear', [ControladorAdmin::class, 'bloquearPublicacao']);
    Route::post('/publicacoes/{id}/excluir', [ControladorAdmin::class, 'excluirPublicacao']);
});

// 🌟 ROTA DE SEED REMOVIDA DO GRUPO ADMIN (Livre de bloqueios inicial)
Route::get('/rodar-seed-temporario', function() {
    $seeder = new \Database\Seeders\DatabaseSeeder();
    $seeder->run();
    return "<br><br>🏁 Processo finalizado.";
});