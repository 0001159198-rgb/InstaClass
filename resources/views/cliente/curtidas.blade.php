@include('layouts.header')

<div class="container" style="max-width: 600px; margin: 0 auto; padding: 20px;">
    
    {{-- Topo Informativo --}}
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #eef0f2; text-align: center;">
        <h2 style="margin: 0; color: #333; font-size: 20px;">❤️ Minhas Curtidas</h2>
        <p style="margin: 5px 0 0 0; color: #888; font-size: 14px;">Você curtiu um total de <strong>{{ $totalCurtidas }}</strong> publicações</p>
    </div>

    {{-- Se o usuário não tiver curtido nada ainda --}}
    @if (count($publicacoes) === 0)
        <div style="background: white; border-radius: 12px; padding: 50px 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <span style="font-size: 50px; display: block; margin-bottom: 15px;">🤍</span>
            <p style="margin: 0 0 15px 0; color: #666; font-size: 16px; font-weight: 500;">Você ainda não curtiu nenhuma publicação.</p>
            <a href="{{ url('/feed') }}" style="display: inline-block; background: #667eea; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px;">
                Explorar o Feed
            </a>
        </div>
    @else
        {{-- Listagem das Publicações Curtidas --}}
        <div class="feed-lista" style="display: flex; flex-direction: column; gap: 30px;">
            @foreach ($publicacoes as $pub)
                @php 
                    // Fallback para caso a URL da imagem venha vazia ou nula
                    $imagem = (!empty($pub->url_imagem) && $pub->url_imagem !== 'null') 
                        ? $pub->url_imagem 
                        : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600';
                    
                    // Garante o ID correto do autor do post
                    $autorId = $pub->usuario_id ?? 0;
                @endphp

                <div class="card-post" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08); border: 1px solid #eef0f2;">
                    
                    {{-- Header do Post (Dados do Autor) --}}
                    <div class="post-header" style="display: flex; align-items: center; gap: 12px; padding: 15px;">
                        <div class="post-avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; color: white; font-weight: bold;">
                            {{ strtoupper(substr($pub->autor_nome ?? 'U', 0, 1)) }}
                        </div>
                        <div class="post-info" style="display: flex; flex-direction: column;">
                            <a href="{{ url('/perfil/' . $autorId) }}" style="color: #333; font-weight: bold; text-decoration: none; font-size: 14px;">
                                {{ $pub->autor_nome ?? 'Usuário' }}
                            </a>
                            <span style="color: #888; font-size: 11px;">
                                @{{ $pub->autor_username ?? 'usuario' }} • {{ date('d/m/Y', strtotime($pub->created_at ?? 'now')) }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Imagem da Publicação --}}
                    <div style="background: #fcfcfc; width: 100%; text-align: center;">
                        <img src="{{ $imagem }}" style="width: 100%; max-height: 450px; object-fit: cover; display: block; margin: 0 auto;">
                    </div>
                    
                    {{-- Corpo e Legenda --}}
                    <div class="card-body" style="padding: 15px;">
                        <p style="margin: 0 0 15px 0; font-size: 14px; color: #222; line-height: 1.5;">
                            <strong>{{ $pub->autor_username ?? 'usuario' }}</strong> {!! nl2br(e($pub->legenda ?? '')) !!}
                        </p>
                        
                        <hr style="border: 0; border-top: 1px solid #f1f2f4; margin-bottom: 12px;">

                        {{-- Área de Ações --}}
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            {{-- Descurtir (Chama a rota passando o ID correto da publicação) --}}
                            <a href="{{ url('/publicacoes/' . $pub->id . '/descurtir') }}" style="text-decoration: none; color: #e74c3c; font-weight: bold; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                                ❤️ Descurtir desta lista
                            </a>
                            
                            <span style="color: #777; font-size: 13px; font-weight: 500;">
                                👥 {{ $pub->total_curtidas ?? 1 }} {{ ($pub->total_curtidas ?? 1) == 1 ? 'curtida' : 'curtidas' }}
                            </span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</div>

@include('layouts.footer')
