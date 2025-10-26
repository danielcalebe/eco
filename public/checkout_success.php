<?php
// checkout_success.php
$codigo = $_GET['codigo'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedido concluído</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="alert alert-success text-center" role="alert">
        Pedido <strong><?= htmlspecialchars($codigo) ?></strong> realizado com sucesso!
    </div>
    <a href="index.php" class="btn btn-primary mt-3">Voltar à página inicial</a>
</body>
</html>
