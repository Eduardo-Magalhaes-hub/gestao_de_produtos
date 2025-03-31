<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

header('Content-Type: application/json');

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Acesso não autorizado']);
    exit();
}

// Recebe dados
$produtos_ids = $_POST['produtos'] ?? [];

// Validação
if (empty($produtos_ids)) {
    echo json_encode(['erro' => 'Nenhum produto selecionado']);
    exit();
}

try {
    // Inicializa cesta se não existir
    if (!isset($_SESSION['cesta'])) {
        $_SESSION['cesta'] = [];
    }

    $produtos_adicionados = 0;

    // Processa cada produto
    foreach ($produtos_ids as $produto_id) {
        $produto_id = (int)$produto_id;
        
        // Busca produto no banco
        $stmt = $pdo->prepare("SELECT id, nome, preco FROM produtos WHERE id = ?");
        $stmt->execute([$produto_id]);
        $produto = $stmt->fetch();

        if (!$produto) continue; // Ignora IDs inválidos

        // Adiciona/atualiza na cesta
        if (isset($_SESSION['cesta'][$produto_id])) {
            $_SESSION['cesta'][$produto_id]['quantidade'] += 1;
        } else {
            $_SESSION['cesta'][$produto_id] = [
                'id' => $produto['id'],
                'nome' => $produto['nome'],
                'preco' => (float)$produto['preco'],
                'quantidade' => 1
            ];
        }
        $produtos_adicionados++;
    }

    // Calcula totais
    $total_itens = count($_SESSION['cesta']);
    $total_geral = array_reduce($_SESSION['cesta'], function($sum, $item) {
        return $sum + ($item['preco'] * $item['quantidade']);
    }, 0);

    echo json_encode([
        'sucesso' => true,
        'total_itens' => $total_itens,
        'total_geral' => $total_geral,
        'mensagem' => "$produtos_adicionados itens adicionados"
    ]);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>