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
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .sidebar { width: 250px; position: fixed; height: 100%; border-right: 1px solid #dbdbdb; background: white; z-index: 100; }
        .logo h2 { padding: 20px; color: #333; margin: 0; }
        .menu-item { display: flex; align-items: center; padding: 12px 20px; text-decoration: none; color: #262626; transition: background 0.2s; }
        .menu-item:hover { background-color: #fafafa; }
        .menu-item span { margin-right: 12px; font-size: 1.2rem; }
        .btn-nova { display: block; background-color: #0095f6; color: white; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold; margin: 20px; }
        .btn-nova:hover { background-color: #0077cc; }
        .user-info { padding: 20px; border-top: 1px solid #dbdbdb; margin-top: 20px; font-size: 14px; }
        .main-content { margin-left: 250px; padding: 20px; min-height: 100vh; background: #fafafa; }
        @media (max-width: 768px) { .sidebar { width: 80px; } .sidebar span:not(.emoji) { display: none; } .main-content { margin-left: 80px; } .btn-nova span { display: none; } }
    </style>
</head>
<body>

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

    <div>
        <a href="{{ url('/publicacoes/criar') }}" class="btn-nova">
            ➕ <span class="menu-text">Nova publicação</span>
        </a>
    </div>

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