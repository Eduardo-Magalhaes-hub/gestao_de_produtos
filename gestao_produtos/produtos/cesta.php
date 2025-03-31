<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/header.php';

// Verifica autenticação
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// Tratamento de mensagens
$mensagem = null;
if (isset($_SESSION['msg'])) {
    $mensagem = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Processa itens do carrinho
$total = 0;
$produtosCesta = [];

if (!empty($_SESSION['cesta'])) {
    foreach ($_SESSION['cesta'] as $id => $produto) {
        // Valida estrutura do item
        if (!isset($produto['id'], $produto['nome'], $produto['preco'], $produto['quantidade'])) {
            error_log("Item inválido no carrinho: ID $id");
            continue;
        }

        // Calcula subtotal
        $subtotal = $produto['preco'] * $produto['quantidade'];
        $total += $subtotal;

        $produtosCesta[] = [
            'id' => $produto['id'],
            'nome' => $produto['nome'],
            'preco' => $produto['preco'],
            'quantidade' => $produto['quantidade'],
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .container {
            max-width: 800px;
        }
        .empty-cart {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .btn-sm {
            white-space: nowrap;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-control button {
            width: 30px;
            height: 30px;
        }
        .quantity-control input {
            width: 50px;
            text-align: center;
            margin: 0 5px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Seu Carrinho</h1>
            <a href="listar.php" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Continuar Comprando
            </a>
        </div>

        <?php if ($mensagem) : ?>
            <div class="alert alert-<?= $mensagem['tipo'] ?> alert-dismissible fade show">
                <?= $mensagem['texto'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($produtosCesta)) : ?>
            <div class="empty-cart bg-white p-5 rounded shadow">
                <div class="text-center">
                    <i class="bi bi-cart-x text-muted" style="font-size: 5rem;"></i>
                    <h4 class="text-muted mt-3">Seu carrinho está vazio</h4>
                    <a href="listar.php" class="btn btn-primary mt-3">
                        <i class="bi bi-cart-plus"></i> Voltar às Compras
                    </a>
                </div>
            </div>
        <?php else : ?>
            <div class="table-responsive bg-white p-3 rounded shadow">
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Produto</th>
                            <th>Preço Unitário</th>
                            <th class="text-center">Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtosCesta as $produto) : ?>
                            <tr>
                                <td><?= htmlspecialchars($produto['nome']) ?></td>
                                <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                <td class="text-center">
                                    <div class="quantity-control">
                                        <a href="alterar_quantidade.php?id=<?= $produto['id'] ?>&op=dec" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-dash"></i>
                                        </a>
                                        <span><?= $produto['quantidade'] ?></span>
                                        <a href="alterar_quantidade.php?id=<?= $produto['id'] ?>&op=inc" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-plus"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>R$ <?= number_format($produto['subtotal'], 2, ',', '.') ?></td>
                                <td>
                                    <a href="remover_cesta.php?id=<?= $produto['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Tem certeza que deseja remover este item?')">
                                       <i class="bi bi-trash"></i> Remover
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th colspan="2">R$ <?= number_format($total, 2, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="limpar_cesta.php" 
                   class="btn btn-outline-danger"
                   onclick="return confirm('Limpar todo o carrinho?')">
                   <i class="bi bi-x-circle"></i> Limpar Carrinho
                </a>
                <a href="finalizar.php" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Finalizar Compra
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>