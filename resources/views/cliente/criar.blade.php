@include('layouts.header')

<div class="container" style="max-width: 600px; margin: 40px auto; padding: 20px;">
    
    <div class="card-criar" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        
        <h2 style="margin-top: 0; margin-bottom: 25px; color: #333; display: flex; align-items: center; gap: 10px;">
            📷 Nova Publicação
        </h2>

        {{-- Exibição de Mensagens de Erro --}}
        @if(session('erro'))
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                {{ session('erro') }}
            </div>
        @endif

        {{-- Adicionado o ID "formPublicar" para interceptar o envio com JavaScript --}}
        <form id="formPublicar" action="{{ url('/publicacoes/salvar') }}" method="POST">
            @csrf

            {{-- Campo da Legenda --}}
            <div style="margin-bottom: 20px;">
                <label for="legenda" style="display: block; font-weight: bold; margin-bottom: 8px; color: #555;">
                    📝 Legenda
                </label>
                <textarea id="legenda" name="legenda" rows="4" placeholder="O que você está pensando? Use @ para mencionar alguém..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 15px; resize: vertical; box-sizing: border-box;" required></textarea>
                <small style="color: #888; display: block; margin-top: 4px;">Dica: Use @nome_usuario para mencionar alguém</small>
            </div>

            {{-- Campo da Imagem --}}
            <div style="margin-bottom: 25px;">
                <label for="url_imagem" style="display: block; font-weight: bold; margin-bottom: 8px; color: #555;">
                    🖼️ URL da Imagem (opcional)
                </label>
                <input type="url" id="url_imagem" name="url_imagem" placeholder="https://exemplo.com/sua-imagem.jpg" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; box-sizing: border-box;">
                <small style="color: #888; display: block; margin-top: 4px;">Se deixar em branco, geraremos uma imagem linda para você!</small>
            </div>

            {{-- Botão de Enviar --}}
            <button type="submit" style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: opacity 0.2s;">
                📥 Publicar
            </button>
        </form>

        {{-- Link de Voltar --}}
        <div style="margin-top: 20px; text-align: center;">
            <a href="{{ url('/feed') }}" style="color: #667eea; text-decoration: none; font-size: 14px; font-weight: 500;">
                ← Voltar ao feed
            </a>
        </div>

    </div>
</div>

{{-- Script para injetar imagem automática caso o campo fique em branco --}}
<script>
document.getElementById('formPublicar').addEventListener('submit', function(e) {
    const inputImagem = document.getElementById('url_imagem');
    
    // Se o usuário não preencher a URL da imagem, injetamos uma do Picsum dinamicamente
    if (!inputImagem.value.trim()) {
        const idAleatorio = Math.floor(Math.random() * 1000);
        inputImagem.value = 'https://picsum.photos/600/500?random=' + idAleatorio;
    }
});
</script>

@include('layouts.footer')
