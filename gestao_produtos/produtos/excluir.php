<?php
require_once __DIR__ . '/../includes/conexao.php';
session_start();

// Limpa mensagens anteriores
unset($_SESSION['msg']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = ['tipo' => 'danger', 'texto' => 'ID inválido'];
    header("Location: listar.php");
    exit();
}

$id = (int)$_GET['id'];

try {
    // Verifica existência
    $stmt = $pdo->prepare("SELECT id FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->fetch()) {
        $pdo->prepare("DELETE FROM produtos WHERE id = ?")->execute([$id]);
        $_SESSION['msg'] = ['tipo' => 'success', 'texto' => 'Produto excluído com sucesso!'];
    } else {
        $_SESSION['msg'] = ['tipo' => 'warning', 'texto' => 'Produto já foi removido anteriormente'];
    }
} catch (PDOException $e) {
    $_SESSION['msg'] = ['tipo' => 'danger', 'texto' => 'Erro ao excluir: ' . $e->getMessage()];
}

header("Location: listar.php");
exit();
?>