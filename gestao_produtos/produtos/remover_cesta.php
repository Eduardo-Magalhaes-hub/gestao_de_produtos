<?php
require_once __DIR__ . '/../includes/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = (int)$_GET['id'];

if (isset($_SESSION['cesta'][$id])) {
    unset($_SESSION['cesta'][$id]);
    
    $_SESSION['msg'] = [
        'tipo' => 'success',
        'texto' => 'Produto removido do carrinho!'
    ];
}

header("Location: cesta.php");
exit;
?>