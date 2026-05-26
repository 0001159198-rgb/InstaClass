@include('layouts.header')

<div class="busca-container">
    <div class="busca-header">
        <h2>🔎 Buscar Publicações e Perfis</h2>
    </div>
    
    <form method="GET" action="{{ url('/buscar') }}" class="search-form">
        <input type="text" 
               name="q" 
               class="search-input"
               placeholder="Buscar por legenda, nome ou use @ para buscar perfis (ex: @admin)" 
               value="{{ request('q') }}"
               autofocus>
        <button type="submit" class="search-button">🔍 Buscar</button>
    </form>
    
    @if (request()->has('q') && !empty(request('q')))
        <div class="result-info">
            <p>
                Resultados para: <strong>"{{ request('q') }}"</strong>
                ({{ count($publicacoes) }} resultado(s) encontrado(s))
            </p>
        </div>
    @endif
    
    @if (empty($publicacoes))
        <div class="empty-state">
            <p>📭 Nenhum resultado encontrado.</p>
            @if (request()->has('q') && !empty(request('q')))
                <p style="margin-top: 10px;">Tente buscar por outra palavra ou use @ para buscar perfis.</p>
            @endif
            <a href="{{ url('/publicacoes/criar') }}" class="btn-criar">➕ Criar publicação</a>
        </div>
    @else
        
        @php 
            // Isola a lógica de filtros de array perfeitamente dentro do Blade
            $publicacoesArray = is_array($publicacoes) ? $publicacoes : $publicacoes->toArray();

            $perfis = array_filter($publicacoesArray, function($item) {
                $item = (array) $item;
                return isset($item['tipo']) && $item['tipo'] == 'perfil';
            });

            $posts = array_filter($publicacoesArray, function($item) {
                $item = (array) $item;
                return !isset($item['tipo']) || $item['tipo'] != 'perfil';
            });
        @endphp
        
        {{-- Seção de Perfis --}}
        @if (!empty($perfis))
            <div class="result-section">
                <h3>👥 Perfis Encontrados</h3>
                @foreach ($perfis as $perfil)
                    @php $perfil = (array) $perfil; @endphp
                    <div class="perfil-result">
                        <div class="perfil-avatar-mini">
                            {{ strtoupper(substr($perfil['nome'] ?? 'U', 0, 1)) }}
                        </div>
                        <div class="perfil-info">
                            <a href="{{ url('/perfil/' . $perfil['id']) }}" class="perfil-nome">
                                {{ $perfil['nome'] }}
                            </a>
                            {{-- 🔥 CORREÇÃO: Isolado o @ para o blade processar a variável --}}
                            <div class="perfil-username">
                                <span>@</span>{{ $perfil['nome_usuario'] ?? 'usuario' }}
                            </div>
                            <div class="perfil-stats">
                                📧 {{ $perfil['email'] }}
                            </div>
                        </div>
                        <a href="{{ url('/perfil/' . $perfil['id']) }}" class="btn-visitar">Ver Perfil</a>
                    </div>
                @endforeach
            </div>
        @endif
        
        {{-- Seção de Publicações --}}
        @if (!empty($posts))
            <div class="result-section">
                <h3>📷 Publicações Encontradas</h3>
                @foreach ($posts as $pub)
                    @php $pub = (array) $pub; @endphp
                    <div class="post" style="background: white; border: 1px solid #dbdbdb; border-radius: 12px; padding: 15px; margin-bottom: 20px;">
                        <div class="post-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div class="post-avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; color: white; font-weight: bold;">
                                {{ strtoupper(substr($pub['autor_nome'] ?? 'U', 0, 1)) }}
                            </div>
                            <div class="post-info">
                                <a href="{{ url('/perfil/' . $pub['usuario_id']) }}" class="post-nome" style="font-weight: bold; color: #262626; text-decoration: none;">
                                    {{ $pub['autor_nome'] ?? 'Usuário' }}
                                </a>
                                {{-- 🔥 CORREÇÃO: Removido o arroba grudado e ajustado para 'autor_username' que vem da query --}}
                                <div class="post-usuario" style="font-size: 13px; color: #8e8e8e;">
                                    <span>@</span>{{ $pub['autor_username'] ?? $pub['nome_usuario'] ?? 'usuario' }}
                                </div>
                                <div class="post-data" style="font-size: 11px; color: #b3b3b3; margin-top: 2px;">
                                    {{ date('d/m/Y \à\s H:i', strtotime($pub['criado_em'] ?? $pub['created_at'] ?? 'now')) }}
                                </div>
                            </div>
                        </div>
                        
                        <p class="post-legenda" style="font-size: 14px; line-height: 1.5; color: #222; margin: 10px 0;">{!! nl2br(e($pub['legenda'])) !!}</p>
                        
                        @if (!empty($pub['url_imagem']))
                            <img src="{{ $pub['url_imagem'] }}" class="post-imagem" style="width: 100%; max-height: 450px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;" onerror="this.src='https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600'">
                        @endif
                        
                        <div class="post-acoes" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #efefef; padding-top: 10px; margin-top: 10px;">
                            <a href="{{ url('/publicacoes/' . $pub['id'] . '/curtir') }}" class="btn-curtir" style="text-decoration: none; font-size: 14px; color: #262626; font-weight: 600;">
                                ❤️ Curtir (<span>{{ $pub['total_curtidas'] ?? 0 }}</span>)
                            </a>
                            <button type="button" class="btn-denunciar" onclick="abrirModal({{ $pub['id'] }})" style="background: none; border: none; color: #e74c3c; font-size: 13px; font-weight: bold; cursor: pointer;">
                                🚨 Denunciar
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
    @endif
</div>

<style>
/* Seus estilos originais perfeitamente preservados */
.busca-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    box-sizing: border-box;
}
.busca-header {
    margin-bottom: 20px;
}
.search-form {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
.search-input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #dbdbdb;
    border-radius: 8px;
    font-size: 14px;
}
.search-button {
    padding: 10px 20px;
    background: #0095f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}
.result-info {
    margin-bottom: 20px;
    color: #262626;
    font-size: 14px;
}
.empty-state {
    text-align: center;
    padding: 40px 20px;
    background: white;
    border: 1px solid #dbdbdb;
    border-radius: 12px;
    color: #8e8e8e;
}
.btn-criar {
    display: inline-block;
    margin-top: 15px;
    padding: 8px 16px;
    background: #0095f6;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
}
.result-section {
    margin-bottom: 30px;
}
.result-section h3 {
    font-size: 18px;
    color: #262626;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #efefef;
}
.perfil-result {
    display: flex;
    align-items: center;
    gap: 15px;
    background: white;
    border: 1px solid #dbdbdb;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 10px;
    transition: box-shadow 0.2s;
}
.perfil-result:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.perfil-avatar-mini {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    font-weight: bold;
}
.perfil-info {
    flex: 1;
}
.perfil-nome {
    font-size: 16px;
    font-weight: bold;
    color: #262626;
    text-decoration: none;
}
.perfil-nome:hover {
    text-decoration: underline;
}
.perfil-username {
    font-size: 13px;
    color: #8e8e8e;
    margin-top: 3px;
}
.perfil-stats {
    font-size: 12px;
    color: #b3b3b3;
    margin-top: 3px;
}
.btn-visitar {
    padding: 8px 16px;
    background: #0095f6;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: background 0.2s;
}
.btn-visitar:hover {
    background: #0077cc;
}
</style>

@include('layouts.footer')
