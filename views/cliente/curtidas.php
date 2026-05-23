<?php 
include __DIR__ . '/../layouts/header.php'; 
// A BASE_URL inteligente já é definida globalmente pelo public/index.php
?>

<style>
    .main-content {
        margin-left: 280px !important;
        padding: 20px !important;
    }
    
    .right-panel {
        display: none !important;
    }
    
    .curtidas-container {
        max-width: 700px;
        margin: 0 auto;
    }
    
    .header-page {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #dbdbdb;
    }
    
    .header-page h2 {
        font-size: 28px;
        color: #262626;
        margin-bottom: 8px;
    }
    
    .header-page p {
        color: #8e8e8e;
        font-size: 14px;
    }
    
    .post {
        background: white;
        border: 1px solid #dbdbdb;
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .post-header {
        display: flex;
        align-items: center;
        padding: 14px 16px;
        border-bottom: 1px solid #efefef;
    }
    
    .post-avatar {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        font-weight: bold;
        margin-right: 12px;
    }
    
    .post-info {
        flex: 1;
    }
    
    .post-nome {
        font-size: 14px;
        font-weight: 600;
        color: #262626;
        text-decoration: none;
    }
    
    .post-nome:hover {
        text-decoration: underline;
    }
    
    .post-data {
        font-size: 11px;
        color: #8e8e8e;
        margin-top: 2px;
    }
    
    .post-legenda {
        padding: 14px 16px;
        font-size: 14px;
        line-height: 1.5;
        color: #262626;
        margin: 0;
    }
    
    .post-imagem {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
    }
    
    .post-acoes {
        display: flex;
        gap: 20px;
        padding: 10px 16px;
        border-top: 1px solid #efefef;
    }
    
    .btn-descurtir {
        text-decoration: none;
        color: #8e8e8e;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s;
    }
    
    .btn-descurtir:hover {
        color: #e74c3c;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 30px;
        background: white;
        border: 1px solid #dbdbdb;
        border-radius: 12px;
    }
    
    .empty-state p {
        font-size: 16px;
        color: #8e8e8e;
        margin-bottom: 20px;
    }
    
    .empty-state .heart {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    
    .btn-voltar {
        display: inline-block;
        background: #0095f6;
        color: white;
        padding: 10px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }
    
    .total-info {
        text-align: center;
        margin-top: 20px;
        color: #8e8e8e;
        font-size: 13px;
        padding: 10px;
    }
</style>

<div class="curtidas-container">
    <div class="header-page">
        <h2>❤️ Minhas Curtidas</h2>
        <p>Publicações que você curtiu</p>
    </div>

    <?php if (empty($publicacoes)): ?>
        <div class="empty-state">
            <div class="heart">💔</div>
            <p>Você ainda não curtiu nenhuma publicação.</p>
            <a href="<?= BASE_URL ?>/feed" class="btn-voltar">Explorar publicações</a>
        </div>
    <?php else: ?>
        <?php foreach ($publicacoes as $pub): ?>
            <div class="post">
                <div class="post-header">
                    <div class="post-avatar">
                        <?= strtoupper(substr($pub['autor_nome'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="post-info">
                        <a href="<?= BASE_URL ?>/perfil/<?= $pub['usuario_id'] ?>" class="post-nome">
                            <?= htmlspecialchars($pub['autor_nome'] ?? 'Usuário') ?>
                        </a>
                        <div class="post-data">
                            Curtido em <?= date('d/m/Y \à\s H:i', strtotime($pub['data_curtida'] ?? 'now')) ?>
                        </div>
                    </div>
                </div>
                
                <p class="post-legenda"><?= nl2br(htmlspecialchars($pub['legenda'])) ?></p>
                
                <?php if (!empty($pub['url_imagem'])): ?>
                    <img src="<?= htmlspecialchars($pub['url_imagem']) ?>" class="post-imagem" alt="Publicação" onerror="this.src='<?= BASE_URL ?>/public/assets/img/default.jpg'">
                <?php endif; ?>
                
                <div class="post-acoes">
                    <a href="<?= BASE_URL ?>/publicacoes/<?= $pub['id'] ?>/descurtir" class="btn-descurtir">
                        💔 Descurtir
                    </a>
                    <a href="<?= BASE_URL ?>/perfil/<?= $pub['usuario_id'] ?>" class="btn-descurtir">
                        👤 Ver perfil
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="total-info">
            Total: <?= $totalCurtidas ?? count($publicacoes) ?> publicação(ões) curtida(s)
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>