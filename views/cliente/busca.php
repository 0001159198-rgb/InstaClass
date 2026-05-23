<?php 
include __DIR__ . '/../layouts/header.php'; 
// A BASE_URL inteligente já é definida globalmente pelo public/index.php
?>

<div class="busca-container">
    <div class="busca-header">
        <h2>🔎 Buscar Publicações e Perfis</h2>
    </div>
    
    <form method="GET" action="<?= BASE_URL ?>/buscar" class="search-form">
        <input type="text" 
               name="q" 
               class="search-input"
               placeholder="Buscar por legenda, nome ou use @ para buscar perfis (ex: @admin)" 
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
               autofocus>
        <button type="submit" class="search-button">🔍 Buscar</button>
    </form>
    
    <?php if (!empty($_GET['q'])): ?>
        <div class="result-info">
            <p>
                Resultados para: <strong>"<?= htmlspecialchars($_GET['q']) ?>"</strong>
                (<?= count($publicacoes) ?> resultado(s) encontrado(s))
            </p>
        </div>
    <?php endif; ?>
    
    <?php if (empty($publicacoes)): ?>
        <div class="empty-state">
            <p>📭 Nenhum resultado encontrado.</p>
            <?php if (!empty($_GET['q'])): ?>
                <p style="margin-top: 10px;">Tente buscar por outra palavra ou use @ para buscar perfis.</p>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/publicacoes/criar" class="btn-criar">➕ Criar publicação</a>
        </div>
    <?php else: ?>
        
        <?php 
        // Separar perfis e publicações
        $perfis = array_filter($publicacoes, function($item) {
            return isset($item['tipo']) && $item['tipo'] == 'perfil';
        });
        $posts = array_filter($publicacoes, function($item) {
            return !isset($item['tipo']) || $item['tipo'] != 'perfil';
        });
        ?>
        
        <?php if (!empty($perfis)): ?>
            <div class="result-section">
                <h3>👥 Perfis Encontrados</h3>
                <?php foreach ($perfis as $perfil): ?>
                    <div class="perfil-result">
                        <div class="perfil-avatar-mini">
                            <?= strtoupper(substr($perfil['nome'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="perfil-info">
                            <a href="<?= BASE_URL ?>/perfil/<?= $perfil['id'] ?>" class="perfil-nome">
                                <?= htmlspecialchars($perfil['nome']) ?>
                            </a>
                            <div class="perfil-username">
                                @<?= htmlspecialchars($perfil['nome_usuario']) ?>
                            </div>
                            <div class="perfil-stats">
                                📧 <?= htmlspecialchars($perfil['email']) ?>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>/perfil/<?= $perfil['id'] ?>" class="btn-visitar">Ver Perfil</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($posts)): ?>
            <div class="result-section">
                <h3>📷 Publicações Encontradas</h3>
                <?php foreach ($posts as $pub): ?>
                    <div class="post">
                        <div class="post-header">
                            <div class="post-avatar">
                                <?= strtoupper(substr($pub['autor_nome'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div class="post-info">
                                <a href="<?= BASE_URL ?>/perfil/<?= $pub['usuario_id'] ?>" class="post-nome">
                                    <?= htmlspecialchars($pub['autor_nome'] ?? 'Usuário') ?>
                                </a>
                                <div class="post-usuario">
                                    @<?= htmlspecialchars($pub['nome_usuario'] ?? 'usuario') ?>
                                </div>
                                <div class="post-data">
                                    <?= date('d/m/Y \à\s H:i', strtotime($pub['criado_em'] ?? 'now')) ?>
                                </div>
                            </div>
                        </div>
                        
                        <p class="post-legenda"><?= nl2br(htmlspecialchars($pub['legenda'])) ?></p>
                        
                        <?php if (!empty($pub['url_imagem'])): ?>
                            <img src="<?= htmlspecialchars($pub['url_imagem']) ?>" class="post-imagem" onerror="this.src='<?= BASE_URL ?>/public/assets/img/default.jpg'">
                        <?php endif; ?>
                        
                        <div class="post-acoes">
                            <a href="<?= BASE_URL ?>/publicacoes/<?= $pub['id'] ?>/curtir" class="btn-curtir">
                                ❤️ Curtir (<span><?= $pub['total_curtidas'] ?? 0 ?></span>)
                            </a>
                            <button type="button" class="btn-denunciar" onclick="abrirModal(<?= $pub['id'] ?>)">
                                🚨 Denunciar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div>

<style>
.result-section {
    margin-bottom: 30px;
}

.result-section h3 {
    font-size: 18px;
    color: #262626;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #efefef;
}

.perfil-result {
    display: flex;
    align-items: center;
    gap: 15px;
    background: white;
    border: 1px solid #dbdbdb;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 10px;
    transition: box-shadow 0.2s;
}

.perfil-result:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.perfil-avatar-mini {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    font-weight: bold;
}

.perfil-info {
    flex: 1;
}

.perfil-nome {
    font-size: 16px;
    font-weight: bold;
    color: #262626;
    text-decoration: none;
}

.perfil-nome:hover {
    text-decoration: underline;
}

.perfil-username {
    font-size: 13px;
    color: #8e8e8e;
    margin-top: 3px;
}

.perfil-stats {
    font-size: 12px;
    color: #b3b3b3;
    margin-top: 3px;
}

.btn-visitar {
    padding: 8px 16px;
    background: #0095f6;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: background 0.2s;
}

.btn-visitar:hover {
    background: #0077cc;
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>