@include('layouts.header')

<div class="admin-container">
    <h2>📷 Gerenciar Publicações</h2>
    
    {{-- Sistema de alertas nativo do Laravel --}}
    @if (session('mensagem'))
        <div class="alert-success">
            {{ session('mensagem') }}
        </div>
    @endif
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Autor</th>
                <th>Legenda</th>
                <th>Imagem</th>
                <th>Status</th>
                <th style="text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($publicacoes as $pub)
            @php 
                // Garante que o item seja tratado como array ou objeto com segurança
                $pubArray = (array) $pub; 
                $statusLimpo = strtolower($pubArray['status'] ?? 'pendente');
            @endphp
            <tr>
                <td>#{{ $pubArray['id'] }}</td>
                <td>{{ $pubArray['autor_nome'] ?? 'Usuário' }}</td>
                <td style="max-width: 300px;">{{ Str::limit($pubArray['legenda'] ?? '', 50, '...') }}</td>
                <td>
                    @if (!empty($pubArray['url_imagem']))
                        <img src="{{ $pubArray['url_imagem'] }}" width="50" height="50" style="object-fit:cover; border-radius:8px;">
                    @else
                        <span>Sem imagem</span>
                    @endif
                </td>
                <td>
                    {{-- 🔥 AJUSTE: Mapeia dinamicamente classes para status terminados em 'o' ou 'a' --}}
                    <span class="status status-{{ str_replace('a', 'o', $statusLimpo) }}">
                        {{ ucfirst($pubArray['status']) }}
                    </span>
                </td>
                <td class="actions-cell">
                    {{-- 🔥 CONTAINER FLEXBOX: Alinha os formulários horizontalmente sem quebras --}}
                    <div class="actions-wrapper">
                        
                        {{-- Botão Aprovar (Aparece se não contiver 'aprovad') --}}
                        @if (!str_contains($statusLimpo, 'aprovad'))
                            <form action="{{ url('/admin/publicacoes/' . $pubArray['id'] . '/aprovar') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-aprovar">✅ Aprovar</button>
                            </form>
                        @endif
                        
                        {{-- Botão Bloquear (Aparece se não contiver 'bloquead') --}}
                        @if (!str_contains($statusLimpo, 'bloquead'))
                            <form action="{{ url('/admin/publicacoes/' . $pubArray['id'] . '/bloquear') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-bloquear">🚫 Bloquear</button>
                            </form>
                        @endif
                        
                        {{-- Botão Excluir --}}
                        <form action="{{ url('/admin/publicacoes/' . $pubArray['id'] . '/excluir') }}" method="POST" onclick="return confirm('Tem certeza?')">
                            @csrf
                            <button type="submit" class="btn-excluir">🗑️ Excluir</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
/* Mantido seu CSS original com as correções estruturais de alinhamento */
.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.admin-container h2 {
    margin-bottom: 20px;
    color: #262626;
}
.admin-table {
    width: 100%;
    background: white;
    border-collapse: collapse;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.admin-table th,
.admin-table td {
    padding: 14px 15px;
    text-align: left;
    border-bottom: 1px solid #efefef;
    vertical-align: middle;
}
.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}
/* Classes aceitando variações de gênero do banco de dados */
.status-aprovado, .status-aprovada { background: #d4edda; color: #155724; }
.status-pendente { background: #fff3cd; color: #856404; }
.status-bloqueado, .status-bloqueada { background: #f8d7da; color: #721c24; }

/* 🔥 CORREÇÃO VISUAL CRUCIAL PARA OS BOTÕES LADO A LADO */
.actions-cell {
    width: 280px; /* Garante espaço confortável na tabela */
}
.actions-wrapper {
    display: flex;
    gap: 6px;
    justify-content: center;
    align-items: center;
}
.actions-wrapper form {
    margin: 0;
    display: inline-block;
}
.actions-wrapper button {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
    white-space: nowrap; /* Evita que o texto quebre dentro do botão */
    transition: opacity 0.2s, transform 0.1s;
}
.actions-wrapper button:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}
.btn-aprovar { background: #28a745; color: white; }
.btn-bloquear { background: #ffc107; color: #212529; }
.btn-excluir { background: #dc3545; color: white; }
</style>

@include('layouts.footer')
