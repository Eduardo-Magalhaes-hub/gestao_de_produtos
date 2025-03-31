<?php
require_once __DIR__ . '/../includes/conexao.php';

try {
    $fornecedores = $pdo->query("SELECT * FROM fornecedores ORDER BY id DESC")->fetchAll();

    foreach ($fornecedores as $fornecedor) {
        // Verifica se CNPJ existe e formata
        $cnpj_formatado = '';
        if (!empty($fornecedor['cnpj'])) {
            $cnpj_formatado = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $fornecedor['cnpj']);
        }
        
        echo '
        <tr>
            <td>'.$fornecedor['id'].'</td>
            <td>'.htmlspecialchars($fornecedor['nome']).'</td>
            <td>'.$cnpj_formatado.'</td>
            <td>
                <a href="editar.php?id='.$fornecedor['id'].'" class="btn btn-sm btn-warning">Editar</a>
                <a href="excluir.php?id='.$fornecedor['id'].'" class="btn btn-sm btn-danger" onclick="return confirm(\'Tem certeza?\')">Excluir</a>
            </td>
        </tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="4" class="text-danger">Erro ao carregar fornecedores: '.htmlspecialchars($e->getMessage()).'</td></tr>';
}
?>