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

        // Limpa espaços ou strings nulas enviadas por parâmetro
        $id = $id ? trim($id) : null;

        if (empty($id) || $id == 0 || $id === 'null') {
            $id = auth()->id();

            if (!$id) {
                return redirect()->to('/login');
            }
        }

        // Força uma busca direta na tabela para evitar falhas de mapeamento do Eloquent
        $usuario = Usuario::where('id', '=', $id)->first();

        if (!$usuario) {
            abort(404, "Usuário com o ID [{$id}] não encontrado na tabela 'usuarios'.");
        }

        $publicacoes = Publicacao::porUsuario($id);

        return view('cliente.perfil', compact('publicacoes', 'usuario'));
    }

    public function buscarPublicacoes(Request $request) {

        $termo = trim($request->input('q', ''));

        if (empty($termo)) {
            $publicacoes = [];
            return view('cliente.busca', compact('publicacoes'));
        }

        $resultadosFinais = [];

        // 1. BUSCA POR PERFIS (Normal ou utilizando o prefixo @)
        if (str_starts_with($termo, '@')) {
            $nomeUsuarioBusca = ltrim($termo, '@');
            $usuariosEncontrados = Usuario::where('nome_usuario', 'LIKE', '%' . $nomeUsuarioBusca . '%')->get();
        } else {
            $usuariosEncontrados = Usuario::where('nome', 'LIKE', '%' . $termo . '%')
                ->orWhere('nome_usuario', 'LIKE', '%' . $termo . '%')
                ->get();
        }

        // Mapeia para a estrutura aceita no array_filter do seu blade de busca
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

        // Transforma o array estruturado em uma coleção compatível com a view
        $publicacoes = collect($resultadosFinais);

        return view('cliente.busca', compact('publicacoes'));
    }

    // ================== CURTIDAS ==================

    public function curtirPublicacao($id) {

        $usuario_id = auth()->id();

        Curtida::toggleCurtida($usuario_id, $id);

        return redirect()->back();
    }

    public function minhasCurtidas() {

        $usuario_id = auth()->id();

        if (!$usuario_id) {
            return redirect()->to('/login');
        }

        // Obtém as publicações curtidas pelo usuário e a contagem total
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

    // ================== DENÚNCIAS ==================

    public function denunciarPublicacao(Request $request, $id) {

        $motivo = $request->input('motivo', 'Conteúdo impróprio');
        $gravidade = $request->input('gravidade', 'media');

        $resultado = Denuncia::criar(
