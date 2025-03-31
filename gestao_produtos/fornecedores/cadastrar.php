<?php
require_once __DIR__ . '/../includes/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj']); // Remove formatação

    // Validações
    if (empty($nome)) {
        $erro = "Nome do fornecedor é obrigatório!";
    } elseif (strlen($cnpj) != 14 && !empty($cnpj)) {
        $erro = "CNPJ deve ter 14 dígitos!";
    } else {
        try {
            // Verifica se CNPJ já existe (apenas se foi informado)
            if (!empty($cnpj)) {
                $stmt = $pdo->prepare("SELECT id FROM fornecedores WHERE cnpj = ?");
                $stmt->execute([$cnpj]);
                if ($stmt->fetch()) {
                    $erro = "CNPJ já cadastrado!";
                }
            }

            if (empty($erro)) {
                $stmt = $pdo->prepare("INSERT INTO fornecedores (nome, cnpj) VALUES (?, ?)");
                $stmt->execute([$nome, !empty($cnpj) ? $cnpj : null]);
                
                $_SESSION['msg'] = [
                    'tipo' => 'success',
                    'texto' => 'Fornecedor cadastrado com sucesso!'
                ];
                header("Location: listar.php");
                exit;
            }
        } catch (PDOException $e) {
            $erro = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Fornecedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .cnpj-mask {
            font-family: monospace;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Cadastrar Fornecedor</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Fornecedor *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required
                               value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="cnpj" class="form-label">CNPJ (opcional)</label>
                        <input type="text" class="form-control cnpj-mask" id="cnpj" name="cnpj"
                               placeholder="00.000.000/0000-00"
                               value="<?= isset($_POST['cnpj']) ? htmlspecialchars($_POST['cnpj']) : '' ?>">
                        <small class="text-muted">Formato: 00.000.000/0000-00</small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                        <a href="listar.php" class="btn btn-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Máscara para CNPJ -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#cnpj').on('input', function() {
            // Remove tudo que não é dígito
            let valor = $(this).val().replace(/\D/g, '');
            
            // Aplica a máscara
            if (valor.length > 2) valor = valor.replace(/^(\d{2})(\d)/, '$1.$2');
            if (valor.length > 6) valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            if (valor.length > 10) valor = valor.replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3/$4');
            if (valor.length > 15) valor = valor.replace(/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d)/, '$1.$2.$3/$4-$5');
            
            $(this).val(valor.substring(0, 18)); // Limita ao tamanho máximo
        });
    });
    </script>
</body>
</html>