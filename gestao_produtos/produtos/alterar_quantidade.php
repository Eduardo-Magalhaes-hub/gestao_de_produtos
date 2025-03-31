<?php
require_once __DIR__ . '/../includes/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'], $_GET['op'])) {
    header("Location: ../index.php");
    exit;
}

$id = (int)$_GET['id'];
$operacao = $_GET['op'];

if (isset($_SESSION['cesta'][$id])) {
    if ($operacao === 'inc') {
        $_SESSION['cesta'][$id]['quantidade']++;
    } elseif ($operacao === 'dec' && $_SESSION['cesta'][$id]['quantidade'] > 1) {
        $_SESSION['cesta'][$id]['quantidade']--;
    }
    
    $_SESSION['msg'] = [
        'tipo' => 'success',
        'texto' => 'Quantidade atualizada!'
    ];
}

header("Location: cesta.php");
exit;
?>