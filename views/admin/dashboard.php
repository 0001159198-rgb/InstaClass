<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="admin-container">
    <h2>📊 Dashboard Administrativo</h2>
    
    <div class="indicadores">
        <div class="card-indicador">
            <div class="indicador-icon">👥</div>
            <div class="indicador-info">
                <h3><?= $totalUsuarios ?? 0 ?></h3>
                <p>Usuários</p>
            </div>
        </div>
        
        <div class="card-indicador">
            <div class="indicador-icon">📷</div>
            <div class="indicador-info">
                <h3><?= $totalPublicacoes ?? 0 ?></h3>
                <p>Publicações</p>
            </div>
        </div>
        
        <div class="card-indicador">
            <div class="indicador-icon">🚨</div>
            <div class="indicador-info">
                <h3><?= $totalDenuncias ?? 0 ?></h3>
                <p>Denúncias</p>
                <?php if (($totalDenuncias ?? 0) > 0): ?>
                    <span class="badge-novo">Novas</span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- CARD PENDENTES REMOVIDO -->
        
    </div>
    
    <!-- Denúncias Recentes -->
    <div class="denuncias-recentes">
        <div class="section-header">
            <h3>🚨 Denúncias Recentes</h3>
            <a href="<?= BASE_URL ?>/admin/denuncias" class="ver-todas">Ver todas →</a>
        </div>
        
        <?php if (empty($denunciasRecentes)): ?>
            <div class="empty-denuncias">
                <p>✅ Nenhuma denúncia pendente. Tudo tranquilo!</p>
            </div>
        <?php else: ?>
            <table class="denuncias-table">
                <thead>
                    <tr>
                        <th>Publicação</th>
                        <th>Motivo</th>
                        <th>Gravidade</th>
                        <th>Data</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($denunciasRecentes as $den): ?>
                    <tr>
                        <td class="legenda-cell">
                            <?= htmlspecialchars(substr($den['publicacao_legenda'] ?? '', 0, 40)) ?>...
                        </td>
                        <td><?= htmlspecialchars($den['motivo']) ?></td>
                        <td>
                            <span class="gravidade gravidade-<?= $den['gravidade'] ?>">
                                <?= ucfirst($den['gravidade']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($den['criado_em'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/publicacoes/<?= $den['publicacao_id'] ?>" class="btn-ver">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div class="admin-menu">
        <a href="<?= BASE_URL ?>/admin/usuarios" class="admin-btn">
            <span>👥</span> Gerenciar Usuários
        </a>
        <a href="<?= BASE_URL ?>/admin/publicacoes" class="admin-btn">
            <span>📷</span> Gerenciar Publicações
        </a>
        <a href="<?= BASE_URL ?>/admin/denuncias" class="admin-btn">
            <span>🚨</span> Ver Denúncias
            <?php if (($totalDenuncias ?? 0) > 0): ?>
                <span class="badge-count"><?= $totalDenuncias ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<style>
.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.admin-container h2 {
    margin-bottom: 30px;
    color: #262626;
}

.indicadores {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.card-indicador {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    position: relative;
}

.indicador-icon {
    font-size: 40px;
}

.indicador-info h3 {
    font-size: 28px;
    margin: 0;
    color: #262626;
}

.indicador-info p {
    margin: 0;
    color: #8e8e8e;
    font-size: 14px;
}

.badge-novo {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #e74c3c;
    color: white;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 10px;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.denuncias-recentes {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #efefef;
}

.section-header h3 {
    margin: 0;
    color: #262626;
}

.ver-todas {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
}

.ver-todas:hover {
    text-decoration: underline;
}

.empty-denuncias {
    text-align: center;
    padding: 40px;
    color: #8e8e8e;
}

.denuncias-table {
    width: 100%;
    border-collapse: collapse;
}

.denuncias-table th,
.denuncias-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #efefef;
}

.denuncias-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.legenda-cell {
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.gravidade {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.gravidade-baixa {
    background: #d4edda;
    color: #155724;
}

.gravidade-media {
    background: #fff3cd;
    color: #856404;
}

.gravidade-alta {
    background: #f8d7da;
    color: #721c24;
}

.btn-ver {
    background: #667eea;
    color: white;
    padding: 5px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
}

.btn-ver:hover {
    background: #5a67d8;
}

.admin-menu {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.admin-btn {
    flex: 1;
    background: white;
    border: 1px solid #dbdbdb;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    color: #262626;
    transition: all 0.2s;
    position: relative;
}

.admin-btn span:first-child {
    font-size: 32px;
    display: block;
    margin-bottom: 10px;
}

.admin-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #667eea;
}

.badge-count {
    position: absolute;
    top: 10px;
    right: 15px;
    background: #e74c3c;
    color: white;
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: bold;
}

@media (max-width: 768px) {
    .denuncias-table {
        font-size: 12px;
    }
    
    .legenda-cell {
        max-width: 120px;
    }
    
    .admin-btn span:first-child {
        font-size: 24px;
    }
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>