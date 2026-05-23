<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InstaClass</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 2em;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 0.9em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .admin-info {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: #999;
        }

        .icon {
            text-align: center;
            font-size: 48px;
            margin-bottom: 10px;
        }

        .tipo-usuario {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .tipo-opcao {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
        }

        .tipo-opcao.selected {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }

        .tipo-opcao.selected label {
            color: white;
        }

        .tipo-opcao input {
            width: auto;
            margin: 0;
        }

        .tipo-opcao label {
            margin: 0;
            cursor: pointer;
            font-weight: normal;
        }

        .tipo-opcao:not(.selected):hover {
            background: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="icon">📷</div>
        <h1>InstaClass</h1>
        <div class="subtitle">Faça login para continuar</div>
        
        {{-- Tratamento de Erros de Login no padrão Laravel --}}
        @if (session('erro') || $errors->has('email'))
            <div class="alert alert-error">
                ❌ Email ou senha inválidos!
            </div>
        @endif
        
        {{-- Mensagem de Sucesso pós-registro no padrão Laravel --}}
        @if (session('sucesso'))
            <div class="alert alert-success">
                自由 Cadastro realizado com sucesso! Faça login.
            </div>
        @endif
        
        <form method="POST" action="{{ url('/logar') }}" id="loginForm">
            @csrf {{-- Token de proteção obrigatório do Laravel --}}
            
            <div class="tipo-usuario">
                <div class="tipo-opcao selected" data-tipo="cliente">
                    <input type="radio" name="tipo" value="cliente" id="tipoCliente" checked>
                    <label for="tipoCliente">👤 Cliente</label>
                </div>
                <div class="tipo-opcao" data-tipo="admin">
                    <input type="radio" name="tipo" value="admin" id="tipoAdmin">
                    <label for="tipoAdmin">👑 Administrador</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>📧 Email</label>
                <input type="email" name="email" id="email" placeholder="Digite seu email" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="form-group">
                <label>🔒 Senha</label>
                <input type="password" name="senha" id="senha" placeholder="Digite sua senha" required>
            </div>
            
            <button type="submit">Entrar</button>
        </form>
        
        <div class="register-link">
            <a href="{{ url('/registrar') }}">📝 Não tem conta? Cadastre-se</a>
        </div>
        
        <div class="admin-info">
            <p>🔐 Credenciais de teste:</p>
            <p id="credenciaisInfo">👤 Cliente: cliente@email.com / 123456</p>
        </div>
    </div>

    <script>
        const tipoCliente = document.getElementById('tipoCliente');
        const tipoAdmin = document.getElementById('tipoAdmin');
        const emailInput = document.getElementById('email');
        const senhaInput = document.getElementById('senha');
        const credenciaisInfo = document.getElementById('credenciaisInfo');
        
        function atualizarCredenciais() {
            if (tipoAdmin.checked) {
                emailInput.value = 'admin@instaclass.com';
                senhaInput.value = '123456';
                credenciaisInfo.innerHTML = '👑 Admin: admin@instaclass.com / 123456';
                credenciaisInfo.style.color = '#667eea';
            } else {
                emailInput.value = 'cliente@email.com';
                senhaInput.value = '123456';
                credenciaisInfo.innerHTML = '👤 Cliente: cliente@email.com / 123456';
                credenciaisInfo.style.color = '#999';
            }
        }
        
        function updateSelectedStyle() {
            document.querySelectorAll('.tipo-opcao').forEach(opt => {
                opt.classList.remove('selected');
            });
            if (tipoAdmin.checked) {
                document.querySelector('.tipo-opcao[data-tipo="admin"]').classList.add('selected');
            } else {
                document.querySelector('.tipo-opcao[data-tipo="cliente"]').classList.add('selected');
            }
        }
        
        tipoCliente.addEventListener('change', function() {
            atualizarCredenciais();
            updateSelectedStyle();
        });
        
        tipoAdmin.addEventListener('change', function() {
            atualizarCredenciais();
            updateSelectedStyle();
        });
        
        // Inicializar com os valores corretos
        atualizarCredenciais();
        updateSelectedStyle();
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (!emailInput.value || !senhaInput.value) {
                e.preventDefault();
                alert('Preencha email e senha!');
            }
        });
    </script>
</body>
</html>