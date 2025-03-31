<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/header.php';

// Processa o formulário apenas se for POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validações
    if (empty($_POST['nome'])) {
        die("Nome do produto é obrigatório!");
    }

    if (!isset($_POST['preco']) || !is_numeric($_POST['preco'])) {
        die("Preço deve ser um número válido!");
    }

    if (empty($_POST['fornecedor_id'])) {
        die("Selecione um fornecedor!");
    }

    // Dados do formulário
    $nome = $_POST['nome'];
    $preco = (float)$_POST['preco'];
    $fornecedor_id = (int)$_POST['fornecedor_id'];

    try {
        // Prepara e executa a query
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, fornecedor_id) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $preco, $fornecedor_id]);
        
        // Redireciona com status de sucesso
        header("Location: listar.php?success=1");
        exit();
        
    } catch (PDOException $e) {
        // Redireciona com status de erro
        header("Location: listar.php?error=1&message=" . urlencode($e->getMessage()));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Cadastrar Novo Produto</h2>
        
        <form method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            
            <div class="mb-3">
                <label for="preco" class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
            </div>
            
            <div class="mb-3">
                <label for="fornecedor_id" class="form-label">Fornecedor</label>
                <select class="form-select" id="fornecedor_id" name="fornecedor_id" required>
                    <option value="">Selecione um fornecedor</option>
                    <?php
                    try {
                        $fornecedores = $pdo->query("SELECT id, nome FROM fornecedores")->fetchAll();
                        foreach ($fornecedores as $fornecedor) {
                            echo '<option value="' . $fornecedor['id'] . '">' . htmlspecialchars($fornecedor['nome']) . '</option>';
                        }
                    } catch (PDOException $e) {
                        echo '<option value="">Erro ao carregar fornecedores</option>';
                    }
                    ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Cadastrar</button>
            <a href="listar.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>