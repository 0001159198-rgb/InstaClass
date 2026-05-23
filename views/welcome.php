<?php 
// A BASE_URL inteligente já é definida globalmente pelo public/index.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InstaClass - Bem-vindo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .welcome-container {
            text-align: center;
            padding: 40px;
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            font-size: 80px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 48px;
            color: white;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .subtitle {
            font-size: 18px;
            color: rgba(255,255,255,0.9);
            margin-bottom: 40px;
        }

        .features {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        .feature-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px;
            width: 200px;
            text-align: center;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            color: white;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: rgba(255,255,255,0.8);
            font-size: 13px;
        }

        .buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 32px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background: white;
            color: #667eea;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
        }

        .footer {
            margin-top: 50px;
            color: rgba(255,255,255,0.6);
            font-size: 12px;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 32px;
            }
            .features {
                gap: 15px;
            }
            .feature-card {
                width: 160px;
                padding: 15px;
            }
            .btn {
                padding: 10px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="logo">📷</div>
        <h1>InstaClass</h1>
        <div class="subtitle">Compartilhe momentos, conecte-se com amigos</div>
        
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>Feed Social</h3>
                <p>Compartilhe suas publicações</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">❤️</div>
                <h3>Curtidas</h3>
                <p>Interaja com os amigos</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3>Busca</h3>
                <p>Encontre perfis e publicações</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👑</div>
                <h3>Admin</h3>
                <p>Área administrativa completa</p>
            </div>
        </div>
        
        <div class="buttons">
            <a href="<?= BASE_URL ?>/login" class="btn btn-primary">🔐 Fazer Login</a>
            <a href="<?= BASE_URL ?>/registrar" class="btn btn-secondary">📝 Criar Conta</a>
        </div>
        
        <div class="footer">
            <p>© 2024 InstaClass - Rede social para compartilhar momentos especiais</p>
        </div>
    </div>
</body>
</html>