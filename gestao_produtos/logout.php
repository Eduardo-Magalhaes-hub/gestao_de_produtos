<?php
// logout.php (pasta raiz) - VERSÃO FINAL OTIMIZADA

// 1. Controle de sessão seguro
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2. Limpeza profunda
$_SESSION = [];
session_regenerate_id(true);

// 3. Destruição do cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 86400,  // Expira 1 dia atrás
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// 4. Destruição final
session_destroy();

// 5. Redirecionamento inteligente
$is_subfolder = (strpos($_SERVER['REQUEST_URI'], '/gestao_produtos/') !== false);
$base_path = $is_subfolder ? '/gestao_produtos' : '';
$redirect_url = "{$base_path}/index.php?logout=1";

// 6. Header seguro contra cache
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Location: " . $redirect_url);
exit;
?>