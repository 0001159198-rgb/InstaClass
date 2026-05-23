<?php

class ControladorCliente {

    // ================== MÉTODOS AUXILIARES (PROTEÇÃO E SESSÃO) ==================

    /**
     * Garante que a sessão foi iniciada de forma limpa.
     */
    private function iniciarSessao() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Exige que o usuário esteja logado. Se não estiver, manda para o login.
     * Retorna o ID do usuário logado.
     */
    private function exigirAutenticacao() {
        $this->iniciarSessao();
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        
        if (!$usuario_id) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        return $usuario_id;
    }

    /**
     * Executa um redirecionamento seguro priorizando a página anterior (Referer).
     */
    private function redirecionarParaAnterior() {
        $redirect = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/feed';
        header("Location: " . $redirect);
        exit;
    }

    // ================== PÁGINA INICIAL ==================

    public function welcome() {
        $this->iniciarSessao();
        
        if (isset($_SESSION['usuario_id'])) {
            if ($_SESSION['usuario_tipo'] == 'admin') {
                header('Location: ' . BASE_URL . '/admin');
            } else {
                header('Location: ' . BASE_URL . '/feed');
            }
            exit;
        }
        
        require __DIR__ . '/../../views/welcome.php';
    }

    // ================== FEED E PUBLICAÇÕES ==================

    public function inicio() {
        $this->feed();
    }

    public function feed() {
        $publicacoes = Publicacao::aprovadas();
        require __DIR__ . '/../../views/cliente/feed.php';
    }

    public function criarPublicacao() {
        $this->exigirAutenticacao();
        require __DIR__ . '/../../views/cliente/criar.php';
    }

    public function salvarPublicacao() {
        $usuario_id = $this->exigirAutenticacao();

        $legenda = trim($_POST['legenda'] ?? '');
        $url_imagem = trim($_POST['url_imagem'] ?? '');

        if (empty($legenda)) {
            $_SESSION['erro'] = "Legenda obrigatória!";
            header('Location: ' . BASE_URL . '/publicacoes/criar');
            exit;
        }

        $resultado = Publicacao::criar($usuario_id, $legenda, $url_imagem);
        
        if ($resultado) {
            $_SESSION['mensagem'] = "✅ Publicação criada com sucesso!";
            header('Location: ' . BASE_URL . '/perfil/' . $usuario_id);
        } else {
            $_SESSION['erro'] = "❌ Erro ao criar publicação!";
            header('Location: ' . BASE_URL . '/publicacoes/criar');
        }
        exit;
    }

    public function listarPublicacoesUsuario($id) {
        if (empty($id) || $id == 0) {
            $this->iniciarSessao();
            $id = $_SESSION['usuario_id'] ?? null;
            
            if (!$id) {
                header('Location: ' . BASE_URL . '/login');
                exit;
            }
        }
        
        $publicacoes = Publicacao::porUsuario($id);
        $usuario = Usuario::buscarPorId($id);
        
        if (!$usuario) {
            http_response_code(404);
            echo "<h1>404 - Usuário não encontrado</h1>";
            exit;
        }
        
        require __DIR__ . '/../../views/cliente/perfil.php';
    }

    public function buscarPublicacoes() {
        $termo = $_GET['q'] ?? '';
        $publicacoes = empty($termo) ? [] : Publicacao::buscar($termo);
        
        require __DIR__ . '/../../views/cliente/busca.php';
    }

    public function curtirPublicacao($id) {
        $usuario_id = $this->exigirAutenticacao();
        Curtida::toggleCurtida($usuario_id, $id);
        $this->redirecionarParaAnterior();
    }

    public function minhasCurtidas() {
        $usuario_id = $this->exigirAutenticacao();
        
        $publicacoes = Curtida::getPublicacoesCurtidas($usuario_id);
        $totalCurtidas = Curtida::countCurtidas($usuario_id);
        
        require __DIR__ . '/../../views/cliente/curtidas.php';
    }

    public function descurtirPublicacao($id) {
        $usuario_id = $this->exigirAutenticacao();
        Curtida::descurtir($usuario_id, $id);
        $this->redirecionarParaAnterior();
    }

    public function denunciarPublicacao($id) {
        $this->exigirAutenticacao();

        $motivo = $_POST['motivo'] ?? 'Conteúdo impróprio';
        $gravidade = $_POST['gravidade'] ?? 'media';

        $resultado = Denuncia::criar($id, $motivo, $gravidade);
        
        if ($resultado) {
            $_SESSION['mensagem'] = "✅ Denúncia enviada com sucesso!";
        } else {
            $_SESSION['erro'] = "❌ Você já denunciou esta publicação.";
        }
        
        $this->redirecionarParaAnterior();
    }

    // ================== MÉTODOS DE AUTENTICAÇÃO ==================

    public function showLogin() {
        $loginView = __DIR__ . '/../../views/auth/login.php';
        
        if (file_exists($loginView)) {
            require_once $loginView;
        } else {
            echo "Erro: Arquivo de login não encontrado em: " . $loginView;
        }
    }

    public function login() {
        $this->iniciarSessao();
        
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $tipoSelecionado = $_POST['tipo'] ?? 'cliente';
        
        if (empty($email) || empty($senha)) {
            header('Location: ' . BASE_URL . '/login?erro=1');
            exit;
        }
        
        $usuario = Usuario::buscarPorEmail($email);
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            
            if ($tipoSelecionado == 'admin' && $usuario['tipo'] != 'admin') {
                header('Location: ' . BASE_URL . '/login?erro=2&tipo=admin');
                exit;
            }
            
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
            
            if ($tipoSelecionado == 'cliente' && $usuario['tipo'] == 'admin') {
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }
            
            if ($usuario['tipo'] == 'admin') {
                header('Location: ' . BASE_URL . '/admin');
            } else {
                header('Location: ' . BASE_URL . '/feed');
            }
        } else {
            header('Location: ' . BASE_URL . '/login?erro=2&tipo=' . $tipoSelecionado);
        }
        exit;
    }

    public function logout() {
        $this->iniciarSessao();
        session_destroy();
        header('Location: ' . BASE_URL . '/');
        exit;
    }

    public function showRegistro() {
        $registroView = __DIR__ . '/../../views/auth/registro.php';
        
        if (file_exists($registroView)) {
            require_once $registroView;
        } else {
            echo "Erro: Arquivo de registro não encontrado em: " . $registroView;
        }
    }

    public function registrar() {
        $nome = $_POST['nome'] ?? '';
        $nome_usuario = $_POST['nome_usuario'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $cadastrarComoAdmin = isset($_POST['cadastrar_como_admin']) && $_POST['cadastrar_como_admin'] == '1';
        $codigoAdmin = $_POST['codigo_admin'] ?? '';
        
        if (empty($nome) || empty($nome_usuario) || empty($email) || empty($senha)) {
            header('Location: ' . BASE_URL . '/registrar?erro=1');
            exit;
        }
        
        if (Usuario::emailExiste($email)) {
            header('Location: ' . BASE_URL . '/registrar?erro=2');
            exit;
        }
        
        if (Usuario::nomeUsuarioExiste($nome_usuario)) {
            header('Location: ' . BASE_URL . '/registrar?erro=4');
            exit;
        }
        
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $tipo = 'cliente';
        
        if ($cadastrarComoAdmin) {
            $codigoSeguranca = 'ADMIN123';
            if ($codigoAdmin !== $codigoSeguranca) {
                header('Location: ' . BASE_URL . '/registrar?erro=5');
                exit;
            }
            $tipo = 'admin';
        } else {
            if (Usuario::total() == 0) {
                $tipo = 'admin';
            }
        }
        
        $sucesso = Usuario::criar($nome, $nome_usuario, $email, $senhaHash, $tipo);
        
        if ($sucesso) {
            header('Location: ' . BASE_URL . '/login?sucesso=1');
        } else {
            header('Location: ' . BASE_URL . '/registrar?erro=3');
        }
        exit;
    }
}