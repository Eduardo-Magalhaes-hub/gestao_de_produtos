<?php
require_once __DIR__ . '/../includes/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    // Verifica se o fornecedor existe
    $stmt = $pdo->prepare("SELECT id FROM fornecedores WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->fetch()) {
        $pdo->prepare("DELETE FROM fornecedores WHERE id = ?")->execute([$id]);
        header("Location: listar.php?success=1");
    } else {
        header("Location: listar.php?error=1");
    }
    exit;
} catch (PDOException $e) {
    die("Erro ao excluir: " . $e->getMessage());
}
?>