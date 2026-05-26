@include('layouts.header')

<div class="container" style="max-width: 600px; margin: 0 auto; padding: 20px;">

    {{-- Mensagens de Feedback --}}
    @if(session('mensagem'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-weight: 500; font-size: 14px;">
            {{ session('mensagem') }}
        </div>
    @endif

    {{-- Cabeçalho do Perfil --}}
    <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #eef0f2; text-align: center;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; color: white; font-weight: bold; margin: 0 auto 15px auto;">
            {{ strtoupper(substr($usuario->nome ?? 'U', 0, 1)) }}
        </div>
        
        <h2 style="margin: 0; color: #333; font-size: 22px;">{{ $usuario->nome ?? 'Usuário' }}</h2>
        
        {{-- 🔥 CORREÇÃO INTEGRAL DA TAG QUEBRADA AQUI --}}
        <p style="margin: 5px 0 15px 0; color: #888; font-size: 14px;">
            <span>@</span>{{ $usuario->nome_usuario ?? 'usuario' }}
        </p>
        
        <div style="display: flex; justify-content: center; gap: 20px; border-top: 1px solid #f1f2f4; padding-top: 15px;">
            <span style="font-size: 14px; color: #555;">📸 <strong>{{ count($publicacoes) }}</strong> {{ count($publicacoes) == 1 ? 'publicação' : 'publicações' }}</span>
        </div>
    </div>

    <h3 style="color: #444; font-size: 16px; margin-bottom: 15px; display: flex; align-items: center; gap: 6px;">📸 Publicações</h3>

    {{-- Se o usuário não tiver posts --}}
    @if (count($publicacoes) === 0)
        <div style="background: white; border-radius: 12px; padding: 40px 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #eef0f2;">
            <p style="margin: 0 0 15px 0; color: #777; font-size: 14px;">Este usuário ainda não fez nenhuma publicação.</p>
            @if(auth()->id() == ($usuario->id ?? 0))
                <a href="{{ url('/publicacoes/criar') }}" style="display: inline-block; background: #667eea; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px;">
                    Criar Nova Publicação
                </a>
            @endif
        </div>
    @else
        {{-- Grade/Lista de Publicações --}}
        <div class="feed-lista" style="display: flex; flex-direction: column; gap: 25px;">
            @foreach ($publicacoes as $pub)
                @php 
                    // Fallback se a imagem falhar ou vier nula
                    $imagem = (!empty($pub->url_imagem) && $pub->url_imagem !== 'null') 
                        ? $pub->url_imagem 
                        : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600';
                    
                    // Define a cor da etiqueta de status
                    $statusCor = ($pub->status ?? 'pendente') === 'aprovada' ? '#2ecc71' : '#f39c12';
                @endphp

                <div class="card-post" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.06); border: 1px solid #eef0f2;">
                    
                    {{-- Imagem do Post --}}
                    <div style="background: #fdfdfd; width: 100%; text-align: center;">
                        <img src="{{ $imagem }}" style="width: 100%; max-height: 450px; object-fit: cover; display: block; margin: 0 auto;">
                    </div>
                    
                    {{-- Conteúdo e Legenda --}}
                    <div class="card-body" style="padding: 15px;">
                        
                        <p style="margin: 0 0 12px 0; font-size: 14px; color: #222; line-height: 1.5;">
                            <strong>{{ $usuario->nome_usuario ?? 'usuario' }}</strong> 
                            @if(!empty($pub->legenda))
                                {!! nl2br(e($pub->legenda)) !!}
                            @else
                                <span style="color: #bbb; font-style: italic;">Sem legenda</span>
                            @endif
                        </p>
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f8f9fa; padding-top: 10px; margin-top: 10px;">
                            <span style="font-size: 12px; font-weight: bold; color: white; background: {{ $statusCor }}; padding: 3px 8px; border-radius: 4px; text-transform: uppercase;">
                                Status: {{ $pub->status ?? 'pendente' }}
                            </span>

                            <span style="color: #777; font-size: 13px; font-weight: 500;">
                                ❤️ {{ $pub->total_curtidas ?? 0 }} {{ ($pub->total_curtidas ?? 0) == 1 ? 'curtida' : 'curtidas' }}
                            </span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</div>

@include('layouts.footer')
