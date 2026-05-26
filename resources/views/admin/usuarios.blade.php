@include('layouts.header')

<div class="admin-container">
    <h2>👥 Usuários Cadastrados</h2>
    
    @if (empty($usuarios) || count($usuarios) === 0)
        <div class="empty-state">
            <p>📭 Nenhum usuário encontrado no sistema.</p>
        </div>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Usuário</th>
                    <th>E-mail</th>
                    <th>Tipo</th>
                    <th>Cadastro</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $user)
                <tr>
                    {{-- ✅ CORREÇÃO: Acessando diretamente como objeto Eloquent com -> --}}
                    <td>#{{ $user->id }}</td>
                    <td>{{ $user->nome ?? $user->name }}</td>
                    <td>{{ '@' . ($user->nome_usuario ?? 'usuario') }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ ($user->tipo ?? 'user') == 'admin' ? 'badge-admin' : 'badge-user' }}">
                            {{ ucfirst($user->tipo ?? 'user') }}
                        </span>
                    </td>
                    {{-- Usa created_at que é o padrão do banco no Laravel --}}
                    <td>{{ date('d/m/Y', strtotime($user->created_at)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<style>
/* Estilização original mantida e otimizada */
.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.admin-container h2 {
    margin-bottom: 20px;
    color: #262626;
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

.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-admin {
    background: #e8daff;
    color: #6f42c1;
}

.badge-user {
    background: #e2e3e5;
    color: #383d41;
}
</style>

@include('layouts.footer')
