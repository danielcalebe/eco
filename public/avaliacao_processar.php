<?php
session_start();
include './db.php'; // conexão com o banco

// Verifica se o usuário está logado
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    $_SESSION['erro_avaliacao'] = "Você precisa estar logado para avaliar o produto.";
    header('Location: catalogoprodutos.php');
    exit;
}

// Verifica se os dados do formulário foram enviados
if (!isset($_POST['id_produto'], $_POST['nota'], $_POST['comentario'])) {
    $_SESSION['erro_avaliacao'] = "Dados inválidos.";
    header('Location: catalogoprodutos.php');
    exit;
}

$id_produto = intval($_POST['id_produto']);
$nota = intval($_POST['nota']);
$comentario = trim($_POST['comentario']);

// Limita nota entre 1 e 5
if ($nota < 1 || $nota > 5) {
    $_SESSION['erro_avaliacao'] = "Nota inválida.";
    header("Location: detalhesproduto.php?id=$id_produto");
    exit;
}

// Verifica se o usuário comprou o produto
$stmt_check = $conn->prepare("
    SELECT 1
    FROM item_pedido ip
    JOIN pedido p ON ip.id_pedido = p.id_pedido
    WHERE ip.id_produto = ? AND p.id_usuario = ?
    LIMIT 1
");
$stmt_check->bind_param("ii", $id_produto, $id_usuario);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows === 0) {
    $_SESSION['erro_avaliacao'] = "Você só pode avaliar produtos que comprou.";
    header("Location: detalhesproduto.php?id=$id_produto");
    exit;
}

// Insere a avaliação
$stmt_insert = $conn->prepare("
    INSERT INTO avaliacao (id_produto, id_usuario, nota, comentario, data_avaliacao)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt_insert->bind_param("iiis", $id_produto, $id_usuario, $nota, $comentario);
if ($stmt_insert->execute()) {
    $_SESSION['sucesso_avaliacao'] = "Avaliação enviada com sucesso!";
} else {
    $_SESSION['erro_avaliacao'] = "Erro ao enviar avaliação. Tente novamente.";
}

// Redireciona de volta para a página do produto
header("Location: detalhesproduto.php?id=$id_produto");
exit;
?>
