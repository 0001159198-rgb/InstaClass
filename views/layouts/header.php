<?php
// Iniciar sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Garante que a BASE_URL está definida
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host . '/RedeSocial---Ead7';
} else {
    $baseUrl = BASE_URL;
}

// Verifica se a URL atual contém 'admin'
$isAdmin = false;
if (isset($_GET['url'])) {
    $currentUrl = $_GET['url'];
    if (strpos($currentUrl, 'admin') !== false) {
        $isAdmin = true;
    }
}

// Pega os dados da sessão
$usuarioId = $_SESSION['usuario_id'] ?? 0;
$usuarioNome = $_SESSION['usuario_nome'] ?? 'Visitante';
$usuarioTipo = $_SESSION['usuario_tipo'] ?? 'cliente';

// Se não estiver logado, redireciona para login (exceto nas páginas de auth)
$paginasPermitidas = ['login', 'logar', 'registrar', 'cadastrar'];
$rotaAtual = $_GET['url'] ?? '';

if ($usuarioId == 0 && !in_array($rotaAtual, $paginasPermitidas) && $rotaAtual != '') {
    header('Location: ' . $baseUrl . '/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InstaClass</title>
    
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/public/css/style.css">
    
    <?php if ($isAdmin): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/public/css/admin.css">
    <?php endif; ?>
    
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
        <a href="<?= htmlspecialchars($baseUrl) ?>/feed" class="menu-item">
            <span>🏠</span> <span class="menu-text">Início</span>
        </a>

        <a href="<?= htmlspecialchars($baseUrl) ?>/buscar" class="menu-item">
            <span>🔍</span> <span class="menu-text">Explorar</span>
        </a>

        <a href="<?= htmlspecialchars($baseUrl) ?>/curtidas" class="menu-item">
            <span>❤️</span> <span class="menu-text">Curtidas</span>
        </a>

        <?php if ($usuarioId > 0): ?>
        <a href="<?= htmlspecialchars($baseUrl) ?>/perfil/<?= $usuarioId ?>" class="menu-item">
            <span>👤</span> <span class="menu-text">Meu Perfil</span>
        </a>
        <?php else: ?>
        <a href="<?= htmlspecialchars($baseUrl) ?>/login" class="menu-item">
            <span>👤</span> <span class="menu-text">Entrar</span>
        </a>
        <?php endif; ?>

        <?php if ($isAdmin || $usuarioTipo == 'admin'): ?>
        <a href="<?= htmlspecialchars($baseUrl) ?>/admin" class="menu-item">
            <span>⚙️</span> <span class="menu-text">Admin</span>
        </a>
        <?php endif; ?>
    </nav>

    <div>
        <a href="<?= htmlspecialchars($baseUrl) ?>/publicacoes/criar" class="btn-nova">
            ➕ <span class="menu-text">Nova publicação</span>
        </a>
    </div>

    <div class="user-info">
        <?php if ($usuarioId > 0): ?>
            <strong><?= htmlspecialchars($usuarioNome) ?></strong><br>
            <a href="<?= htmlspecialchars($baseUrl) ?>/logout" style="font-size: 12px; color: #8e8e8e; text-decoration: none;">Sair</a>
        <?php else: ?>
            <strong>Visitante</strong><br>
            <a href="<?= htmlspecialchars($baseUrl) ?>/login" style="font-size: 12px; color: #0095f6; text-decoration: none;">Fazer login</a>
        <?php endif; ?>
    </div>
</div>

<div class="main-content">