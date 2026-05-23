<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="admin-container">
    <h2>👥 Gerenciar Usuários</h2>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Usuário</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $user): ?>
            <tr>
                <td>#<?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['nome']) ?></td>
                <td>@<?= htmlspecialchars($user['nome_usuario']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <span class="badge <?= $user['tipo'] == 'admin' ? 'badge-admin' : 'badge-user' ?>">
                        <?= $user['tipo'] ?>
                    </span>
                </td>
                <td><?= date('d/m/Y', strtotime($user['criado_em'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.admin-table {
    width: 100%;
    background: white;
    border-collapse: collapse;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.admin-table th,
.admin-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #efefef;
}
.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
}
.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}
.badge-admin {
    background: #d4edda;
    color: #155724;
}
.badge-user {
    background: #e2e3e5;
    color: #383d41;
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>