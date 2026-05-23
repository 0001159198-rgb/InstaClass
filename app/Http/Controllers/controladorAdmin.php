<?php

class ControladorAdmin {

    // MÉTODO AUXILIAR PRIVADO: Evita repetição de código em todas as funções
    private function verificarAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'admin') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function dashboard() {
        $this->verificarAdmin();
        
        // Buscar dados para os indicadores
        $totalUsuarios = Usuario::total();
        $totalPublicacoes = count(Publicacao::todas());
        $totalDenuncias = count(Denuncia::all());
        $totalPendentes = count(Publicacao::pendentes());
        
        // Buscar denúncias recentes
        $denunciasRecentes = Denuncia::recentes(5);
        
        require __DIR__ . '/../../views/admin/dashboard.php';
    }

    public function listarUsuarios() {
        $this->verificarAdmin();
        
        $usuarios = Usuario::todos();
        require __DIR__ . '/../../views/admin/usuarios.php';
    }

    public function listarPublicacoes() {
        $this->verificarAdmin();
        
        $publicacoes = Publicacao::todas();
        require __DIR__ . '/../../views/admin/publicacoes.php';
    }

    public function listarDenuncias() {
        $this->verificarAdmin();
        
        $denuncias = Denuncia::all();
        require __DIR__ . '/../../views/admin/denuncias.php';
    }

    public function verPublicacao($id) {
        $this->verificarAdmin();
        
        // Validar ID
        if (empty($id) || !is_numeric($id)) {
            $_SESSION['erro'] = "ID de publicação inválido.";
            header("Location: " . BASE_URL . "/admin/publicacoes");
            exit;
        }
        
        $publicacao = Publicacao::buscarPorId($id);
        if (!$publicacao) {
            $_SESSION['erro'] = "Publicação não encontrada!";
            header("Location: " . BASE_URL . "/admin/publicacoes");
            exit;
        }
        
        require __DIR__ . '/../../views/admin/ver_publicacao.php';
    }

    public function aprovarPublicacao($id) {
        $this->verificarAdmin();
        
        if (empty($id) || !is_numeric($id)) {
            $_SESSION['erro'] = "ID de publicação inválido.";
            header("Location: " . BASE_URL . "/admin/publicacoes");
            exit;
        }
        
        try {
            $db = Database::connect();
            $stmt = $db->prepare("UPDATE publicacoes SET status = 'aprovado' WHERE id = ?");
            $stmt->execute([$id]);
            
            $_SESSION['mensagem'] = "✅ Publicação #$id aprovada com sucesso!";
        } catch (Exception $e) {
            $_SESSION['erro'] = "Erro ao aprovar publicação: " . $e->getMessage();
        }
        
        header("Location: " . BASE_URL . "/admin/publicacoes");
        exit;
    }

    public function determinarStatus($id, $status, $mensagemSucesso) {
        try {
            $db = Database::connect();
            $stmt = $db->prepare("UPDATE publicacoes SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            $_SESSION['mensagem'] = $mensagemSucesso;
        } catch (Exception $e) {
            $_SESSION['erro'] = "Erro ao atualizar publicação: " . $e->getMessage();
        }
    }

    public function bloquearPublicacao($id) {
        $this->verificarAdmin();
        
        if (empty($id) || !is_numeric($id)) {
            $_SESSION['erro'] = "ID de publicação inválido.";
            header("Location: " . BASE_URL . "/admin/publicacoes");
            exit;
        }
        
        try {
            $db = Database::connect();
            $stmt = $db->prepare("UPDATE publicacoes SET status = 'bloqueado' WHERE id = ?");
            $stmt->execute([$id]);
            
            $_SESSION['mensagem'] = "🚫 Publicação #$id bloqueada com sucesso!";
        } catch (Exception $e) {
            $_SESSION['erro'] = "Erro ao bloquear publicação: " . $e->getMessage();
        }
        
        header("Location: " . BASE_URL . "/admin/publicacoes");
        exit;
    }

    public function excluirPublicacao($id) {
        $this->verificarAdmin();
        
        if (empty($id) || !is_numeric($id)) {
            $_SESSION['erro'] = "ID de publicação inválido.";
            header("Location: " . BASE_URL . "/admin/publicacoes");
            exit;
        }
        
        try {
            $db = Database::connect();
            
            // Primeiro, excluir as curtidas relacionadas
            $db->prepare("DELETE FROM curtidas WHERE publicacao_id = ?")->execute([$id]);
            
            // Depois, excluir as denúncias relacionadas
            $db->prepare("DELETE FROM denuncias WHERE publicacao_id = ?")->execute([$id]);
            
            // Por fim, excluir a publicação
            $stmt = $db->prepare("DELETE FROM publicacoes WHERE id = ?");
            $stmt->execute([$id]);
            
            $_SESSION['mensagem'] = "🗑️ Publicação #$id excluída permanentemente!";
        } catch (Exception $e) {
            $_SESSION['erro'] = "Erro ao excluir publicação: " . $e->getMessage();
        }
        
        header("Location: " . BASE_URL . "/admin/publicacoes");
        exit;
    }
}