<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ControladorCliente;
use App\Http\Controllers\ControladorAdmin;

// ================== AUTENTICAÇÃO ==================
Route::get('/login', [ControladorCliente::class, 'showLogin']);
Route::post('/logar', [ControladorCliente::class, 'login']);
Route::get('/logout', [ControladorCliente::class, 'logout']);
Route::get('/registrar', [ControladorCliente::class, 'showRegistro']);
Route::post('/cadastrar', [ControladorCliente::class, 'registrar']);

// ================== ÁREA DO CLIENTE ==================
Route::get('/', [ControladorCliente::class, 'welcome']);
Route::get('/feed', [ControladorCliente::class, 'inicio']);
Route::get('/buscar', [ControladorCliente::class, 'buscarPublicacoes']);

// 🚨 CORREÇÃO: Rota de Perfil que estava faltando (Aceita /perfil ou /perfil/9)
Route::get('/perfil/{id?}', [ControladorCliente::class, 'listarPublicacoesUsuario'])->where('id', '[0-9]+');

// 🚨 CORREÇÃO: Rotas de criação de posts que estavam faltando para a conta normal funcionar
Route::get('/publicacoes/criar', [ControladorCliente::class, 'criarPublicacao']);
Route::post('/publicacoes/salvar', [ControladorCliente::class, 'salvarPublicacao']);

// 🚨 CORREÇÃO: Rotas de Curtidas e Denúncias essenciais para as interações
Route::get('/publicacoes/{id}/curtir', [ControladorCliente::class, 'curtirPublicacao']);
Route::get('/publicacoes/{id}/descurtir', [ControladorCliente::class, 'descurtirPublicacao']);
Route::get('/minhas-curtidas', [ControladorCliente::class, 'minhasCurtidas']);
Route::post('/publicacoes/{id}/denunciar', [ControladorCliente::class, 'denunciarPublicacao']);


// ================== ÁREA ADMINISTRATIVA ==================
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


// ================== UTILITÁRIOS DE SISTEMA ==================
Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return nl2br(Artisan::output());
});

Route::get('/rodar-seed-temporario', function() {
    try {
        $seeder = new \Database\Seeders\DatabaseSeeder();
        $seeder->run();
        return "⚡ Seed executado com sucesso!<br><br>🏁 Processo finalizado.";
    } catch (\Exception $e) {
        return "❌ Erro ao rodar Seed: " . $e->getMessage();
    }
});
