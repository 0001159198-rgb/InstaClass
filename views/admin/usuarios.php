@include('layouts.header')

<div class="admin-container">
    <h2>👥 Gerenciar Usuários</h2>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Usuário</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $user)
            @php 
                // Proteção para garantir que o registro possa ser lido como array com segurança
                $userArray = (array) $user; 
            @endphp
            <tr>
                <td>#{{ $userArray['id'] }}</td>
                <td>{{ $userArray['nome'] }}</td>
                <td>@{{ $userArray['nome_usuario'] }}</td>
                <td>{{ $userArray['email'] }}</td>
                <td>
                    <span class="badge {{ $userArray['tipo'] == 'admin' ? 'badge-admin' : 'badge-user' }}">
                        {{ $userArray['tipo'] }}
                    </span>
                </td>
                <td>{{ date('d/m/Y', strtotime($userArray['criado_em'])) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
    background: #d4edda;
    color: #155724;
}
.badge-user {
    background: #e2e3e5;
    color: #383d41;
}
</style>

@include('layouts.footer')