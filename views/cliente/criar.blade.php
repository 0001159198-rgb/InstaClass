<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Publicação - InstaClass</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="header">
        <h1>📷 Nova Publicação</h1>
    </div>
    <div class="container">
        <div class="card">
            
            {{-- Mensagem de Erro Gerenciada pelo Laravel --}}
            @if (session('erro'))
                <div class="erro-flash">
                    {{ session('erro') }}
                </div>
            @endif
            
            {{-- Mensagem de Sucesso Gerenciada pelo Laravel --}}
            @if (session('mensagem'))
                <div class="mensagem-flash">
                    {{ session('mensagem') }}
                </div>
            @endif
            
            <form method="POST" action="{{ url('/publicacoes/salvar') }}">
                @csrf {{-- Proteção obrigatória para envio de formulários POST --}}
                
                <div class="form-group">
                    <label>📝 Legenda</label>
                    <textarea name="legenda" placeholder="O que você está pensando? Use @ para mencionar alguém..." required>{{ old('legenda') }}</textarea>
                    <div class="dica">💡 Dica: Use @nome_usuario para mencionar alguém</div>
                </div>
                
                <div class="form-group">
                    <label>🖼️ URL da imagem (opcional)</label>
                    <input type="url" name="url_imagem" value="{{ old('url_imagem') }}" placeholder="https://exemplo.com/imagem.jpg">
                    <div class="dica">📷 Cole o link de uma imagem da internet</div>
                </div>
                
                <button type="submit">📤 Publicar</button>
            </form>
            
            <div class="back">
                <a href="{{ url('/feed') }}">← Voltar ao feed</a>
            </div>
        </div>
    </div>
</body>
</html>