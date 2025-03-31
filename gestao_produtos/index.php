<?php
// index.php - VERSÃO FINAL CORRIGIDA

// Controle de sessão seguro
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/conexao.php';

// Verificação de redirecionamento otimizada
if (!empty($_SESSION['usuario_id']) && basename($_SERVER['SCRIPT_NAME']) === 'index.php') {
    header("Location: produtos/listar.php");
    exit;
}

// Processamento do formulário
$erro = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = hash('sha256', $_POST["senha"]);

    try {
        $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE email = ? AND senha_hashed = ?");
        $stmt->execute([$email, $senha]);
        
        if ($usuario = $stmt->fetch()) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['cesta'] = $_SESSION['cesta'] ?? [];
            header("Location: produtos/listar.php");
            exit();
        } else {
            $erro = "Credenciais inválidas!";
        }
    } catch (PDOException $e) {
        error_log("Erro de login: " . $e->getMessage());
        $erro = "Erro no sistema. Tente novamente mais tarde.";
    }
}

// Mensagem de logout
$mensagem_logout = '';
if (isset($_GET['logout'])) {
    $mensagem_logout = '<div class="alert alert-info alert-dismissible fade show">
        Você saiu do sistema com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gestão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { 
            max-width: 500px; 
            margin: 100px auto;
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <?= $mensagem_logout ?>
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               placeholder="digite seu email">
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required
                               placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </button>
                </form>
                <div class="mt-3 text-center">
                    <a href="cadastro.php" class="btn btn-outline-secondary">
                        <i class="bi bi-person-plus"></i> Criar conta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons (adicione se não estiver usando) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Validação do formulário no cliente -->
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                this.classList.add('was-validated');
            }
        });
    </script>
</body>
</html>