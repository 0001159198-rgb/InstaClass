@include('layouts.header')

<div class="admin-container">
    <h2>🚨 Denúncias Recebidas</h2>
    
    {{-- Sistema de alertas nativo do Laravel --}}
    @if (session('mensagem'))
        <div class="alert-success">
            {{ session('mensagem') }}
        </div>
    @endif
    
    @if (empty($denuncias) || count($denuncias) === 0)
        <div class="empty-state">
            <p>📭 Nenhuma denúncia encontrada.</p>
        </div>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Publicação</th>
                    <th>Autor</th>
                    <th>Motivo</th>
                    <th>Gravidade</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($denuncias as $d)
                @php 
                    // Garante que o item seja tratado como array com segurança
                    $dArray = (array) $d; 
                @endphp
                <tr>
                    <td>#{{ $dArray['id'] }}</td>
                    <td style="max-width: 200px;">
                        {{ Str::limit($dArray['publicacao_legenda'] ?? '', 50, '...') }}
                    </td>
                    <td>
                        <strong>{{ $dArray['usuario_nome'] ?? 'Usuário' }}</strong>
                    </td>
                    <td>{{ $dArray['motivo'] }}</td>
                    <td>
                        <span class="gravidade gravidade-{{ $dArray['gravidade'] }}">
                            {{ ucfirst($dArray['gravidade']) }}
                        </span>
                    </td>
                    <td>
                        <span class="status status-{{ $dArray['status'] }}">
                            {{ ucfirst($dArray['status']) }}
                        </span>
                    </td>
                    <td>{{ date('d/m/Y H:i', strtotime($dArray['criado_em'])) }}</td>
                    <td class="actions">
                        <a href="{{ url('/admin/publicacoes/' . $dArray['publicacao_id']) }}" class="btn-ver">👁️ Ver Post</a>
                        
                        @if ($dArray['status'] == 'pendente')
                            <a href="{{ url('/admin/denuncias/' . $dArray['id'] . '/analisar') }}" class="btn-analisar">✅ Analisar</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<style>
/* Mantido seu CSS original perfeitamente intacto */
.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.admin-container h2 {
    margin-bottom: 20px;
    color: #262626;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.empty-state {
    text-align: center;
    padding: 60px 30px;
    background: white;
    border: 1px solid #dbdbdb;
    border-radius: 12px;
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

.gravidade {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.gravidade-baixa {
    background: #d4edda;
    color: #155724;
}

.gravidade-media {
    background: #fff3cd;
    color: #856404;
}

.gravidade-alta {
    background: #f8d7da;
    color: #721c24;
}

.status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-pendente {
    background: #fff3cd;
    color: #856404;
}

.status-analisada {
    background: #d4edda;
    color: #155724;
}

.status-resolvida {
    background: #cfe2ff;
    color: #004085;
}

.actions a {
    display: inline-block;
    margin: 0 5px;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 6px;
    font-size: 12px;
}

.btn-ver {
    background: #17a2b8;
    color: white;
}

.btn-analisar {
    background: #28a745;
    color: white;
}

.btn-excluir {
    background: #dc3545;
    color: white;
}
</style>

@include('layouts.footer')