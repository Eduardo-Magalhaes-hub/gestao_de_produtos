<?php
// Configurações (substitua pelos seus dados)
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestao_produtos');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro de conexão: " . $e->getMessage());
    die("Erro ao conectar ao banco. Contate o administrador.");
}
?>