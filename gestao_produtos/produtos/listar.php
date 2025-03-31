<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/header.php';

// Verificação de acesso
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// Mensagens
$mensagem = '';
if (isset($_SESSION['msg'])) {
    $mensagem = '
    <div class="alert alert-'.$_SESSION['msg']['tipo'].' alert-dismissible fade show">
        '.$_SESSION['msg']['texto'].'
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
    unset($_SESSION['msg']);
}

// Consulta produtos
try {
    $produtos = $pdo->query("
        SELECT p.*, f.nome AS fornecedor 
        FROM produtos p 
        LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
        ORDER BY p.id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar produtos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #selecionar-todos {
            cursor: pointer;
        }
        tr:hover {
            background-color: #f8f9fa !important;
        }
        .produto-checkbox:checked + td {
            background-color: #e2f3ff;
        }
        .toast {
            min-width: 250px;
        }
        .carrinho-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .btn-sm {
         white-space: nowrap;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Lista de Produtos</h1>
            <div>
                <a href="cadastrar.php" class="btn btn-primary">+ Novo Produto</a>
                <a href="cesta.php" class="btn btn-info position-relative">
                    Carrinho
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger carrinho-contador">
                        <?= count($_SESSION['cesta'] ?? []) ?>
                    </span>
                </a>
            </div>
        </div>

        <?= $mensagem ?>

        <form id="form-cesta" method="POST">
            <div class="table-container p-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50px">
                                <input type="checkbox" id="selecionar-todos" class="form-check-input">
                            </th>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Fornecedor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="produtos[]" value="<?= $produto['id'] ?>" class="form-check-input produto-checkbox">
                                </td>
                                <td><?= $produto['id'] ?></td>
                                <td><?= htmlspecialchars($produto['nome']) ?></td>
                                <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($produto['fornecedor'] ?? 'N/D') ?></td>
                                <td>
                                    <a href="editar.php?id=<?= $produto['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="excluir.php?id=<?= $produto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success" id="btn-adicionar">
                    Adicionar Selecionados ao Carrinho
                </button>
            </div>
        </form>
    </div>

    <!-- Botão flutuante do carrinho -->
    <a href="cesta.php" class="btn btn-primary carrinho-btn rounded-circle p-3">
        🛒 <span class="badge bg-danger carrinho-contador"><?= count($_SESSION['cesta'] ?? []) ?></span>
    </a>

    <!-- Container para notificações -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Seleção/deseleção de todos os checkboxes
        $('#selecionar-todos').change(function() {
            $('.produto-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Validação do formulário
        $('#form-cesta').submit(function(e) {
            e.preventDefault();
            
            const produtos = $('.produto-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            // Validação mínima de 1 produto
            if (produtos.length === 0) {
                alert('Selecione pelo menos 1 produto!');
                return;
            }

            // Feedback visual
            const btn = $('#btn-adicionar');
            btn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" role="status"></span>
                Processando...
            `);

            // AJAX
            $.ajax({
                url: 'adicionar_cesta.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.erro) {
                        alert(response.erro);
                    } else {
                        // Atualiza contadores
                        $('.carrinho-contador').text(response.total_itens);
                        
                        // Feedback visual
                        const feedback = $(`
                            <div class="toast align-items-center text-white bg-success" role="alert">
                                <div class="d-flex">
                                    <div class="toast-body">
                                        ${produtos.length} itens adicionados! Total: R$ ${response.total_geral.toFixed(2)}
                                    </div>
                                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                                </div>
                            </div>
                        `);
                        $('.toast-container').append(feedback);
                        feedback.toast({ delay: 3000 }).toast('show');
                    }
                },
                error: function() {
                    alert('Erro na comunicação com o servidor');
                },
                complete: function() {
                    btn.prop('disabled', false).html('Adicionar ao Carrinho');
                }
            });
        });
    });
    </script>
</body>
</html>