@include('layouts.header')

<meta name="csrf-token" content="{{ csrf_token() }}">

@if (session('mensagem'))
    <div class="mensagem-flash">
        {{ session('mensagem') }}
    </div>
@endif

@if (session('erro'))
    <div class="erro-flash">
        {{ session('erro') }}
    </div>
@endif

<div id="modalDenuncia" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🚨 Denunciar Publicação</h3>
            <span class="close-modal">&times;</span>
        </div>
        <form id="formDenuncia" method="POST">
            @csrf {{-- Proteção obrigatória contra ataques CSRF --}}
            <input type="hidden" name="publicacao_id" id="publicacao_id">
            <p>Selecione o motivo da denúncia:</p>
            <div class="motivo-option" data-motivo="Conteúdo impróprio">📝 Conteúdo impróprio</div>
            <div class="motivo-option" data-motivo="Discurso de ódio">😡 Discurso de ódio</div>
            <div class="motivo-option" data-motivo="Spam ou enganoso">📢 Spam ou enganoso</div>
            <div class="motivo-option" data-motivo="Violência ou conteúdo perigoso">⚠️ Violência ou conteúdo perigoso</div>
            <div class="motivo-option" data-motivo="Assédio ou bullying">💔 Assédio ou bullying</div>
            <div class="motivo-option" data-motivo="Conteúdo sexual">🔞 Conteúdo sexual</div>
            <div class="motivo-option" data-motivo="Outro">📌 Outro (especifique)</div>
            <input type="text" id="motivo_outro" class="motivo-outro" placeholder="Digite o motivo da denúncia...">
            <input type="hidden" name="motivo" id="motivo_selecionado">
            <input type="hidden" name="gravidade" value="media">
            <button type="submit" class="btn-enviar">Enviar Denúncia</button>
        </form>
    </div>
</div>

<div class="app-shell">
    <main class="main-content">
        <div class="feed-container">
            <header class="topbar-feed">
                <h2>🏠 Feed</h2>
            </header>
            <section class="feed">
                @if (empty($publicacoes) || count($publicacoes) === 0)
                    <div class="empty-state">
                        <p>📭 Nenhuma publicação encontrada.</p>
                        <a href="{{ url('/publicacoes/criar') }}" class="btn-criar">➕ Criar primeira publicação</a>
                    </div>
                @else
                    @foreach ($publicacoes as $pub)
                        @php 
                            // Cast preventivo para garantir compatibilidade com arrays ou objetos do banco
                            $pub = (array) $pub; 
                        @endphp
                        <div class="post">
                            <div class="post-header">
                                <div class="post-avatar">
                                    {{ strtoupper(substr($pub['autor_nome'] ?? 'U', 0, 1)) }}
                                </div>
                                <div class="post-info">
                                    <a href="{{ url('/perfil/' . $pub['usuario_id']) }}" class="post-nome">
                                        {{ $pub['autor_nome'] ?? 'Usuário' }}
                                    </a>
                                    <div class="post-data">
                                        {{ date('d/m/Y \à\s H:i', strtotime($pub['criado_em'] ?? 'now')) }}
                                    </div>
                                </div>
                            </div>
                            
                            <p class="post-legenda">{!! nl2br(e($pub['legenda'])) !!}</p>
                            
                            @if (!empty($pub['url_imagem']))
                                <img src="{{ $pub['url_imagem'] }}" class="post-imagem" onerror="this.src='{{ asset('assets/img/default.jpg') }}'">
                            @endif
                            
                            <div class="post-acoes">
                                <a href="{{ url('/publicacoes/' . $pub['id'] . '/curtir') }}" class="btn-curtir">
                                    ❤️ Curtir (<span>{{ $pub['total_curtidas'] ?? 0 }}</span>)
                                </a>
                                <button type="button" class="btn-denunciar" onclick="abrirModal({{ $pub['id'] }})">
                                    🚨 Denunciar
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </section>
        </div>
    </main>
</div>

<script>
// ================== MODAL DE DENÚNCIA ==================
var modal = document.getElementById('modalDenuncia');
var publicacaoId = null;

function abrirModal(id) {
    publicacaoId = id;
    document.getElementById('publicacao_id').value = id;
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Resetar seleção
    document.querySelectorAll('.motivo-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    document.getElementById('motivo_outro').classList.remove('show');
    document.getElementById('motivo_outro').value = '';
    document.getElementById('motivo_selecionado').value = '';
}

function fecharModal() {
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Fechar modal ao clicar no X ou fora
document.querySelector('.close-modal')?.addEventListener('click', fecharModal);
window.addEventListener('click', function(e) {
    if (e.target == modal) fecharModal();
});

// Selecionar motivo
document.querySelectorAll('.motivo-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.motivo-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        this.classList.add('selected');
        
        var motivo = this.getAttribute('data-motivo');
        document.getElementById('motivo_selecionado').value = motivo;
        
        if (motivo === 'Outro') {
            document.getElementById('motivo_outro').classList.add('show');
        } else {
            document.getElementById('motivo_outro').classList.remove('show');
        }
    });
});

// Enviar denúncia
document.getElementById('formDenuncia').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var motivo = document.getElementById('motivo_selecionado').value;
    var motivoOutro = document.getElementById('motivo_outro').value;
    
    if (!motivo) {
        alert('⚠️ Por favor, selecione um motivo para a denúncia.');
        return;
    }
    
    if (motivo === 'Outro' && motivoOutro.trim() === '') {
        alert('⚠️ Por favor, digite o motivo da denúncia.');
        return;
    }
    
    if (motivo === 'Outro') {
        motivo = motivoOutro;
    }
    
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ url('/publicacoes') }}/" + publicacaoId + "/denunciar";
    
    // Adiciona o Token CSRF do Laravel para o envio de formulário dinâmico ser aceito pelo servidor
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var inputToken = document.createElement('input');
    inputToken.type = 'hidden';
    inputToken.name = '_token';
    inputToken.value = token;
    
    var inputMotivo = document.createElement('input');
    inputMotivo.type = 'hidden';
    inputMotivo.name = 'motivo';
    inputMotivo.value = motivo;
    
    var inputGravidade = document.createElement('input');
    inputGravidade.type = 'hidden';
    inputGravidade.name = 'gravidade';
    inputGravidade.value = 'media';
    
    form.appendChild(inputToken);
    form.appendChild(inputMotivo);
    form.appendChild(inputGravidade);
    document.body.appendChild(form);
    
    form.submit();
});

// ================== CURTIR - ASSÍNCRONO COM TOKEN DE PROTEÇÃO ==================
document.querySelectorAll('.btn-curtir').forEach(link => {
    link.addEventListener('click', async function(e) {
        e.preventDefault();
        const url = this.href;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (response.ok) {
                window.location.reload();
            } else {
                console.error('Erro na resposta:', response.status);
            }
        } catch (error) {
            console.error('Erro ao curtir:', error);
        }
    });
});

// ================== FECHAR MENSAGENS FLASH ==================
setTimeout(function() {
    var mensagens = document.querySelectorAll('.mensagem-flash, .erro-flash');
    mensagens.forEach(function(msg) {
        msg.style.opacity = '0';
        setTimeout(function() { if(msg) msg.remove(); }, 500);
    });
}, 5000);
</script>

@include('layouts.footer')