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

// Rota de Perfil flexível (Aceita /perfil ou /perfil/9) protegendo contra 404 automáticos
Route::get('/perfil/{id?}', [ControladorCliente::class, 'listarPublicacoesUsuario'])->where('id', '[0-9]+');

// Rotas para criação de publicações com mapeamento isolado para não quebrar o CSS
Route::get('/publicacoes/criar', [ControladorCliente::class, 'criarPublicacao']);
Route::post('/publicacoes/salvar', [ControladorCliente::class, 'salvarPublicacao']);

// Rotas de interações (Curtidas e Denúncias)
Route::get('/publicacoes/{id}/curtir', [ControladorCliente::class, 'curtirPublicacao']);
Route::get('/publicacoes/{id}/descurtir', [ControladorCliente::class, 'descurtirPublicacao']);
Route::post('/publicacoes/{id}/denunciar', [ControladorCliente::class, 'denunciarPublicacao']);

// Rota de listagem das curtidas do utilizador logado com redirecionamento amigável
Route::get('/minhas-curtidas', [ControladorCliente::class, 'minhasCurtidas']);
Route::redirect('/curtidas', '/minhas-curtidas');


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
        return "⚡ Seed executado com sucesso e novas contas de exemplo criadas!<br><br>🏁 Processo finalizado.";
    } catch (\Exception $e) {
        return "❌ Erro ao rodar Seed: " . $e->getMessage();
    }
});
