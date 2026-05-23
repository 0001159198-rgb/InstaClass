<?php 
include __DIR__ . '/../layouts/header.php'; 

?>

<div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
    
    <?php if (isset($usuario) && !empty($usuario)): ?>
        <div class="perfil-header" style="background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); contain: content;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; color: white; flex-shrink: 0;">
                    <?= strtoupper(substr($usuario['nome'] ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <h2 style="margin: 0 0 5px 0;"><?= htmlspecialchars($usuario['nome'] ?? 'Usuário') ?></h2>
                    <p style="margin: 0; color: #666;">@<?= htmlspecialchars($usuario['nome_usuario'] ?? 'usuario') ?></p>
                    <p style="margin: 5px 0 0 0; color: #888; font-size: 14px;">📧 <?= htmlspecialchars($usuario['email'] ?? '') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <h3 style="margin-bottom: 15px;">📷 Publicações</h3>
    <hr style="margin-bottom: 20px;">

    <?php if (!isset($publicacoes) || !is_array($publicacoes)): ?>
        <div class="alert alert-warning" style="background: #fff3cd; color: #856404; padding: 12px; border-radius: 5px;">
            ⚠️ Nenhuma publicação encontrada.
        </div>
    <?php elseif (empty($publicacoes)): ?>
        <div class="alert alert-info" style="background: #d1ecf1; color: #0c5460; padding: 12px; border-radius: 5px;">
            📭 Este usuário ainda não tem publicações.
        </div>
    <?php else: ?>
        <div class="grid-publicacoes" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; min-height: 200px;">
            <?php foreach ($publicacoes as $pub): ?>
                <div class="card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); contain: content;">
                    
                    <?php 
                    // Checa se a imagem existe no banco, senão usa uma imagem placeholder externa garantida
                    $imagem = (!empty($pub['url_imagem']) && $pub['url_imagem'] !== 'null' && $pub['url_imagem'] !== 'undefined') 
                        ? $pub['url_imagem'] 
                        : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=500&auto=format&fit=crop'; // Placeholder externo estável
                    ?>
                    
                    <img src="<?= htmlspecialchars($imagem) ?>" 
                         alt="Publicação" 
                         style="width: 100%; height: 250px; object-fit: cover; display: block;"
                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=500&auto=format&fit=crop';">
                    
                    <div class="card-body" style="padding: 12px;">
                        <p style="margin: 0 0 8px 0; font-size: 14px; word-wrap: break-word;"><?= nl2br(htmlspecialchars($pub['legenda'] ?? 'Sem legenda')) ?></p>
                        <small style="color: #666;">Status: 
                            <strong style="color: <?= $pub['status'] == 'aprovado' ? '#27ae60' : ($pub['status'] == 'pendente' ? '#f39c12' : '#e74c3c') ?>">
                                <?= htmlspecialchars($pub['status'] ?? 'pendente') ?>
                            </strong>
                        </small>
                        <br>
                        <small style="color: #888;">❤️ <?= $pub['total_curtidas'] ?? 0 ?> curtidas</small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div style="margin-top: 30px; text-align: center;">
        <a href="<?= BASE_URL ?>/feed" style="display: inline-block; background: #667eea; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">
            ← Voltar ao Feed
        </a>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>ç