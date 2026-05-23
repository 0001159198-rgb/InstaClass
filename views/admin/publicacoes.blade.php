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
                <th>Ações</th>
            </tr>
        </table>
        </thead>
        <tbody>
            @foreach ($publicacoes as $pub)
            @php 
                // Garante que o item seja tratado como array ou objeto com segurança
                $pubArray = (array) $pub; 
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
                    <span class="status status-{{ $pubArray['status'] }}">
                        {{ ucfirst($pubArray['status']) }}
                    </span>
                </td>
                <td class="actions">
                    {{-- Mudança para botões com formulário POST para bater certinho com suas rotas --}}
                    @if ($pubArray['status'] != 'aprovado')
                        <form action="{{ url('/admin/publicacoes/' . $pubArray['id'] . '/aprovar') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-aprovar">✅ Aprovar</button>
                        </form>
                    @endif
                    
                    @if ($pubArray['status'] != 'bloqueado')
                        <form action="{{ url('/admin/publicacoes/' . $pubArray['id'] . '/bloquear') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-bloquear">🚫 Bloquear</button>
                        </form>
                    @endif
                    
                    <form action="{{ url('/admin/publicacoes/' . $pubArray['id'] . '/excluir') }}" method="POST" style="display:inline;" onclick="return confirm('Tem certeza?')">
                        @csrf
                        <button type="submit" class="btn-excluir">🗑️ Excluir</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
/* Mantido seu CSS original e adicionados pequenos ajustes para alinhar os novos botões */
.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
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
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.admin-table th,
.admin-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #efefef;
}
.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}
.status-aprovado { background: #d4edda; color: #155724; }
.status-pendente { background: #fff3cd; color: #856404; }
.status-bloqueado { background: #f8d7da; color: #721c24; }

.actions form {
    margin: 0 2px;
}
.actions button {
    display: inline-block;
    padding: 5px 10px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: opacity 0.2s;
}
.actions button:hover {
    opacity: 0.85;
}
.btn-aprovar { background: #28a745; color: white; }
.btn-bloquear { background: #ffc107; color: #333; }
.btn-excluir { background: #dc3545; color: white; }
</style>

@include('layouts.footer')