<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$_SESSION['cesta'] = [];
$_SESSION['msg'] = [
    'tipo' => 'success',
    'texto' => 'Carrinho limpo com sucesso!'
];

header("Location: cesta.php");
exit;
?>