@include('layouts.header')

<div class="container" style="max-width: 600px; margin: 0 auto; padding: 20px;">
    
    {{-- Botão para Criar Nova Publicação --}}
    <div style="margin-bottom: 25px; text-align: right;">
        <a href="{{ url('/publicacoes/criar') }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            ➕ Nova Publicação
        </a>
    </div>

    {{-- Sistema de Alertas (Mensagens de Sucesso ou Erro) --}}
    @if(session('mensagem'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            {{ session('mensagem') }}
        </div>
    @endif

    @if(session('erro'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            {{ session('erro') }}
        </div>
    @endif

    {{-- Renderização da Listagem do Feed --}}
    @if (!isset($publicacoes) || count($publicacoes) === 0)
        <div style="background: white; border-radius: 12px; padding: 40px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <span style="font-size: 48px; display: block; margin-bottom: 10px;">📭</span>
            <p style="margin: 0; color: #666; font-size: 16px;">Nenhuma publicação encontrada no feed.</p>
        </div>
    @else
        <div class="feed-lista" style="display: flex; flex-direction: column; gap: 30px;">
            @foreach ($publicacoes as $pub)
                @php 
                    // Força a conversão para array para evitar erros de leitura de chaves no PostgreSQL/Eloquent
                    $pubArray = (array) $pub;
                    
                    // Identifica o ID do usuário através de múltiplos fallbacks para matar o Erro 500
                    $linkId = $pubArray['usuario_id'] ?? $pubArray['id_usuario'] ?? $pubArray['user_id'] ?? null;
                    
                    // Validação do link da imagem
                    $imagem = (!empty($pubArray['url_imagem']) && $pubArray['url_imagem'] !== 'null' && $pubArray['url_imagem'] !== 'undefined') 
                        ? $pubArray['url_imagem'] 
                        : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600';
                @endphp

                <div class="card-post" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08); border: 1px solid #eef0f2;">
                    
                    {{-- Cabeçalho do Post (Avatar e Nome do Autor) --}}
                    <div class="post-header" style="display: flex; align-items: center; gap: 12px; padding: 15px;">
                        <div class="post-avatar" style="width: 42px; height: 42px; background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; color: white; font-weight: bold; flex-shrink: 0;">
                            {{ strtoupper(substr($pubArray['autor_nome'] ?? $pubArray['nome'] ?? 'U', 0, 1)) }}
                        </div>
                        <div class="post-info" style="display: flex; flex-direction: column;">
                            @if($linkId)
                                <a href="{{ url('/perfil/' . $linkId) }}" class="post-nome" style="color: #333; font-weight: bold; text-decoration: none; font-size: 15px;">
                                    {{ $pubArray['autor_nome'] ?? $pubArray['nome'] ?? 'Usuário' }}
                                </a>
                            @else
                                <span class="post-nome" style="color: #333; font-weight: bold; font-size: 15px;">
                                    {{ $pubArray['autor_nome'] ?? $pubArray['nome'] ?? 'Usuário' }}
                                </span>
                            @endif
                            <div class="post-data" style="color: #888; font-size: 12px; margin-top: 2px;">
                                {{ date('d/m/Y \à\s H:i', strtotime($pubArray['criado_em'] ?? $pubArray['created_at'] ?? 'now')) }}
                            </div>
                        </div>
                    </div>
                    
                    {{-- Imagem da Publicação --}}
                    <img src="{{ $imagem }}" 
                         alt="Imagem do post" 
                         style="width: 100%; max-height: 450px; object-fit: cover; display: block;"
                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600';">
                    
                    {{-- Corpo da Publicação (Legenda e Ações) --}}
                    <div class="card-body" style="padding: 15px;">
                        
                        {{-- Legenda com suporte a quebras de linha limpas --}}
                        <p class="post-legenda" style="margin: 0 0 15px 0; font-size: 15px; color: #222; line-height: 1.5; word-wrap: break-word;">
                            {!! nl2br(e($pubArray['legenda'] ?? '')) !!}
                        </p>
                        
                        <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 12px;">

                        {{-- Barra de Interação (Curtidas e Denúncias) --}}
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            
                            {{-- Seção de Curtidas --}}
                            <div style="display: flex; align-items: center; gap: 15px;">
                                @if (isset($pubArray['ja_curtiu']) && $pubArray['ja_curtiu'])
                                    <a href="{{ url('/publicacoes/' . ($pubArray['id'] ?? 0) . '/descurtir') }}" style="text-decoration: none; color: #e74c3c; font-weight: bold; font-size: 14px; display: flex; align-items: center; gap: 5px;">
                                        ❤️ Descurtir
                                    </a>
                                @else
                                    <a href="{{ url('/publicacoes/' . ($pubArray['id'] ?? 0) . '/curtir') }}" style="text-decoration: none; color: #666; font-weight: 500; font-size: 14px; display: flex; align-items: center; gap: 5px;">
                                        🤍 Curtir
                                    </a>
                                @endif
                                
                                <span style="color: #666; font-size: 13px; font-weight: 500;">
                                    {{ $pubArray['total_curtidas'] ?? 0 }} curtidas
                                </span>
                            </div>

                            {{-- Botão de Denúncia --}}
                            <form action="{{ url('/publicacoes/' . ($pubArray['id'] ?? 0) . '/denunciar') }}" method="POST" style="margin: 0;" onsubmit="return confirm('Deseja realmente denunciar esta publicação por conteúdo inadequado?');">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: #95a5a6; font-size: 13px; cursor: pointer; font-weight: 500; padding: 0;">
                                    ⚠️ Denunciar
                                </button>
                            </form>

                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@include('layouts.footer')
