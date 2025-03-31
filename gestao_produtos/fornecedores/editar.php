<?php
require_once __DIR__ . '/../includes/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = (int)$_GET['id'];

// Busca fornecedor
try {
    $stmt = $pdo->prepare("SELECT * FROM fornecedores WHERE id = ?");
    $stmt->execute([$id]);
    $fornecedor = $stmt->fetch();

    if (!$fornecedor) {
        header("Location: listar.php?error=1");
        exit;
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

// Processa edição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj']);

    try {
        $stmt = $pdo->prepare("UPDATE fornecedores SET nome = ?, cnpj = ? WHERE id = ?");
        $stmt->execute([$nome, $cnpj, $id]);
        header("Location: listar.php?success=1");
        exit;
    } catch (PDOException $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Fornecedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Fornecedor</h2>
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($fornecedor['nome']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">CNPJ</label>
                <input type="text" name="cnpj" class="form-control" value="<?= htmlspecialchars($fornecedor['cnpj']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="listar.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</body>
</html>