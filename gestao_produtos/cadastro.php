<?php
// Inclui a conexão com o banco
require_once 'includes/conexao.php';

// Processa o formulário se for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = hash('sha256', $_POST["senha"]); // Criptografa a senha

    try {
        // Prepara e executa a inserção no banco
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hashed) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $senha]);
        
        // Mensagem de sucesso
        echo "<div class='alert alert-success'>Usuário cadastrado com sucesso! <a href='index.php'>Faça login</a>.</div>";
    } catch (PDOException $e) {
        // Mensagem de erro (ex: e-mail duplicado)
        echo "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Cadastro</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" name="senha" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                        </form>
                        <p class="mt-3 text-center">
                            Já tem conta? <a href="index.php">Faça login aqui</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>