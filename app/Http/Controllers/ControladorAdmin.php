<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User as Usuario;    // 📦 Apelidando o modelo User padrão do Laravel como Usuario
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

        // CORREÇÃO: Usando contagens nativas do banco para poupar memória e evitar chamadas de métodos inexistentes
        $totalUsuarios = Usuario::count();
        $totalPublicacoes = Publicacao::count();
        $totalDenuncias = Denuncia::count();
        $totalPendentes = Publicacao::where('status', '=', 'pendente')->count();
        
        // Buscar denúncias recentes (Com tratamento preventivo caso o método customizado falhe)
        try {
            $denunciasRecentes = Denuncia::recentes(5);
        } catch (\BadMethodCallException | \Error $e) {
            $denunciasRecentes = Denuncia::orderBy('created_at', 'desc')->take(5)->get();
        }
        
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
        
        // CORREÇÃO: Fallback preventivo caso o método estático customizado 'todos()' não esteja definido
        try {
            $usuarios = Usuario::todos();
        } catch (\BadMethodCallException | \Error $e) {
            $usuarios = Usuario::orderBy('name', 'asc')->get();
        }

        return view('admin.usuarios', compact('usuarios'));
    }

    public function listarPublicacoes() {
        $this->checarAdmin();

        // CORREÇÃO: Fallback preventivo utilizando o método nativo all() do Laravel
        try {
            $publicacoes = Publicacao::todas();
        } catch (\BadMethodCallException | \Error $e) {
            $publicacoes = Publicacao::orderBy('created_at', 'desc')->get();
        }

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
        
        // CORREÇÃO: Utilizando find() nativo para evitar falha no método customizado buscarPorId()
        try {
            $publicacao = Publicacao::buscarPorId($id);
        } catch (\BadMethodCallException | \Error $e) {
            $publicacao = Publicacao::find($id);
        }

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
            // Ajustado para manter compatibilidade com o status buscado pelo feed ('aprovada')
            DB::table('publicacoes')->where('id', $id)->update(['status' => 'aprovada']);
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
