<?php
require_once __DIR__ . '/../config/database.php';

echo "<h1>🌱 Inserindo Dados Iniciais (Seed)</h1>";

$db = Database::connect();

try {
    // Limpar tabelas existentes (opcional - comentar se não quiser)
    echo "<p>🧹 Limpando dados antigos...</p>";
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("TRUNCATE TABLE curtidas");
    $db->exec("TRUNCATE TABLE denuncias");
    $db->exec("TRUNCATE TABLE publicacoes");
    $db->exec("TRUNCATE TABLE usuarios");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p style='color:green'>✅ Dados antigos removidos!</p>";
    
    // ================== 1. CRIAR 5 USUÁRIOS ==================
    echo "<h2>👥 Criando 5 usuários...</h2>";
    
    $usuarios = [
        [
            'nome' => 'Administrador',
            'nome_usuario' => 'admin',
            'email' => 'admin@instaclass.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'tipo' => 'admin'
        ],
        [
            'nome' => 'João Silva',
            'nome_usuario' => 'joaosilva',
            'email' => 'joao@email.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'tipo' => 'cliente'
        ],
        [
            'nome' => 'Maria Oliveira',
            'nome_usuario' => 'mariaoliveira',
            'email' => 'maria@email.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'tipo' => 'cliente'
        ],
        [
            'nome' => 'Carlos Santos',
            'nome_usuario' => 'carlossantos',
            'email' => 'carlos@email.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'tipo' => 'cliente'
        ],
        [
            'nome' => 'Ana Pereira',
            'nome_usuario' => 'anapereira',
            'email' => 'ana@email.com',
            'senha' => password_hash('123456', PASSWORD_DEFAULT),
            'tipo' => 'cliente'
        ]
    ];
    
    $usuariosIds = [];
    foreach ($usuarios as $user) {
        $stmt = $db->prepare("INSERT INTO usuarios (nome, nome_usuario, email, senha, tipo, criado_em) 
                              VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user['nome'], $user['nome_usuario'], $user['email'], $user['senha'], $user['tipo']]);
        $usuariosIds[] = $db->lastInsertId();
        echo "<p>✅ Usuário criado: {$user['nome']} (@{$user['nome_usuario']}) - Tipo: {$user['tipo']}</p>";
    }
    
    // ================== 2. CRIAR 10 PUBLICAÇÕES ==================
    echo "<h2>📷 Criando 10 publicações...</h2>";
    
    $publicacoes = [
        ['usuario_id' => 2, 'legenda' => 'Meu primeiro post no InstaClass! 🎉 Bem-vindos!', 'url_imagem' => 'https://picsum.photos/id/1/500/500', 'status' => 'aprovado'],
        ['usuario_id' => 2, 'legenda' => 'Hoje está um dia ensolarado! ☀️ Aproveitando a vida.', 'url_imagem' => 'https://picsum.photos/id/2/500/500', 'status' => 'aprovado'],
        ['usuario_id' => 3, 'legenda' => 'Aprendendo PHP e criando uma rede social! 💻 Muito legal!', 'url_imagem' => 'https://picsum.photos/id/3/500/500', 'status' => 'aprovado'],
        ['usuario_id' => 3, 'legenda' => 'Compartilhando um momento especial com vocês ❤️', 'url_imagem' => 'https://picsum.photos/id/4/500/500', 'status' => 'aprovado'],
        ['usuario_id' => 4, 'legenda' => 'Dica de hoje: Estudem programação! Vale muito a pena! 🚀', 'url_imagem' => 'https://picsum.photos/id/5/500/500', 'status' => 'aprovado'],
        ['usuario_id' => 4, 'legenda' => 'Praia no fim de semana 🏖️ Recarregando as energias!', 'url_imagem' => 'https://picsum.photos/id/6/500/500', 'status' => 'aprovado'],
        ['usuario_id' => 5, 'legenda' => 'Novo projeto incrível em breve! Aguardem! 🔥', 'url_imagem' => 'https://picsum.photos/id/7/500/500', 'status' => 'aprovado'],
        ['usuario_id' => 5, 'legenda' => 'Café e código ☕️ A melhor combinação!', 'url_imagem' => 'https://picsum.photos/id/8/500/500', 'status' => 'pendente'],
        ['usuario_id' => 2, 'legenda' => 'Final de semana de estudos! 📚 Quem mais?', 'url_imagem' => 'https://picsum.photos/id/9/500/500', 'status' => 'aprovado'],
        ['usuario_id' => 3, 'legenda' => 'Música nova no estilo! 🎵 Recomendo demais!', 'url_imagem' => 'https://picsum.photos/id/10/500/500', 'status' => 'aprovado']
    ];
    
    $publicacoesIds = [];
    foreach ($publicacoes as $pub) {
        $stmt = $db->prepare("INSERT INTO publicacoes (usuario_id, legenda, url_imagem, status, total_curtidas, total_comentarios, criado_em) 
                              VALUES (?, ?, ?, ?, 0, 0, NOW())");
        $stmt->execute([$pub['usuario_id'], $pub['legenda'], $pub['url_imagem'], $pub['status']]);
        $publicacoesIds[] = $db->lastInsertId();
        echo "<p>✅ Publicação criada: ID {$db->lastInsertId()} - {$pub['legenda']} (Status: {$pub['status']})</p>";
    }
    
    // ================== 3. CRIAR 10 CURTIDAS ==================
    echo "<h2>❤️ Criando 10 curtidas...</h2>";
    
    $curtidas = [
        ['usuario_id' => 2, 'publicacao_id' => 1],
        ['usuario_id' => 2, 'publicacao_id' => 2],
        ['usuario_id' => 3, 'publicacao_id' => 1],
        ['usuario_id' => 3, 'publicacao_id' => 3],
        ['usuario_id' => 4, 'publicacao_id' => 2],
        ['usuario_id' => 4, 'publicacao_id' => 4],
        ['usuario_id' => 5, 'publicacao_id' => 1],
        ['usuario_id' => 5, 'publicacao_id' => 5],
        ['usuario_id' => 2, 'publicacao_id' => 6],
        ['usuario_id' => 3, 'publicacao_id' => 7]
    ];
    
    foreach ($curtidas as $curtida) {
        $stmt = $db->prepare("INSERT INTO curtidas (usuario_id, publicacao_id, criado_em) VALUES (?, ?, NOW())");
        $stmt->execute([$curtida['usuario_id'], $curtida['publicacao_id']]);
        
        // Atualizar total_curtidas na tabela publicacoes
        $db->prepare("UPDATE publicacoes SET total_curtidas = total_curtidas + 1 WHERE id = ?")
           ->execute([$curtida['publicacao_id']]);
        
        echo "<p>✅ Curtida: Usuário {$curtida['usuario_id']} → Publicação {$curtida['publicacao_id']}</p>";
    }
    
    // ================== 4. CRIAR 3 DENÚNCIAS ==================
    echo "<h2>🚨 Criando 3 denúncias...</h2>";
    
    $denuncias = [
        ['publicacao_id' => 2, 'motivo' => 'Conteúdo impróprio', 'gravidade' => 'media'],
        ['publicacao_id' => 5, 'motivo' => 'Discurso de ódio', 'gravidade' => 'alta'],
        ['publicacao_id' => 8, 'motivo' => 'Spam ou enganoso', 'gravidade' => 'baixa']
    ];
    
    foreach ($denuncias as $denuncia) {
        $stmt = $db->prepare("INSERT INTO denuncias (publicacao_id, motivo, gravidade, status, criado_em) 
                              VALUES (?, ?, ?, 'pendente', NOW())");
        $stmt->execute([$denuncia['publicacao_id'], $denuncia['motivo'], $denuncia['gravidade']]);
        echo "<p>✅ Denúncia: Publicação {$denuncia['publicacao_id']} - Motivo: {$denuncia['motivo']}</p>";
    }
    
    // ================== RESUMO FINAL ==================
    echo "<hr>";
    echo "<h2>📊 Resumo do Seed:</h2>";
    echo "<ul>";
    echo "<li>✅ 5 usuários criados</li>";
    echo "<li>✅ 10 publicações criadas</li>";
    echo "<li>✅ 10 curtidas registradas</li>";
    echo "<li>✅ 3 denúncias registradas</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<h3>🔐 Credenciais para teste:</h3>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@instaclass.com / 123456</li>";
    echo "<li><strong>João:</strong> joao@email.com / 123456</li>";
    echo "<li><strong>Maria:</strong> maria@email.com / 123456</li>";
    echo "<li><strong>Carlos:</strong> carlos@email.com / 123456</li>";
    echo "<li><strong>Ana:</strong> ana@email.com / 123456</li>";
    echo "</ul>";
    
    echo "<p style='color:green; font-size:18px;'>✅ Seed concluído com sucesso!</p>";
    echo "<br><a href='/RedeSocial---Ead7/login' style='display:inline-block; background:#667eea; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>🔐 Ir para o Login</a>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>