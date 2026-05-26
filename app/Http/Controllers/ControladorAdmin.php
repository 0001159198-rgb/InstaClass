<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User as Usuario;    // 📦 Apelidando o modelo User padrão do Laravel como Usuario
use App\Models\Publicacao; 
use App\Models\Denuncia;   

class ControladorAdmin extends Controller {

    /**
     * Valida se o usuário é Administrador utilizando a sessão e o Auth.
     * Retorna um booleano para controle limpo do fluxo do controlador.
     */
    private function eAdmin() {
        if (!auth()->check()) {
            return false;
        }
        
        $tipo = auth()->user()->tipo ?? session('usuario_tipo');
        return $tipo === 'admin';
    }

    // ================== DASHBOARD ==================

    public function dashboard() {
        if (!$this->eAdmin()) {
            return redirect()->to('/login')->with('erro', 'Acesso restrito a administradores.');
        }

        // Contagens nativas do banco de dados eficientes
        $totalUsuarios = Usuario::count();
        $totalPublicacoes = Publicacao::count();
        $totalDenuncias = Denuncia::count();
        
        // 🔥 PADRONIZAÇÃO: Aceita variações masculinas e femininas de pendentes para segurança
        $totalPendentes = Publicacao::whereIn('status', ['pendente', 'PENDENTE'])->count();
        
        // Buscar denúncias recentes com segurança usando relacionamentos do Eloquent
        $denunciasRecentes = Denuncia::with(['publicacao', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
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
        if (!$this->eAdmin()) {
            return redirect()->to('/login');
        }
        
        try {
            $usuarios = Usuario::orderBy('id', 'desc')->get();
        } catch (\Exception $e) {
            $usuarios = Usuario::all();
        }

        return view('admin.usuarios', compact('usuarios'));
    }

    public function listarPublicacoes() {
        if (!$this->eAdmin()) {
            return redirect()->to('/login');
        }

        // 🔥 CORREÇÃO: Traz as publicações com o nome do autor para a tabela do admin não quebrar
        try {
            $publicacoes = DB::table('publicacoes')
                ->join('usuarios', 'publicacoes.usuario_id', '=', 'usuarios.id')
                ->select('publicacoes.*', 'usuarios.nome as autor_nome')
                ->orderBy('publicacoes.created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            $publicacoes = Publicacao::orderBy('created_at', 'desc')->get();
        }

        return view('admin.publicacoes', compact('publicacoes'));
    }

    public function listarDenuncias() {
        if (!$this->eAdmin()) {
            return redirect()->to('/login');
        }
        
        // Carrega adiantado (Eager Loading) os relacionamentos para evitar erro de chaves na view
        $denuncias = Denuncia::with(['publicacao', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.denuncias', compact('denuncias'));
    }

    // ================== AÇÕES SOBRE AS PUBLICAÇÕES ==================

    public function verPublicacao($id) {
        if (!$this->eAdmin()) {
            return redirect()->to('/login');
        }
        
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        $publicacao = Publicacao::find($id);

        if (!$publicacao) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Publicação não encontrada!');
        }
        
        return view('admin.ver_publicacao', compact('publicacao'));
    }

    public function aprovarPublicacao($id) {
        if (!$this->eAdmin()) {
            return redirect()->to('/login');
        }
        
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        try {
            // 🔥 CORREÇÃO: Alterado de 'aprovada' para 'aprovado' para casar perfeitamente com o BuscarController e o Feed
            DB::table('publicacoes')->where('id', $id)->update(['status' => 'aprovado']);
            return redirect()->to('/admin/publicacoes')->with('mensagem', "✅ Publicação #$id aprovada com sucesso e liberada para o Feed!");
        } catch (\Exception $e) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Erro ao aprovar publicação: ' . $e->getMessage());
        }
    }

    public function determinarStatus($id, $status, $mensagemSucesso) {
        if (!$this->eAdmin()) {
            return redirect()->to('/login');
        }
        
        try {
            DB::table('publicacoes')->where('id', $id)->update(['status' => $status]);
            session()->flash('mensagem', $mensagemSucesso);
        } catch (\Exception $e) {
            session()->flash('erro', 'Erro ao atualizar publicação: ' . $e->getMessage());
        }
    }

    public function bloquearPublicacao($id) {
        if (!$this->eAdmin()) {
            return redirect()->to('/login');
        }
        
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        try {
            // 🔥 CORREÇÃO: Alterado de 'bloqueada' para 'bloqueado' para manter o padrão correto no banco
            DB::table('publicacoes')->where('id', $id)->update(['status' => 'bloqueado']);
            return redirect()->to('/admin/publicacoes')->with('mensagem', "🚫 Publicação #$id bloqueada com sucesso!");
        } catch (\Exception $e) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Erro ao bloquear publicação: ' . $e->getMessage());
        }
    }

    public function excluirPublicacao($id) {
        if (!$this->eAdmin()) {
            return redirect()->to('/login');
        }
        
        if (empty($id) || !is_numeric($id)) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'ID de publicação inválido.');
        }
        
        try {
            DB::transaction(function () use ($id) {
                DB::table('curtidas')->where('publicacao_id', $id)->delete();
                DB::table('denuncias')->where('publicacao_id', $id)->delete();
                DB::table('publicacoes')->where('id', $id)->delete();
            });
            
            return redirect()->to('/admin/publicacoes')->with('mensagem', "🗑️ Publicação #$id excluída permanentemente do sistema!");
        } catch (\Exception $e) {
            return redirect()->to('/admin/publicacoes')->with('erro', 'Erro ao excluir publicação: ' . $e->getMessage());
        }
    }

    // ================== AÇÕES SOBRE AS DENÚNCIAS ==================

    /**
     * ✅ MÉTODO ADICIONADO: Executa a auditoria e altera o status das denúncias no banco
     */
    public function analisarDenuncia($id) {
        if (!$this->eAdmin()) {
            return redirect()->to('/login')->with('erro', 'Acesso negado.');
        }

        if (empty($id) || !is_numeric($id)) {
            return redirect()->back()->with('erro', 'ID de denúncia inválido.');
        }

        try {
            // Localiza a denúncia com o Eloquent ou gera uma falha limpa controlada
            $denuncia = Denuncia::findOrFail($id);
            
            // Sincroniza o status para mantê-lo resolvido/analisado
            $denuncia->status = 'analisada';
            $denuncia->save();

            return redirect()->back()->with('mensagem', "✅ Denúncia #$id marcada como analisada com sucesso!");
        } catch (\Exception $e) {
            return redirect()->back()->with('erro', 'Erro ao atualizar processamento da denúncia: ' . $e->getMessage());
        }
    }
}
