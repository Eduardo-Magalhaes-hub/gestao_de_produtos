<?php
// includes/header.php - VERSÃO FINAL CORRIGIDA

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .nav-link.active {
            font-weight: 600;
            border-bottom: 2px solid white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../produtos/listar.php">
                <i class="bi bi-box-seam"></i> Gestão de Produtos
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'listar.php' ? 'active' : '' ?>" 
                           href="../produtos/listar.php">
                            <i class="bi bi-cart"></i> Produtos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['SCRIPT_NAME']) == 'listar.php' ? 'active' : '' ?>" 
                           href="../fornecedores/listar.php">
                            <i class="bi bi-truck"></i> Fornecedores
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <span class="text-light me-3">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
                    </span>
                    <a href="../logout.php" class="btn btn-sm btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-4">