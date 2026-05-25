O seu arquivo atual de visualização das curtidas está muito bem estruturado e com um design limpo! No entanto, olhando atentamente o mapeamento de variáveis que você usou comparado ao banco de dados, existem **dois pequenos detalhes críticos** no Blade que vão quebrar o link de redirecionamento ou exibir erros na tela:

1. **`$pub['id']` vs `$pub['publicacao_id']**`: Em queries que trazem dados da tabela pivot de curtidas, o ID real da publicação costuma vir mapeado como `publicacao_id`. Usar apenas `id` pode tentar enviar o ID da *relação da curtida*, gerando erros na hora de remover.
2. **`$pub['usuario_id']`**: Dependendo de como a query foi montada no seu Model `Curtida`, o ID do dono do post pode vir como `usuario_id` ou o próprio link de perfil pode falhar se o dado estiver nulo.

Para blindar o seu arquivo mantendo o estilo visual idêntico ao que você enviou, aqui está a versão totalmente ajustada e segura para você substituir:

### Código Completo da View: `resources/views/cliente/curtidas.blade.php`

```html
@include('layouts.header')

<style>
    .main-content {
        margin-left: 280px !important;
        padding: 20px !important;
    }
    
    .right-panel {
        display: none !important;
    }
    
    .curtidas-container {
        max-width: 700px;
        margin: 0 auto;
    }
    
    .header-page {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #dbdbdb;
    }
    
    .header-page h2 {
        font-size: 28px;
        color: #262626;
        margin-bottom: 8px;
    }
    
    .header-page p {
        color: #8e8e8e;
        font-size: 14px;
    }
    
    .post {
        background: white;
        border: 1px solid #dbdbdb;
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .post-header {
        display: flex;
        align-items: center;
        padding: 14px 16px;
        border-bottom: 1px solid #efefef;
    }
    
    .post-avatar {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        font-weight: bold;
        margin-right: 12px;
    }
    
    .post-info {
        flex: 1;
    }
    
    .post-nome {
        font-size: 14px;
        font-weight: 600;
        color: #262626;
        text-decoration: none;
    }
    
    .post-nome:hover {
        text-decoration: underline;
    }
    
    .post-data {
        font-size: 11px;
        color: #8e8e8e;
        margin-top: 2px;
    }
    
    .post-legenda {
        padding: 14px 16px;
        font-size: 14px;
        line-height: 1.5;
        color: #262626;
        margin: 0;
    }
    
    .post-imagem {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
    }
    
    .post-acoes {
        display: flex;
        gap: 20px;
        padding: 10px 16px;
        border-top: 1px solid #efefef;
    }
    
    .btn-descurtir {
        text-decoration: none;
        color: #8e8e8e;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s;
    }
    
    .btn-descurtir:hover {
        color: #e74c3c;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 30px;
        background: white;
        border: 1px solid #dbdbdb;
        border-radius: 12px;
    }
    
    .empty-state p {
        font-size: 16px;
        color: #8e8e8e;
        margin-bottom: 20px;
    }
    
    .empty-state .heart {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    
    .btn-voltar {
        display: inline-block;
        background: #0095f6;
        color: white;
        padding: 10px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }
    
    .total-info {
        text-align: center;
        margin-top: 20px;
        color: #8e8e8e;
        font-size: 13px;
        padding: 10px;
    }
</style>

<div class="curtidas-container">
    <div class="header-page">
        <h2>❤️ Minhas Curtidas</h2>
        <p>Publicações que você curtiu</p>
    </div>

    @if (empty($publicacoes) || count($publicacoes) === 0)
        <div class="empty-state">
            <div class="heart">💔</div>
            <p>Você ainda não curtiu nenhuma publicação.</p>
            <a href="{{ url('/feed') }}" class="btn-voltar">Explorar publicações</a>
        </div>
    @else
        @foreach ($publicacoes as $pub)
            @php 
                // Cast preventivo obrigatório
                $pub = (array) $pub; 
                
                // Tratamento inteligente para achar o ID correto da postagem
                $idPublicacao = $pub['publicacao_id'] ?? ($pub['id'] ?? null);
                
                // Tratamento para achar o autor do post
                $idAutor = $pub['usuario_id'] ?? ($pub['autor_id'] ?? null);
            @endphp
            
            @if($idPublicacao)
                <div class="post">
                    <div class="post-header">
                        <div class="post-avatar">
                            {{ strtoupper(substr($pub['autor_nome'] ?? 'U', 0, 1)) }}
                        </div>
                        <div class="post-info">
                            @if($idAutor)
                                <a href="{{ url('/perfil/' . $idAutor) }}" class="post-nome">
                                    {{ $pub['autor_nome'] ?? 'Usuário' }}
                                </a>
                            @else
                                <span class="post-nome">{{ $pub['autor_nome'] ?? 'Usuário' }}</span>
                            @endif
                            
                            <div class="post-data">
                                Curtido em {{ date('d/m/Y \à\s H:i', strtotime($pub['data_curtida'] ?? ($pub['created_at'] ?? 'now'))) }}
                            </div>
                        </div>
                    </div>
                    
                    <p class="post-legenda">{!! nl2br(e($pub['legenda'] ?? '')) !!}</p>
                    
                    @if (!empty($pub['url_imagem']) && $pub['url_imagem'] !== 'null')
                        <img src="{{ $pub['url_imagem'] }}" class="post-imagem" alt="Publicação" onerror="this.src='{{ asset('assets/img/default.jpg') }}'">
                    @endif
                    
                    <div class="post-acoes">
                        <a href="{{ url('/publicacoes/' . $idPublicacao . '/descurtir') }}" class="btn-descurtir">
                            💔 Descurtir
                        </a>
                        @if($idAutor)
                            <a href="{{ url('/perfil/' . $idAutor) }}" class="btn-descurtir" style="color: #0095f6;">
                                👤 Ver perfil
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
        
        <div class="total-info">
            Total: {{ $totalCurtidas ?? count($publicacoes) }} publicação(ões) curtida(s)
        </div>
    @endif
</div>

@include('layouts.footer')

```
