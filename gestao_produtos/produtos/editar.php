<?php
require_once __DIR__ . '/../includes/conexao.php';
session_start();

// Limpa mensagens anteriores
unset($_SESSION['msg']);

// Verificação de ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = ['tipo' => 'danger', 'texto' => 'ID inválido'];
    header("Location: listar.php");
    exit();
}

$id = (int)$_GET['id'];

// Busca produto
try {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();

    if (!$produto) {
        $_SESSION['msg'] = ['tipo' => 'danger', 'texto' => 'Produto não encontrado'];
        header("Location: listar.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro no sistema: " . $e->getMessage());
}

// Processa formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $pdo->prepare("UPDATE produtos SET nome=?, preco=?, fornecedor_id=? WHERE id=?");
        $stmt->execute([
            $_POST['nome'],
            (float)$_POST['preco'],
            (int)$_POST['fornecedor_id'],
            $id
        ]);
        
        $_SESSION['msg'] = ['tipo' => 'success', 'texto' => 'Produto atualizado com sucesso!'];
        header("Location: listar.php");
        exit();
    } catch (PDOException $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Produto</h1>
            <a href="listar.php" class="btn btn-secondary">Voltar</a>
        </div>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-4 rounded shadow">
            <div class="mb-3">
                <label class="form-label">Nome do Produto</label>
                <input type="text" name="nome" class="form-control" 
                       value="<?= htmlspecialchars($produto['nome']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" name="preco" class="form-control"
                       value="<?= htmlspecialchars($produto['preco']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Fornecedor</label>
                <select name="fornecedor_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php
                    $fornecedores = $pdo->query("SELECT id, nome FROM fornecedores")->fetchAll();
                    foreach ($fornecedores as $fornecedor):
                        $selected = $fornecedor['id'] == $produto['fornecedor_id'] ? 'selected' : '';
                        echo '<option value="'.$fornecedor['id'].'" '.$selected.'>'
                            .htmlspecialchars($fornecedor['nome']).
                            '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>