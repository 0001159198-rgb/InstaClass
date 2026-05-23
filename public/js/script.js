// Aguarda o DOM carregar completamente
document.addEventListener('DOMContentLoaded', function() {
    
    console.log('JavaScript carregado com sucesso!');
    
    // ================== DENÚNCIA ==================
    const denunciaForms = document.querySelectorAll('form[action*="denunciar"]');
    console.log('Formulários de denúncia encontrados:', denunciaForms.length);
    
    denunciaForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            console.log('Formulário de denúncia submetido');
            if (!confirm('⚠️ Tem certeza que deseja denunciar esta publicação?')) {
                e.preventDefault();
                console.log('Denúncia cancelada');
            } else {
                console.log('Denúncia confirmada');
            }
        });
    });
    
    // ================== CURTIR (SEM PISCAR / AJAX REAL) ==================
    const curtirLinks = document.querySelectorAll('.btn-curtir');
    console.log('Links de curtir encontrados:', curtirLinks.length);
    
    curtirLinks.forEach(link => {
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            const url = this.href;
            const btn = this;
            
            console.log('Clicou em curtir:', url);
            
            try {
                const response = await fetch(url, { method: 'GET' }); // Ou 'POST' dependendo do seu backend
                if (response.ok) {
                    console.log('Curtida processada com sucesso!');
                    
                    // SOLUÇÃO ANTI-PISCAR: Atualiza visualmente a interface via JS sem dar F5 na página
                    if (btn.innerText.includes('Curtido')) {
                        btn.innerHTML = '❤️ Curtir'; // Ajuste o texto ou ícone conforme o seu design original
                        btn.style.color = '#262626';
                    } else {
                        btn.innerHTML = '❤️ Curtido';
                        btn.style.color = '#e74c3c';
                    }
                    
                } else {
                    console.error('Erro na resposta:', response.status);
                }
            } catch (error) {
                console.error('Erro ao curtir:', error);
            }
        });
    });
    
    // ================== MENSAGENS TEMPORÁRIAS ==================
    const mensagensFlash = document.querySelectorAll('.mensagem-flash, .erro-flash');
    if (mensagensFlash.length) {
        setTimeout(() => {
            mensagensFlash.forEach(msg => {
                msg.style.opacity = '0';
                setTimeout(() => {
                    if (msg && msg.parentNode) msg.parentNode.removeChild(msg);
                }, 500);
            });
        }, 5000);
    }
    
    // ================== BUSCA ==================
    function configurarBusca(seletor) {
        const input = document.querySelector(seletor);
        if (input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const termo = this.value.trim();
                    if (termo !== '') {
                        window.location.href = '/RedeSocial/buscar?q=' + encodeURIComponent(termo);
                    }
                }
            });
        }
    }
    
    configurarBusca('.search-box input');
    configurarBusca('.search-input');
    
    // ================== NOVA PUBLICAÇÃO (AUTO-RESIZE PROTEGIDO) ==================
    // Seleciona o textarea específico da nova publicação para evitar conflitos
    const textareasPublicacao = document.querySelectorAll('.card textarea, form textarea');
    
    textareasPublicacao.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
    
    // Confirmar antes de sair sem salvar (opcional)
    const formPublicacao = document.querySelector('.card form');
    let formModified = false;
    
    if (formPublicacao) {
        const inputs = formPublicacao.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                formModified = true;
            });
        });
    }
    
    // ================== MODAL DE DENÚNCIA ==================
    const modal = document.getElementById('modalDenuncia');
    
    if (modal) {
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'block') {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
    
    console.log('Todos os eventos foram registrados com sucesso!');
});