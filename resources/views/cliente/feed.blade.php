@include('layouts.header')

<div class="container" style="max-width: 600px; margin: 0 auto; padding: 20px;">

    {{-- Exibição de Mensagens de Sucesso ou Erro vindo do Controlador --}}
    @if(session('mensagem'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-weight: 500; font-size: 14px;">
            {{ session('mensagem') }}
        </div>
    @endif

    @if(session('erro'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; font-weight: 500; font-size: 14px;">
            {{ session('erro') }}
        </div>
    @endif

    {{-- Se não houver publicações aprovadas --}}
    @if(count($publicacoes) === 0)
        <div style="background: white; border-radius: 12px; padding: 50px 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #eef0f2;">
            <span style="font-size: 50px; display: block; margin-bottom: 15px;">📸</span>
            <p style="margin: 0 0 15px 0; color: #666; font-size: 16px; font-weight: 500;">Nenhuma publicação encontrada no momento.</p>
            <a href="{{ url('/publicacoes/criar') }}" style="display: inline-block; background: #667eea; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px;">
                Seja o primeiro a publicar
            </a>
        </div>
    @else
        {{-- Listagem de Publicações do Feed --}}
        <div class="feed-lista" style="display: flex; flex-direction: column; gap: 30px;">
            @foreach ($publicacoes as $pub)
                @php 
                    // Fallback automático se a URL da imagem falhar ou vier em branco
                    $imagem = (!empty($pub->url_imagem) && $pub->url_imagem !== 'null') 
                        ? $pub->url_imagem 
                        : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600';
                @endphp

                <div class="card-post" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08); border: 1px solid #eef0f2;">
                    
                    {{-- Cabeçalho do Post (Dono do Conteúdo) --}}
                    <div class="post-header" style="display: flex; align-items: center; justify-content: space-between; padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="post-avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; color: white; font-weight: bold;">
                                {{ strtoupper(substr($pub->autor_nome ?? 'U', 0, 1)) }}
                            </div>
                            <div class="post-info" style="display: flex; flex-direction: column;">
                                <a href="{{ url('/perfil/' . $pub->usuario_id) }}" style="color: #333; font-weight: bold; text-decoration: none; font-size: 14px;">
                                    {{ $pub->autor_nome ?? 'Usuário' }}
                                </a>
                                <span style="color: #888; font-size: 11px;">
                                    @{{ $pub->autor_username ?? 'usuario' }} • {{ date('d/m/Y H:i', strtotime($pub->created_at ?? 'now')) }}
                                </span>
                            </div>
                        </div>

                        {{-- Botão de Denúncia com o Interceptador JavaScript --}}
                        <button type="button" onclick="abrirModalDenuncia({{ $pub->id }})" style="background: none; border: none; color: #e74c3c; cursor: pointer; font-size: 13px; font-weight: bold; display: flex; align-items: center; gap: 4px; padding: 5px 10px; border-radius: 6px;">
                            ⚠️ Denunciar
                        </button>
                    </div>
                    
                    {{-- Mídia do Post --}}
                    <div style="background: #fcfcfc; width: 100%; text-align: center;">
                        <img src="{{ $imagem }}" style="width: 100%; max-height: 500px; object-fit: cover; display: block; margin: 0 auto;">
                    </div>
                    
                    {{-- Ações e Legenda --}}
                    <div class="card-body" style="padding: 15px;">
                        
                        {{-- Botão de Curtir com Efeito Coração Vermelho/Vazio --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                            <a href="{{ url('/publicacoes/' . $pub->id . '/curtir') }}" style="text-decoration: none; font-size: 22px; display: inline-block;">
                                @if($pub->ja_curtiu)
                                    ❤️ <span style="font-size: 14px; color: #333; font-weight: bold; vertical-align: middle;">Curtido</span>
                                @else
                                    🤍 <span style="font-size: 14px; color: #666; vertical-align: middle;">Curtir</span>
                                @endif
                            </a>

                            <span style="color: #777; font-size: 13px; font-weight: 500;">
                                👥 {{ $pub->total_curtidas ?? 0 }} {{ ($pub->total_curtidas ?? 0) == 1 ? 'curtida' : 'curtidas' }}
                            </span>
                        </div>

                        <p style="margin: 0; font-size: 14px; color: #222; line-height: 1.5;">
                            <strong>{{ $pub->autor_username ?? 'usuario' }}</strong> {!! nl2br(e($pub->legenda ?? '')) !!}
                        </p>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- ========================================================= --}}
{{-- 📦 ESTRUTURA DO MODAL FLUTUANTE DE DENÚNCIA               --}}
{{-- ========================================================= --}}
<div id="modalDenuncia" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div style="background: white; padding: 25px; border-radius: 12px; max-width: 400px; width: 90%; box-shadow: 0 4px 15px rgba(0,0,0,0.2); position: relative; font-family: sans-serif;">
        
        <h3 style="margin-top: 0; color: #333; font-size: 18px; display: flex; align-items: center; gap: 8px;">⚠️ Denunciar Publicação</h3>
        <p style="color: #666; font-size: 13px; margin-bottom: 20px; line-height: 1.4;">Por favor, selecione o motivo real para que nossa equipe administrativa analise este conteúdo.</p>
        
        {{-- Formulário Dinâmico --}}
        <form id="formDenuncia" method="POST" action="">
            @csrf
            
            {{-- Campo Seleção de Motivo --}}
            <label style="display: block; font-weight: bold; font-size: 13px; color: #444; margin-bottom: 8px;">Motivo da Denúncia:</label>
            <select name="motivo" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; margin-bottom: 15px; background: #fafafa; cursor: pointer;">
                <option value="Conteúdo impróprio">領 Conteúdo impróprio / Nudez / Pornografia</option>
                <option value="Discurso de ódio">🤬 Discurso de ódio, Intolerância ou Bullying</option>
                <option value="Spam ou Fraude">🛡️ Spam, Links maliciosos ou Golpe</option>
                <option value="Violência ou Ameaças">⚠️ Violência explícita ou Ameaças</option>
                <option value="Propriedade intelectual">📝 Direitos autorais de terceiros</option>
            </select>

            {{-- Campo Seleção de Gravidade --}}
            <label style="display: block; font-weight: bold; font-size: 13px; color: #444; margin-bottom: 8px;">Gravidade Estimada:</label>
            <select name="gravidade" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; margin-bottom: 25px; background: #fafafa; cursor: pointer;">
                <option value="baixa">🟢 Baixa (Apenas revisão de rotina)</option>
                <option value="media" selected>🟡 Média (Incomoda a experiência na rede)</option>
                <option value="alta">🔴 Alta (Conteúdo criminoso / Bloqueio imediato)</option>
            </select>

            {{-- Botões Inferiores --}}
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="fecharModalDenuncia()" style="background: #e0e0e0; color: #333; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
                    Cancelar
                </button>
                <button type="submit" style="background: #e74c3c; color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
                    Confirmar Denúncia
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script Nativo de Controle do Modal --}}
<script>
function abrirModalDenuncia(publicacaoId) {
    const modal = document.getElementById('modalDenuncia');
    const form = document.getElementById('formDenuncia');
    
    // Injeta a rota de envio correta apontando para o ID do post clicado
    form.action = "{{ url('/publicacoes') }}/" + publicacaoId + "/denunciar";
    
    // Abre a janela flutuante usando flex para centralizar perfeitamente
    modal.style.display = 'flex';
}

function fecharModalDenuncia() {
    const modal = document.getElementById('modalDenuncia');
    modal.style.display = 'none';
}

// Fecha se o usuário clicar na área cinza de fundo
window.onclick = function(event) {
    const modal = document.getElementById('modalDenuncia');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

@include('layouts.footer')
