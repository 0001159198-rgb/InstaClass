<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;    
use App\Models\Publicacao; 
use App\Models\Denuncia;   

class ControladorAdmin extends Controller {

    /**
     * Função auxiliar privada para validar se o usuário é Administrador.
     * Evita o erro de encadeamento de middleware do construtor no Laravel 11.
     */
    private function checarAdmin() {
        if (!auth()->check() || auth()->user()->tipo !== 'admin') {
            redirect()->to('/login')->send();
            exit();
        }
    }

    // ================== DASHBOARD ==================

    public function dashboard() {
        $this->checarAdmin(); // Garante a proteção da rota

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
        $this->checarAdmin();
        $usuarios = Usuario::todos();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function listarPublicacoes() {
        $this->checarAdmin();
        $publicacoes = Publicacao::todas();
        return view('admin.publicacoes', compact('publicacoes'));
    }

    public function listarDenuncias() {
        $this->checarAdmin();
        $denuncias = Denuncia::all();
        return view('admin.denuncias', compact('denuncias'));
    }

    // ================== AÇÕES SOBRE AS PUBLICAÇÕES ==================

    public function verPublicacao($id) {
        $this->checarAdmin();
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
        $this->checarAdmin();
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        try {
            DB::table('publicacoes')->where('id', $id)->update(['status' => 'aprovado']);
            return redirect()->to('/admin/publicacoes')->with('mensagem', "✅ Publicação #$id aprovada com sucesso!");
        } catch (\Exception $e) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Erro ao aprovar publicação: ' . $e->getMessage());
        }
    }

    public function determinarStatus($id, $status, $mensagemSucesso) {
        $this->checarAdmin();
        try {
            DB::table('publicacoes')->where('id', $id)->update(['status' => $status]);
            session()->flash('mensagem', $mensagemSucesso);
        } catch (\Exception $e) {
            session()->flash('erro', 'Erro ao atualizar publicação: ' . $e->getMessage());
        }
    }

    public function bloquearPublicacao($id) {
        $this->checarAdmin();
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
        $this->checarAdmin();
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        try {
            DB::transaction(function () use ($id) {
                DB::table('curtidas')->where('publicacao_id', $id)->delete();
                DB::table('denuncias')->where('publicacao_id', $id)->delete();
                DB::table('publicacoes')->where('id', $id)->delete();
            });
            
            return redirect()->to('/admin/publicacoes')->with('mensagem', "🗑️ Publicação #$id excluída permanentemente!");
        } catch (\Exception $e) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Erro ao excluir publicação: ' . $e->getMessage());
        }
    }
}