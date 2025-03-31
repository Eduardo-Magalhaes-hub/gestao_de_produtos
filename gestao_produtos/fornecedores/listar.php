<?php
require_once __DIR__ . '/../includes/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// Mensagens
$mensagem = '';
if (isset($_GET['success'])) {
    $mensagem = '<div class="alert alert-success">Operação realizada com sucesso!</div>';
} elseif (isset($_GET['error'])) {
    $mensagem = '<div class="alert alert-danger">Erro ao processar a solicitação.</div>';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Fornecedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #tabela-fornecedores {
            transition: opacity 0.3s;
        }
        .updating {
            opacity: 0.7;
        }

        td:empty::before {
    content: "N/A";
    color: #999;
    font-style: italic;
}
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <h2 class="mb-4">Fornecedores</h2>
        <?= $mensagem ?>
        <a href="cadastrar.php" class="btn btn-primary mb-3">Novo Fornecedor</a>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-fornecedores">
                    <?php include 'listar_fornecedores_ajax.php'; ?>
                </tbody>
            </table>
        </div>
    </div>

    <<script>
$(document).ready(function() {
    function atualizarFornecedores() {
        $.ajax({
            url: 'listar_fornecedores_ajax.php',
            method: 'GET',
            success: function(data) {
                $('#tabela-fornecedores').html(data);
            },
            error: function(xhr) {
                console.error('Erro na atualização:', xhr.statusText);
            }
        });
    }

    // Atualiza imediatamente e a cada 30 segundos
    atualizarFornecedores();
    setInterval(atualizarFornecedores, 30000);
});
</script>
</body>
</html>