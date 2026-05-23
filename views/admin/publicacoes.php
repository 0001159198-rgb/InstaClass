<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="admin-container">
    <h2>📷 Gerenciar Publicações</h2>
    
    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert-success"><?= $_SESSION['mensagem'] ?><?php unset($_SESSION['mensagem']); ?></div>
    <?php endif; ?>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Autor</th>
                <th>Legenda</th>
                <th>Imagem</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($publicacoes as $pub): ?>
            <tr>
                <td>#<?= $pub['id'] ?></td>
                <td><?= htmlspecialchars($pub['autor_nome'] ?? 'Usuário') ?></td>
                <td style="max-width: 300px;"><?= htmlspecialchars(substr($pub['legenda'], 0, 50)) ?>...</td>
                <td>
                    <?php if (!empty($pub['url_imagem'])): ?>
                        <img src="<?= htmlspecialchars($pub['url_imagem']) ?>" width="50" height="50" style="object-fit:cover; border-radius:8px;">
                    <?php else: ?>
                        <span>Sem imagem</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="status status-<?= $pub['status'] ?>">
                        <?= ucfirst($pub['status']) ?>
                    </span>
                </td>
                <td class="actions">
                    <?php if ($pub['status'] != 'aprovado'): ?>
                        <a href="<?= BASE_URL ?>/admin/publicacoes/<?= $pub['id'] ?>/aprovar" class="btn-aprovar">✅ Aprovar</a>
                    <?php endif; ?>
                    <?php if ($pub['status'] != 'bloqueado'): ?>
                        <a href="<?= BASE_URL ?>/admin/publicacoes/<?= $pub['id'] ?>/bloquear" class="btn-bloquear">🚫 Bloquear</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/admin/publicacoes/<?= $pub['id'] ?>/excluir" class="btn-excluir" onclick="return confirm('Tem certeza?')">🗑️ Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
}
.status-aprovado { background: #d4edda; color: #155724; }
.status-pendente { background: #fff3cd; color: #856404; }
.status-bloqueado { background: #f8d7da; color: #721c24; }
.actions a {
    display: inline-block;
    margin: 0 5px;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 6px;
    font-size: 12px;
}
.btn-aprovar { background: #28a745; color: white; }
.btn-bloquear { background: #ffc107; color: #333; }
.btn-excluir { background: #dc3545; color: white; }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>