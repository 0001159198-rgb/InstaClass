<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publicacao; 
use App\Models\User as Usuario;
use App\Models\Curtida;
use App\Models\Denuncia;
use Illuminate\Support\Facades\DB;

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
        // 🔥 CORREÇÃO CRÍTICA: Traz os posts cruzando com a tabela de usuários 
        // Isso faz aparecer o nome e as publicações dos outros autores do Seeder!
        $publicacoes = DB::table('publicacoes')
            ->join('usuarios', 'publicacoes.usuario_id', '=', 'usuarios.id')
            ->select(
                'publicacoes.*', 
                'usuarios.nome as autor_nome', 
                'usuarios.nome_usuario as autor_username'
            )
            ->where('publicacoes.status', '=', 'aprovada')
            ->orderBy('publicacoes.created_at', 'desc')
            ->get();

        // Vincula dinamicamente se o usuário logado curtiu ou não cada post
        $usuario_id = auth()->id() ?? session('usuario_id');
        foreach ($publicacoes as $pub) {
            $pub->total_curtidas = DB::table('curtidas')
                ->where('publicacao_id', $pub->id)
                ->count();
                
            $pub->ja_curtiu = $usuario_id ? DB::table('curtidas')
                ->where('publicacao_id', $pub->id)
                ->where('usuario_id', $usuario_id)
                ->exists() : false;
        }

        return view('cliente.feed', compact('publicacoes'));
    }

    public function criarPublicacao() {
        return view('cliente.criar');
    }

    public function salvarPublicacao(Request $request) {

        $usuario_id = auth()->id() ?? session('usuario_id');

        if (!$usuario_id) {
            return redirect()->to('/login')->with('erro', 'Sessão expirada. Faça login novamente.');
        }

        $legenda = trim($request->input('legenda', ''));
        $url_imagem = trim($request->input('url_imagem', ''));

        if (empty($legenda)) {
            return redirect()->to('/publicacoes/criar')
                ->with('erro', 'Legenda obrigatória!');
        }

        // Garante que o post vá como 'aprovada' para o feed não sumir
        $resultado = Publicacao::criar($usuario_id, $legenda, $url_imagem);

        if ($resultado) {
            return redirect()->to('/perfil/' . $usuario_id)
                ->with('mensagem', '✅ Publicação criada com sucesso!');
        }

        return redirect()->to('/publicacoes/criar')
            ->with('erro', '❌ Erro ao criar publicação!');
    }

    public function listarPublicacoesUsuario($id = null) {

        $id = $id ? trim($id) : null;

        if (empty($id) || $id == 0 || $id === 'null' || $id === 'undefined') {
            $id = auth()->id() ?? session('usuario_id');

            if (!$id) {
                return redirect()->to('/login');
            }
        }

        $usuario = Usuario::where('id', '=', $id)->first();

        if (!$usuario) {
            // Fallback de segurança para evitar erro 404 caso ocorra dessincronização no Render
            $usuario = new \stdClass();
            $usuario->id = $id;
            $usuario->nome = auth()->user()->nome ?? session('usuario_nome') ?? 'Usuário';
            $usuario->nome_usuario = auth()->user()->nome_usuario ?? session('usuario_nome_usuario') ?? 'usuario';
            $usuario->email = auth()->user()->email ?? session('usuario_email') ?? '';
            
            $publicacoes = [];
        } else {
            // Busca as publicações usando o Model tratado
            $publicacoes = Publicacao::porUsuario($id);
            
            // Injeta contagem de curtidas para não quebrar a View do perfil
            foreach ($publicacoes as $pub) {
                $pub->total_curtidas = DB::table('curtidas')->where('publicacao_id', $pub->id)->count();
            }
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

        if (str_starts_with($termo, '@')) {
            $nomeUsuarioBusca = ltrim($termo, '@');
            $usuariosEncontrados = Usuario::where('nome_usuario', 'LIKE', '%' . $nomeUsuarioBusca . '%')->get();
        } else {
            $usuariosEncontrados = Usuario::where('nome', 'LIKE', '%' . $termo . '%')
                ->orWhere('nome_usuario', 'LIKE', '%' . $termo . '%')
                ->get();
        }

        foreach ($usuariosEncontrados as $usr) {
            $resultadosFinais[] = [
                'id' => $usr->id,
                'nome' => $usr->nome,
                'nome_usuario' => $usr->nome_usuario,
                'email' => $usr->email,
                'tipo' => 'perfil' 
            ];
        }

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
                'total_curtidas' => DB::table('curtidas')->where('publicacao_id', $post->id)->count()
            ];
        }

        $publicacoes = collect($resultadosFinais);

        return view('cliente.busca', compact('publicacoes'));
    }

    // ================== CURTIDAS ==================

    public function curtirPublicacao($id) {

        $usuario_id = auth()->id() ?? session('usuario_id');

        if (!$usuario_id) {
            return redirect()->to('/login')->with('erro', 'Faça login para curtir.');
        }

        Curtida::toggleCurtida($usuario_id, $id);

        return redirect()->back();
    }

    public function minhasCurtidas() {

        $usuario_id = auth()->id() ?? session('usuario_id');

        if (!$usuario_id) {
            return redirect()->to('/login');
        }

        $publicacoes = Curtida::getPublicacoesCurtidas($usuario_id);
        $totalCurtidas = Curtida::countCurtidas($usuario_id);

        return view('cliente.curtidas', compact('publicacoes', 'totalCurtidas'));
    }

    public function descurtirPublicacao($id) {

        $usuario_id = auth()->id() ?? session('usuario_id');

        if (!$usuario_id) {
            return redirect()->to('/login');
        }

        Curtida::descurtir($usuario_id, $id);

        return redirect()->back();
    }

    // ================== DENÚNCIAS ==================

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

        $email = trim($request->input('email', ''));
        $senha = $request->input('senha', '');
        $tipoSelecionado = $request->input('tipo', 'cliente');

        if (empty($email) || empty($senha)) {
            return redirect()->to('/login?erro=1');
        }

        $usuario = Usuario::buscarPorEmail($email);

        if ($usuario && password_verify($senha, $usuario->senha)) {

            if ($tipoSelecionado == 'admin' && $usuario->tipo != 'admin') {
                return redirect()->to('/login?erro=2&tipo=admin');
            }

            auth()->loginUsingId($usuario->id);

            session([
                'usuario_id' => $usuario->id,
                'usuario_nome' => $usuario->nome,
                'usuario_email' => $usuario->email,
                'usuario_tipo' => $usuario->tipo
            ]);

            if ($usuario->tipo == 'admin') {
                return redirect()->to('/admin');
            }

            return redirect()->to('/feed');
        }

        return redirect()->to('/login?erro=2&tipo=' . $tipoSelecionado);
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

        $nome = trim($request->input('nome', ''));
        $nome_usuario = trim($request->input('nome_usuario', ''));
        $email = trim($request->input('email', ''));
        $senha = $request->input('senha', '');

        $cadastrarComoAdmin = $request->has('cadastrar_como_admin') && $request->input('cadastrar_como_admin') == '1';
        $codigoAdmin = $request->input('codigo_admin', '');

        if (empty($nome) || empty($nome_usuario) || empty($email) || empty($senha)) {
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

        $sucesso = Usuario::criar($nome, $nome_usuario, $email, $senhaHash, $tipo);

        if ($sucesso) {
            // Força a limpeza antes de mover para o login para evitar conflito de cookies
            auth()->logout();
            session()->flush();
            return redirect()->to('/login?sucesso=1');
        }

        return redirect()->to('/registrar?erro=3');
    }
}
