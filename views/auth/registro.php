<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - InstaClass</title>
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
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
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

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }

        input:focus, select:focus {
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

        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .icon {
            text-align: center;
            font-size: 48px;
            margin-bottom: 10px;
        }

        .password-requirements {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        .admin-option {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .admin-option label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            margin: 0;
            color: #555;
        }

        .admin-option input {
            width: auto;
            margin: 0;
        }

        .admin-info {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
            margin-left: 25px;
        }

        .admin-option.warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
        }

        .admin-option.warning label {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="icon">📷</div>
        <h1>Criar Conta</h1>
        <div class="subtitle">Cadastre-se para começar</div>
        
        {{-- Tratamento dinâmico de erros mapeado para o Laravel --}}
        @if (session('erro') || $errors->any())
            <div class="alert alert-error">
                @if (session('erro') == 1 || $errors->has('nome') || $errors->has('senha'))
                    ❌ Preencha todos os campos corretamente!
                @elseif (session('erro') == 2 || $errors->has('email'))
                    ❌ Email já cadastrado!
                @elseif (session('erro') == 4 || $errors->has('nome_usuario'))
                    ❌ Nome de usuário já existe!
                @elseif (session('erro') == 5 || session('erro_codigo_admin'))
                    ❌ Código de admin inválido!
                @else
                    ❌ Erro ao cadastrar. Tente novamente!
                @endif
            </div>
        @endif
        
        <form method="POST" action="{{ url('/cadastrar') }}">
            @csrf
            
            <div class="form-group">
                <label>👤 Nome completo</label>
                <input type="text" name="nome" placeholder="Digite seu nome completo" value="{{ old('nome') }}" required autofocus>
            </div>
            
            <div class="form-group">
                <label>🏷️ Nome de usuário</label>
                <input type="text" name="nome_usuario" placeholder="Escolha um nome de usuário" value="{{ old('nome_usuario') }}" required>
            </div>
            
            <div class="form-group">
                <label>📧 Email</label>
                <input type="email" name="email" placeholder="Digite seu email" value="{{ old('email') }}" required>
            </div>
            
            <div class="form-group">
                <label>🔒 Senha</label>
                <input type="password" name="senha" placeholder="Crie uma senha" required>
                <div class="password-requirements">Mínimo de 6 caracteres</div>
            </div>
            
            <div class="admin-option {{ old('cadastrar_como_admin') ? 'warning' : '' }}" id="adminOption">
                <label>
                    <input type="checkbox" name="cadastrar_como_admin" value="1" id="adminCheckbox" {{ old('cadastrar_como_admin') ? 'checked' : '' }}>
                    <span>👑 Cadastrar como Administrador</span>
                </label>
                <div class="admin-info" id="adminInfo">
                    ⚠️ É necessário um código de segurança para criar uma conta de administrador.
                </div>
            </div>
            
            <div class="form-group" id="codigoAdminGroup" style="display: {{ old('cadastrar_como_admin') ? 'block' : 'none' }};">
                <label>🔐 Código de Administrador</label>
                <input type="password" name="codigo_admin" id="codigoAdmin" placeholder="Digite o código de segurança">
                <div class="password-requirements">Código: ADMIN123</div>
            </div>
            
            <button type="submit">Criar conta</button>
        </form>
        
        <div class="login-link">
            <a href="{{ url('/login') }}">🔑 Já tem conta? Faça login</a>
        </div>
    </div>

    <script>
        const adminCheckbox = document.getElementById('adminCheckbox');
        const codigoAdminGroup = document.getElementById('codigoAdminGroup');
        const adminOption = document.getElementById('adminOption');
        
        adminCheckbox.addEventListener('change', function() {
            if (this.checked) {
                codigoAdminGroup.style.display = 'block';
                adminOption.classList.add('warning');
            } else {
                codigoAdminGroup.style.display = 'none';
                adminOption.classList.remove('warning');
                document.getElementById('codigoAdmin').value = '';
            }
        });
    </script>
</body>
</html>