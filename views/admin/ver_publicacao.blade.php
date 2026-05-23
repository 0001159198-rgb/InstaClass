@include('layouts.header')

<div class="admin-container">
    <div class="card">
        <div class="card-header">
            <h2>📷 Detalhes da Publicação #{{ $publicacao['id'] }}</h2>
            <a href="{{ url('/admin/publicacoes') }}" class="btn-voltar">← Voltar</a>
        </div>
        
        <div class="card-body">
            <div class="info-group">
                <label>Autor:</label>
                <p><strong>{{ $publicacao['autor_nome'] ?? 'Usuário' }}</strong> (@{{ $publicacao['nome_usuario'] ?? 'usuario' }})</p>
            </div>
            
            <div class="info-group">
                <label>Data:</label>
                <p>{{ date('d/m/Y H:i:s', strtotime($publicacao['criado_em'])) }}</p>
            </div>
            
            <div class="info-group">
                <label>Status:</label>
                <p>
                    <span class="status status-{{ $publicacao['status'] }}">
                        {{ ucfirst($publicacao['status']) }}
                    </span>
                </p>
            </div>
            
            <div class="info-group">
                <label>Legenda:</label>
                <div class="legenda-box">
                    {{-- Renderiza quebras de linha com segurança XSS no Laravel --}}
                    {!! nl2br(e($publicacao['legenda'])) !!}
                </div>
            </div>
            
            @if (!empty($publicacao['url_imagem']))
                <div class="info-group">
                    <label>Imagem:</label>
                    <div class="imagem-box">
                        <img src="{{ $publicacao['url_imagem'] }}" class="imagem-detalhe">
                    </div>
                </div>
            @endif
            
            <div class="info-group">
                <label>Curtidas:</label>
                <p>❤️ {{ $publicacao['total_curtidas'] ?? 0 }} curtidas</p>
            </div>
            
            <div class="acoes">
                {{-- Modificado para botões de formulário POST para bater com suas rotas protegidas --}}
                @if ($publicacao['status'] != 'aprovado')
                    <form action="{{ url('/admin/publicacoes/' . $publicacao['id'] . '/aprovar') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-aprovar">✅ Aprovar</button>
                    </form>
                @endif
                
                @if ($publicacao['status'] != 'bloqueado')
                    <form action="{{ url('/admin/publicacoes/' . $publicacao['id'] . '/bloquear') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-bloquear">🚫 Bloquear</button>
                    </form>
                @endif
                
                <form action="{{ url('/admin/publicacoes/' . $publicacao['id'] . '/excluir') }}" method="POST" onclick="return confirm('Tem certeza?')">
                    @csrf
                    <button type="submit" class="btn-excluir">🗑️ Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Seu CSS original com ajustes finos nos seletores de botões */
.admin-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
}
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
}
.btn-voltar {
    background: #6c757d;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
}
.card-body {
    padding: 20px;
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
    line-height: 1.5;
}
.imagem-detalhe {
    max-width: 100%;
    max-height: 400px;
    border-radius: 8px;
}
.status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}
.status-aprovado { background: #d4edda; color: #155724; }
.status-pendente { background: #fff3cd; color: #856404; }
.status-bloqueado { background: #f8d7da; color: #721c24; }

.acoes {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #efefef;
}
.acoes button {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: opacity 0.2s;
}
.acoes button:hover {
    opacity: 0.85;
}
.btn-aprovar { background: #28a745; color: white; }
.btn-bloquear { background: #ffc107; color: #333; }
.btn-excluir { background: #dc3545; color: white; }
</style>

@include('layouts.footer')