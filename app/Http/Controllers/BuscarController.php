<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuscarController extends Controller
{
    public function index(Request $request)
    {
        // Pega o termo digitado pelo usuário
        $termo = $request->input('q');
        
        // Inicializa o array de resultados vazio
        $resultados = [];

        // Só faz a busca se o usuário digitou alguma coisa
        if (!empty($termo)) {
            
            // Regra: Se o usuário digitou "@dada", removemos o "@" para a busca funcionar
            $termoLimpo = ltrim($termo, '@');

            // 👥 1. BUSCA DE PERFIS (Começa com a letra pesquisada - Case Insensitive)
            $perfis = DB::table('usuarios')
                ->where('nome_usuario', 'LIKE', $termoLimpo . '%')
                ->orWhere('nome', 'LIKE', $termoLimpo . '%')
                ->select('id', 'nome', 'nome_usuario', 'email', DB::raw("'perfil' as tipo"))
                ->get()
                ->toArray();

            // 📷 2. BUSCA DE PUBLICAÇÕES (Agora também COMEÇA com a palavra pesquisada - Case Insensitive)
            $posts = DB::table('publicacoes')
                ->join('usuarios', 'publicacoes.usuario_id', '=', 'usuarios.id')
                ->leftJoin('curtidas', 'publicacoes.id', '=', 'curtidas.publicacao_id')
                // 🔥 MUDANÇA AQUI: O '%' ficou apenas no final para buscar posts que COMEÇAM com o termo
                ->where('publicacoes.legenda', 'LIKE', $termoLimpo . '%')
                ->where('publicacoes.status', '=', 'aprovado') // Garante apenas posts aprovados
                ->select(
                    'publicacoes.id',
                    'publicacoes.usuario_id',
                    'publicacoes.url_imagem',
                    'publicacoes.legenda',
                    'publicacoes.created_at as criado_em',
                    'usuarios.nome as autor_nome',
                    'usuarios.nome_usuario as autor_username',
                    DB::raw('COUNT(curtidas.id) as total_curtidas')
                )
                ->groupBy('publicacoes.id', 'publicacoes.usuario_id', 'publicacoes.url_imagem', 'publicacoes.legenda', 'publicacoes.created_at', 'usuarios.nome', 'usuarios.nome_usuario')
                ->get()
                ->toArray();

            // Unifica os dois resultados mantendo o padrão que a sua View espera
            $resultados = array_merge($perfis, $posts);
        }

        // Retorna a view 'buscar.blade.php' com os dados filtrados
        return view('cliente.buscar', ['publicacoes' => $resultados]);
    }
}
