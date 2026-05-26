@include('layouts.header')

<div class="admin-container">
    <div class="card">
        <div class="card-header">
            <h2>📷 Detalhes da Publicação #{{ $publicacao->id }}</h2>
            <a href="{{ url('/admin/publicacoes') }}" class="btn-voltar">← Voltar</a>
        </div>
        
        <div class="card-body">
            <div class="info-group">
                <label>Autor:</label>
                <p>
                    <strong>{{ $publicacao->usuario->nome ?? 'Usuário' }}</strong> 
                    <span style="color: #8e8e8e;">{{ '@' . ($publicacao->usuario->nome_usuario ?? 'usuario') }}</span>
                </p>
            </div>
            
            <div class="info-group">
                <label>Data:</label>
                <p>{{ date('d/m/Y H:i:s', strtotime($publicacao->created_at)) }}</p>
            </div>
            
            <div class="info-group">
                <label>Status:</label>
                <p>
                    {{-- 🔥 AJUSTE: Garante compatibilidade tanto com 'aprovada/bloqueada' quanto 'aprovado/bloqueado' --}}
                    <span class="status status-{{ str_replace('a', 'o', strtolower($publicacao->status ?? 'pendente')) }}">
                        {{ ucfirst($publicacao->status ?? 'Pendente') }}
                    </span>
                </p>
            </div>
            
            <div class="info-group">
                <label>Legenda:</label>
                <div class="legenda-box">
                    {!! nl2br(e($publicacao->legenda)) !!}
                </div>
            </div>
            
            @if (!empty($publicacao->url_imagem))
                <div class="info-group">
                    <label>Imagem:</label>
                    <div class="imagem-box">
                        <img src="{{ $publicacao->url_imagem }}" class="imagem-detalhe">
                    </div>
                </div>
            @endif
            
            <div class="info-group">
                <label>Curtidas:</label>
                <p>❤️ {{ $publicacao->total_curtidas ?? ($publicacao->curtidas_count ?? 0) }} curtidas</p>
            </div>
            
            <div class="acoes">
                {{-- 🔥 AJUSTE DE STRING: Verifica tanto 'aprovado' quanto 'aprovada' --}}
                @if (!str_contains(strtolower($publicacao->status ?? ''), 'aprovad'))
                    <a href="{{ url('/admin/publicacoes/' . $publicacao->id . '/aprovar') }}" class="btn-link-acao btn-aprovar">
                        ✅ Aprovar
                    </a>
                @endif
                
                {{-- 🔥 AJUSTE DE STRING: Verifica tanto 'bloqueado' quanto 'bloqueada' --}}
                @if (!str_contains(strtolower($publicacao->status ?? ''), 'bloquead'))
                    <a href="{{ url('/admin/publicacoes/' . $publicacao->id . '/bloquear') }}" class="btn-link-acao btn-bloquear">
                        🚫 Bloquear
                    </a>
                @endif
                
                <a href="{{ url('/admin/publicacoes/' . $publicacao->id . '/excluir') }}" class="btn-link-acao btn-excluir" onclick="return confirm('Tem certeza que deseja excluir permanentemente esta publicação?')">
                    🗑️ Excluir
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.admin-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08); /* Sombra levemente mais suave e moderna */
    overflow: hidden;
}
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #efefef;
    background: #f8f9fa;
}
.card-header h2 {
    margin: 0;
    font-size: 20px;
    color: #333;
}
.btn-voltar {
    background: #6c757d;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: background 0.2s;
}
.btn-voltar:hover {
    background: #5a6268;
}
.card-body {
    padding: 24px;
}
.info-group {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #efefef;
}
.info-group label {
    display: block;
    font-weight: 600;
    color: #666;
    margin-bottom: 8px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.info-group p {
    margin: 0;
    font-size: 16px;
    color: #262626;
}
.legenda-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    font-size: 15px;
    line-height: 1.6;
    color: #333;
    border: 1px solid #eee;
}
.imagem-box {
    margin-top: 10px;
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
    display: inline-block;
}
.imagem-detalhe {
    max-width: 100%;
    max-height: 450px;
    border-radius: 6px;
    display: block;
}
.status {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
}
/* Suporte às duas variações de escrita de status */
.status-aprovado, .status-aprovada { background: #d4edda; color: #155724; }
.status-pendente, .status-pendente { background: #fff3cd; color: #856404; }
.status-bloqueado, .status-bloqueada { background: #f8d7da; color: #721c24; }

.acoes {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #efefef;
}
.btn-link-acao {
    flex: 1; /* Faz com que os botões dividam o espaço de forma limpa na tela cheia */
    max-width: 180px;
    padding: 12px 20px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 14px;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}
.btn-link-acao:hover {
    transform: translateY(-1px);
}
.btn-aprovar { background: #28a745; color: white; box-shadow: 0 2px 4px rgba(40,167,69,0.2); }
.btn-bloquear { background: #ffc107; color: #212529; box-shadow: 0 2px 4px rgba(255,193,7,0.2); }
.btn-excluir { background: #dc3545; color: white; box-shadow: 0 2px 4px rgba(220,53,69,0.2); }

.btn-aprovar:hover { background: #218838; }
.btn-bloquear:hover { background: #e0a800; }
.btn-excluir:hover { background: #c82333; }
</style>

@include('layouts.footer')
