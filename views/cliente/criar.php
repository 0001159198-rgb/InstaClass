<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// A BASE_URL inteligente já é definida globalmente pelo public/index.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Publicação - InstaClass</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
    <div class="header">
        <h1>📷 Nova Publicação</h1>
    </div>
    <div class="container">
        <div class="card">
            
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="erro-flash">
                    <?= $_SESSION['erro'] ?>
                    <?php unset($_SESSION['erro']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="mensagem-flash">
                    <?= $_SESSION['mensagem'] ?>
                    <?php unset($_SESSION['mensagem']); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="<?= BASE_URL ?>/publicacoes/salvar">
                <div class="form-group">
                    <label>📝 Legenda</label>
                    <textarea name="legenda" placeholder="O que você está pensando? Use @ para mencionar alguém..." required></textarea>
                    <div class="dica">💡 Dica: Use @nome_usuario para mencionar alguém</div>
                </div>
                <div class="form-group">
                    <label>🖼️ URL da imagem (opcional)</label>
                    <input type="url" name="url_imagem" placeholder="https://exemplo.com/imagem.jpg">
                    <div class="dica">📷 Cole o link de uma imagem da internet</div>
                </div>
                <button type="submit">📤 Publicar</button>
            </form>
            <div class="back">
                <a href="<?= BASE_URL ?>/feed">← Voltar ao feed</a>
            </div>
        </div>
    </div>
</body>
</html>