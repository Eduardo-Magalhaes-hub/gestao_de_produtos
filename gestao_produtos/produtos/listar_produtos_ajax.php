<?php
require_once __DIR__ . '/../includes/conexao.php';

try {
    $produtos = $pdo->query("
        SELECT p.*, f.nome AS fornecedor 
        FROM produtos p 
        LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
        ORDER BY p.id DESC
    ")->fetchAll();

    foreach ($produtos as $produto) {
        echo '
        <tr>
            <td><input type="checkbox" name="produtos[]" value="'.$produto['id'].'" class="form-check-input"></td>
            <td>'.$produto['id'].'</td>
            <td>'.htmlspecialchars($produto['nome']).'</td>
            <td>R$ '.number_format($produto['preco'], 2, ',', '.').'</td>
            <td>'.htmlspecialchars($produto['fornecedor'] ?? 'N/D').'</td>
            <td>
                <a href="editar.php?id='.$produto['id'].'" class="btn btn-sm btn-warning">Editar</a>
                <a href="excluir.php?id='.$produto['id'].'" class="btn btn-sm btn-danger" onclick="return confirm(\'Tem certeza?\')">Excluir</a>
            </td>
        </tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="6" class="text-danger">Erro ao carregar produtos</td></tr>';
}
?>