<?php
$baseUrl = 'BASE_URL';
?>
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

        /* Estilo para a opção de admin */
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
        
        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-error">
                <?php if ($_GET['erro'] == 1): ?>
                    ❌ Preencha todos os campos!
                <?php elseif ($_GET['erro'] == 2): ?>
                    ❌ Email já cadastrado!
                <?php elseif ($_GET['erro'] == 3): ?>
                    ❌ Erro ao cadastrar. Tente novamente!
                <?php elseif ($_GET['erro'] == 4): ?>
                    ❌ Nome de usuário já existe!
                <?php elseif ($_GET['erro'] == 5): ?>
                    ❌ Código de admin inválido!
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="<?= $baseUrl ?>/cadastrar">
            <div class="form-group">
                <label>👤 Nome completo</label>
                <input type="text" name="nome" placeholder="Digite seu nome completo" required autofocus>
            </div>
            
            <div class="form-group">
                <label>🏷️ Nome de usuário</label>
                <input type="text" name="nome_usuario" placeholder="Escolha um nome de usuário" required>
            </div>
            
            <div class="form-group">
                <label>📧 Email</label>
                <input type="email" name="email" placeholder="Digite seu email" required>
            </div>
            
            <div class="form-group">
                <label>🔒 Senha</label>
                <input type="password" name="senha" placeholder="Crie uma senha" required>
                <div class="password-requirements">Mínimo de 6 caracteres</div>
            </div>
            
            <!-- Opção para cadastrar como Admin -->
            <div class="admin-option" id="adminOption">
                <label>
                    <input type="checkbox" name="cadastrar_como_admin" value="1" id="adminCheckbox">
                    <span>👑 Cadastrar como Administrador</span>
                </label>
                <div class="admin-info" id="adminInfo">
                    ⚠️ É necessário um código de segurança para criar uma conta de administrador.
                </div>
            </div>
            
            <!-- Campo para código de admin (inicialmente escondido) -->
            <div class="form-group" id="codigoAdminGroup" style="display: none;">
                <label>🔐 Código de Administrador</label>
                <input type="password" name="codigo_admin" id="codigoAdmin" placeholder="Digite o código de segurança">
                <div class="password-requirements">Código: ADMIN123</div>
            </div>
            
            <button type="submit">Criar conta</button>
        </form>
        
        <div class="login-link">
            <a href="<?= $baseUrl ?>/login">🔑 Já tem conta? Faça login</a>
        </div>
    </div>

    <script>
        // Mostrar/esconder campo de código admin
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
            }
        });
    </script>
</body>
</html>