<?php
session_start();

// Limpa todas as variáveis da sessão
$_SESSION = [];

// Destroi a sessão
session_destroy();

// Redireciona para login ou página inicial
header("Location: login.php");
exit();
