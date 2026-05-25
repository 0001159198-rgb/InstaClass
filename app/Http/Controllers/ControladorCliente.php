<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publicacao; 
use App\Models\User as Usuario;
use App\Models\Curtida;
use App\Models\Denuncia;

class ControladorCliente extends Controller {

    // ================== PÁGINA INICIAL ==================

    public function welcome() {

        if (auth()->check()) {

            if (auth()->user()->tipo === 'admin') {
                return redirect()->to('/admin');
            }

            return redirect()->to('/feed');
        }

        return view('welcome');
    }

    // ================== FEED E PUBLICAÇÕES ==================

    public function inicio() {
        return $this->feed();
    }

    public function feed() {
        $publicacoes = Publicacao::aprovadas();
        return view('cliente.feed', compact('publicacoes'));
    }

    public function criarPublicacao() {
        return view('cliente.criar');
    }

    public function salvarPublicacao(Request $request) {

        $usuario_id = auth()->id();

        $legenda = trim($request->input('legenda', ''));
        $url_imagem = trim($request->input('url_imagem', ''));

        if (empty($legenda)) {
            return redirect()->to('/publicacoes/criar')
                ->with('erro', 'Legenda obrigatória!');
        }

        $resultado = Publicacao::criar($usuario_id, $legenda, $url_imagem);

        if ($resultado) {

            return redirect()->to('/perfil/' . $usuario_id)
                ->with('mensagem', '✅ Publicação criada com sucesso!');
        }

        return redirect()->to('/publicacoes/criar')
            ->with('erro', '❌ Erro ao criar publicação!');
    }

    public function listarPublicacoesUsuario($id = null) {

        if (empty($id) || $id == 0) {

            $id = auth()->id();

            if (!$id) {
                return redirect()->to('/login');
            }
        }

        $publicacoes = Publicacao::porUsuario($id);
        $usuario = Usuario::buscarPorId($id);

        if (!$usuario) {
            abort(404, 'Usuário não encontrado');
        }

        return view('cliente.perfil', compact('publicacoes', 'usuario'));
    }

    public function buscarPublicacoes(Request $request) {

        $termo = trim($request->input('q', ''));

        if (empty($termo)) {
            $publicacoes = [];
            return view('cliente.busca', compact('publicacoes'));
        }

        $resultadosFinais = [];

        // 1. BUSCA POR PERFIS (Abstrata ou usando @)
        if (str_starts_with($termo, '@')) {
            $nomeUsuarioBusca = ltrim($termo, '@');
            $usuariosEncontrados = Usuario::where('nome_usuario', 'LIKE', '%' . $nomeUsuarioBusca . '%')->get();
        } else {
            $usuariosEncontrados = Usuario::where('nome', 'LIKE', '%' . $termo . '%')
                ->orWhere('nome_usuario', 'LIKE', '%' . $termo . '%')
                ->get();
        }

        // Formata os usuários encontrados para o padrão esperado pela view (tipo => perfil)
        foreach ($usuariosEncontrados as $usr) {
            $resultadosFinais[] = [
                'id' => $usr->id,
                'nome' => $usr->nome,
                'nome_usuario' => $usr->nome_usuario,
                'email' => $usr->email,
                'tipo' => 'perfil' 
            ];
        }

        // 2. BUSCA POR PUBLICAÇÕES
        $postsEncontrados = Publicacao::where('legenda', 'LIKE', '%' . $termo . '%')
            ->where('status', 'aprovada')
            ->get();

        foreach ($postsEncontrados as $post) {
            $autor = $post->usuario; 

            $resultadosFinais[] = [
                'id' => $post->id,
                'usuario_id' => $post->usuario_id,
                'legenda' => $post->legenda,
                'url_imagem' => $post->url_imagem,
                'criado_em' => $post->created_at,
                'autor_nome' => $autor ? $autor->nome : 'Usuário',
                'nome_usuario' => $autor ? $autor->nome_usuario : 'usuario',
                'total_curtidas' => 0 
            ];
        }

        // Transforma o array unificado em uma Coleção para que o Blade consiga ler e contar
        $publicacoes = collect($resultadosFinais);

        return view('cliente.busca', compact('publicacoes'));
    }

    public function curtirPublicacao($id) {

        $usuario_id = auth()->id();

        Curtida::toggleCurtida($usuario_id, $id);

        return redirect()->back();
    }

    public function minhasCurtidas() {

        $usuario_id = auth()->id();

        $publicacoes = Curtida::getPublicacoesCurtidas($usuario_id);
        $totalCurtidas = Curtida::countCurtidas($usuario_id);

        return view('cliente.curtidas', compact(
            'publicacoes',
            'totalCurtidas'
        ));
    }

    public function descurtirPublicacao($id) {

        $usuario_id = auth()->id();

        Curtida::descurtir($usuario_id, $id);

        return redirect()->back();
    }

    public function denunciarPublicacao(Request $request, $id) {

        $motivo = $request->input('motivo', 'Conteúdo impróprio');

        $gravidade = $request->input('gravidade', 'media');

        $resultado = Denuncia::criar(
            $id,
            $motivo,
            $gravidade
        );

        if ($resultado) {

            return redirect()->back()
                ->with('mensagem', '✅ Denúncia enviada com sucesso!');
        }

        return redirect()->back()
            ->with('erro', '❌ Você já denunciou esta publicação.');
    }

    // ================== LOGIN ==================

    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {

        $email = $request->input('email', '');
        $senha = $request->input('senha', '');
        $tipoSelecionado = $request->input('tipo', 'cliente');

        if (empty($email) || empty($senha)) {
            return redirect()->to('/login?erro=1');
        }

        $usuario = Usuario::buscarPorEmail($email);

        if ($usuario && password_verify($senha, $usuario->senha)) {

            if (
                $tipoSelecionado == 'admin'
                && $usuario->tipo != 'admin'
            ) {
                return redirect()->to('/login?erro=2&tipo=admin');
            }

            // LOGIN LARAVEL
            auth()->loginUsingId($usuario->id);

            // SESSÃO
            session([
                'usuario_id' => $usuario->id,
                'usuario_nome' => $usuario->nome,
                'usuario_email' => $usuario->email,
                'usuario_tipo' => $usuario->tipo
            ]);

            // REDIRECIONAMENTO
            if (
                $tipoSelecionado == 'cliente'
                && $usuario->tipo == 'admin'
            ) {
                return redirect()->to('/admin');
            }

            return ($usuario->tipo == 'admin')
                ->with('mensagem', '❌ Erro ao criar publicação!');
        }

        return redirect()->to(
            '/login?erro=2&tipo=' . $tipoSelecionado
        );
    }

    // ================== LOGOUT ==================

    public function logout() {

        auth()->logout();

        session()->flush();

        return redirect()->to('/');
    }

    // ================== REGISTRO ==================

    public function showRegistro() {
        return view('auth.registro');
    }

    public function registrar(Request $request) {

        $nome = $request->input('nome', '');
        $nome_usuario = $request->input('nome_usuario', '');
        $email = $request->input('email', '');
        $senha = $request->input('senha', '');

        $cadastrarComoAdmin =
            $request->has('cadastrar_como_admin')
            && $request->input('cadastrar_como_admin') == '1';

        $codigoAdmin = $request->input('codigo_admin', '');

        if (
            empty($nome)
            || empty($nome_usuario)
            || empty($email)
            || empty($senha)
        ) {
            return redirect()->to('/registrar?erro=1');
        }

        if (Usuario::emailExiste($email)) {
            return redirect()->to('/registrar?erro=2');
        }

        if (Usuario::nomeUsuarioExiste($nome_usuario)) {
            return redirect()->to('/registrar?erro=4');
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $tipo = 'cliente';

        if ($cadastrarComoAdmin) {

            $codigoSeguranca = 'ADMIN123';

            if ($codigoAdmin !== $codigoSeguranca) {
                return redirect()->to('/registrar?erro=5');
            }

            $tipo = 'admin';

        } else {

            if (Usuario::total() == 0) {
                $tipo = 'admin';
            }
        }

        $sucesso = Usuario::criar(
            $nome,
            $nome_usuario,
            $email,
            $senhaHash,
            $tipo
        );

        if ($sucesso) {
            return redirect()->to('/login?sucesso=1');
        }

        return redirect()->to('/registrar?erro=3');
    }
}
