<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;    // Ajuste o namespace para seus Models reais
use App\Models\Publicacao; // Ajuste o namespace para seus Models reais
use App\Models\Denuncia;   // Ajuste o namespace para seus Models reais

class ControladorAdmin extends Controller {

    /**
     * Construtor do Controlador.
     * Define que apenas usuários logados do tipo 'admin' podem acessar estes métodos.
     * Isso substitui a função manual 'verificarAdmin()' usando a proteção nativa do Laravel.
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->tipo !== 'admin') {
                return redirect()->to('/login')->send();
            }
            return $next($request);
        });
    }

    // ================== DASHBOARD ==================

    public function dashboard() {
        // Buscar dados para os indicadores
        $totalUsuarios = Usuario::total();
        $totalPublicacoes = count(Publicacao::todas());
        $totalDenuncias = count(Denuncia::all());
        $totalPendentes = count(Publicacao::pendentes());
        
        // Buscar denúncias recentes
        $denunciasRecentes = Denuncia::recentes(5);
        
        return view('admin.dashboard', compact(
            'totalUsuarios', 
            'totalPublicacoes', 
            'totalDenuncias', 
            'totalPendentes', 
            'denunciasRecentes'
        ));
    }

    // ================== LISTAGENS ==================

    public function listarUsuarios() {
        $usuarios = Usuario::todos();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function listarPublicacoes() {
        $publicacoes = Publicacao::todas();
        return view('admin.publicacoes', compact('publicacoes'));
    }

    public function listarDenuncias() {
        $denuncias = Denuncia::all();
        return view('admin.denuncias', compact('denuncias'));
    }

    // ================== AÇÕES SOBRE AS PUBLICAÇÕES ==================

    public function verPublicacao($id) {
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        $publicacao = Publicacao::buscarPorId($id);
        if (!$publicacao) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Publicação não encontrada!');
        }
        
        return view('admin.ver_publicacao', compact('publicacao'));
    }

    public function aprovarPublicacao($id) {
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        try {
            // Substituição do Database::connect() pelo Query Builder do Laravel
            DB::table('publicacoes')->where('id', $id)->update(['status' => 'aprovado']);
            
            return redirect()->to('/admin/publicacoes')->with('mensagem', "✅ Publicação #$id aprovada com sucesso!");
        } catch (\Exception $e) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Erro ao aprovar publicação: ' . $e->getMessage());
        }
    }

    public function determinarStatus($id, $status, $mensagemSucesso) {
        try {
            DB::table('publicacoes')->where('id', $id)->update(['status' => $status]);
            session()->flash('mensagem', $mensagemSucesso);
        } catch (\Exception $e) {
            session()->flash('erro', 'Erro ao atualizar publicação: ' . $e->getMessage());
        }
    }

    public function bloquearPublicacao($id) {
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        try {
            DB::table('publicacoes')->where('id', $id)->update(['status' => 'bloqueado']);
            
            return redirect()->to('/admin/publicacoes')->with('mensagem', "🚫 Publicação #$id bloqueada com sucesso!");
        } catch (\Exception $e) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Erro ao bloquear publicação: ' . $e->getMessage());
        }
    }

    public function excluirPublicacao($id) {
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        try {
            // Gerencia a exclusão em lote de forma limpa usando o DB do Laravel
            DB::transaction(function () use ($id) {
                // Primeiro, excluir as curtidas relacionadas
                DB::table('curtidas')->where('publicacao_id', $id)->delete();
                
                // Depois, excluir as denúncias relacionadas
                DB::table('denuncias')->where('publicacao_id', $id)->delete();
                
                // Por fim, excluir a publicação
                DB::table('publicacoes')->where('id', $id)->delete();
            });
            
            return redirect()->to('/admin/publicacoes')->with('mensagem', "🗑️ Publicação #$id excluída permanentemente!");
        } catch (\Exception $e) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Erro ao excluir publicação: ' . $e->getMessage());
        }
    }
}