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

// Rota de Perfil flexível protegendo contra 404 automáticos
Route::get('/perfil/{id?}', [ControladorCliente::class, 'listarPublicacoesUsuario'])->where('id', '[0-9]+');

// Rotas para criação de publicações com mapeamento isolado para não quebrar o CSS
Route::get('/publicacoes/criar', [ControladorCliente::class, 'criarPublicacao']);
Route::post('/publicacoes/salvar', [ControladorCliente::class, 'salvarPublicacao']);

// Rotas de interações (Curtidas e Denúncias)
Route::get('/publicacoes/{id}/curtir', [ControladorCliente::class, 'curtirPublicacao']);
Route::get('/publicacoes/{id}/descurtir', [ControladorCliente::class, 'descurtirPublicacao']);
Route::post('/publicacoes/{id}/denunciar', [ControladorCliente::class, 'denunciarPublicacao']);

// Rota de listagem das curtidas com redirecionamento amigável
Route::get('/minhas-curtidas', [ControladorCliente::class, 'minhasCurtidas']);
Route::redirect('/curtidas', '/minhas-curtidas');


// ================== ÁREA ADMINISTRATIVA ==================
Route::prefix('admin')->group(function () {
    Route::get('/', [ControladorAdmin::class, 'dashboard']);
    Route::get('/usuarios', [ControladorAdmin::class, 'listarUsuarios']);
    Route::get('/denuncias', [ControladorAdmin::class, 'listarDenuncias']);
    
    Route::get('/publicacoes', [ControladorAdmin::class, 'listarPublicacoes']);
    Route::get('/publicacoes/{id}', [ControladorAdmin::class, 'verPublicacao'])->where('id', '[0-9]+');
    
    // ✅ CORREÇÃO CRUCIAL: 'Route::any' garante suporte total a requisições GET ou POST antigas vindas da View
    Route::any('/publicacoes/{id}/aprovar', [ControladorAdmin::class, 'aprovarPublicacao'])->where('id', '[0-9]+');
    Route::any('/publicacoes/{id}/bloquear', [ControladorAdmin::class, 'bloquearPublicacao'])->where('id', '[0-9]+');
    Route::any('/publicacoes/{id}/excluir', [ControladorAdmin::class, 'excluirPublicacao'])->where('id', '[0-9]+');

    // 🔥 FIX: Sintaxe da linha de análise fechada perfeitamente agora!
    Route::any('/denuncias/{id}/analisar', [ControladorAdmin::class, 'analisarDenuncia'])->where('id', '[0-9]+');
});


// ================== UTILITÁRIOS DE SISTEMA ==================
Route::get('/clear-cache', function () {
    // Força a limpeza absoluta de caches físicos no servidor Render
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    
    // 🔥 INJEÇÃO DE DADOS: Força a execução do DatabaseSeeder dentro do banco de dados de produção do Render
    Artisan::call('db:seed', ['--force' => true]);
    
    return "🧹 Todos os caches foram limpos e os usuários do Seeder foram semeados com sucesso!";
});
