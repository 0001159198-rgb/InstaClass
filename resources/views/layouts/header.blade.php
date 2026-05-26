<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InstaClass</title>
    
    {{-- CSS Geral usando o helper seguro asset() --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    {{-- Injeta o CSS do painel administrativo se a URL atual contiver 'admin' --}}
    @if (str_contains(request()->url(), 'admin'))
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endif
    
    <style>
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #fafafa; }
        .sidebar { width: 250px; position: fixed; height: 100%; border-right: 1px solid #dbdbdb; background: white; z-index: 100; display: flex; flex-direction: column; }
        .logo h2 { padding: 20px; color: #333; margin: 0; }
        .menu-lateral { flex-grow: 1; }
        .menu-item { display: flex; align-items: center; padding: 12px 20px; text-decoration: none; color: #262626; transition: background 0.2s; }
        .menu-item:hover { background-color: #fafafa; }
        .menu-item span { margin-right: 12px; font-size: 1.2rem; }
        .btn-nova { display: block; background-color: #0095f6; color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold; margin: 20px; }
        .btn-nova:hover { background-color: #0077cc; }
        .user-info { padding: 20px; border-top: 1px solid #dbdbdb; margin-top: auto; font-size: 14px; }
        .main-content { margin-left: 250px; padding: 20px; min-height: 100vh; background: #fafafa; box-sizing: border-box; }
        
        /* Cabeçalho superior para o celular (escondido no PC) */
        .mobile-top-bar { display: none; }

        /* =======================================================
           📱 RESOLUÇÃO DO SEU PRINT: MUDANÇAS PARA CELULAR
           ======================================================= */
        @media (max-width: 768px) {
            /* 1. Faz o conteúdo principal ocupar 100% da largura do celular */
            .main-content { 
                margin-left: 0 !important; 
                padding: 15px;
                padding-bottom: 80px; /* Margem inferior para o menu não cobrir nada */
                width: 100% !important;
            }

            /* 2. Transforma a barra lateral em uma barra inferior estilo app */
            .sidebar {
                width: 100% !important;
                height: 60px !important;
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                top: auto !important;
                border-right: none !important;
                border-top: 1px solid #dbdbdb !important;
                flex-direction: row !important; /* Itens um ao lado do outro */
                justify-content: space-around !important;
                align-items: center !important;
                background: white !important;
                padding: 0 !important;
            }

            /* Esconde o título, as informações do usuário e o botão de texto no celular */
            .logo, .user-info, .menu-text { 
                display: none !important; 
            }

            /* Organiza os links do menu em linha horizontal */
            .menu-lateral {
                display: flex !important;
                width: 100% !important;
                justify-content: space-around !important;
                align-items: center !important;
                height: 100% !important;
            }

            .menu-item {
                padding: 10px !important;
                justify-content: center !important;
                flex: 1 !important;
            }

            .menu-item span {
                margin-right: 0 !important; /* Centraliza o emoji */
                font-size: 24px !important; /* Aumenta o tamanho do ícone para o toque */
            }

            /* Estiliza o botão de "+" para ficar compacto na barra inferior */
            .btn-nova {
                margin: 0 !important;
                padding: 10px !important;
                background: transparent !important;
                color: #262626 !important;
                font-size: 24px !important;
            }

            /* Ativa um topo fixo discreto com o nome do app no celular */
            .mobile-top-bar {
                display: flex;
                align-items: center;
                justify-content: center;
                background: white;
                height: 48px;
                border-bottom: 1px solid #dbdbdb;
                font-weight: bold;
                font-size: 18px;
                position: sticky;
                top: 0;
                z-index: 999;
            }
        }
    </style>
</head>
<body>

{{-- Topo sutil visível apenas em telas mobile --}}
<div class="mobile-top-bar">
    📷 InstaClass
</div>

<div class="sidebar">
    <div class="logo">
        <h2>📷 InstaClass</h2>
    </div>

    <nav class="menu-lateral">
        <a href="{{ url('/feed') }}" class="menu-item">
            <span>🏠</span> <span class="menu-text">Início</span>
        </a>

        <a href="{{ url('/buscar') }}" class="menu-item">
            <span>🔍</span> <span class="menu-text">Explorar</span>
        </a>

        <a href="{{ url('/curtidas') }}" class="menu-item">
            <span>❤️</span> <span class="menu-text">Curtidas</span>
        </a>

        {{-- Botão de "+" movido para dentro do fluxo do menu para alinhar no celular --}}
        <a href="{{ url('/publicacoes/criar') }}" class="btn-nova" title="Nova Publicação">
            <span>➕</span> <span class="menu-text">Nova publicação</span>
        </a>

        {{-- Tratamento do Perfil dinâmico baseado no Auth do Laravel --}}
        @if (auth()->check())
            <a href="{{ url('/perfil/' . auth()->id()) }}" class="menu-item">
                <span>👤</span> <span class="menu-text">Meu Perfil</span>
            </a>
        @else
            <a href="{{ url('/login') }}" class="menu-item">
                <span>👤</span> <span class="menu-text">Entrar</span>
            </a>
        @endif

        {{-- Proteção visual para exibir o botão Admin apenas para administradores --}}
        @if (auth()->check() && (str_contains(request()->url(), 'admin') || auth()->user()->tipo === 'admin'))
            <a href="{{ url('/admin') }}" class="menu-item">
                <span>⚙️</span> <span class="menu-text">Admin</span>
            </a>
        @endif
    </nav>

    <div class="user-info">
        @if (auth()->check())
            <strong>{{ auth()->user()->nome }}</strong><br>
            <a href="{{ url('/logout') }}" style="font-size: 12px; color: #8e8e8e; text-decoration: none;">Sair</a>
        @else
            <strong>Visitante</strong><br>
            <a href="{{ url('/login') }}" style="font-size: 12px; color: #0095f6; text-decoration: none;">Fazer login</a>
        @endif
    </div>
</div>

<div class="main-content">
