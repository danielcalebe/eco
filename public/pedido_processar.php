<?php
session_start();
include './db.php'; // Conexão com o banco

// Verifica se o usuário está logado
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    header('Location: login.php');
    exit;
}

// Verifica se os dados do produto foram enviados
if (!isset($_POST['id_produto'], $_POST['quantidade'], $_POST['preco_unitario'], $_POST['nome_produto'])) {
    header('Location: catalogoprodutos.php');
    exit;
}

$id_produto = intval($_POST['id_produto']);
$quantidade = intval($_POST['quantidade']);
$preco_unitario = floatval($_POST['preco_unitario']);
$nome_produto = $_POST['nome_produto'];

// Calcula o total do pedido
$total_pedido = $quantidade * $preco_unitario;

// Gera um código único para o pedido
        $codigo_pedido = 'PED-' . date('mdHis') . rand(100, 999);

// Aqui você pode definir o endereço de entrega. 
// Pode ser do usuário logado, ou preencher com um campo do checkout.
$endereco_entrega = '';

// Insere o pedido na tabela pedido
$stmt_pedido = $conn->prepare("
    INSERT INTO pedido (total_pedido, forma_pagamento, id_usuario, codigo, endereco_entrega, status)
    VALUES (?, ?, ?, ?, ?, ?)
");
$forma_pagamento = 'Cartão de Crédito';
$status = 'Recebido pela empresa';
$stmt_pedido->bind_param("dissss", $total_pedido, $forma_pagamento, $id_usuario, $codigo_pedido, $endereco_entrega, $status);

if ($stmt_pedido->execute()) {
    $id_pedido = $stmt_pedido->insert_id;

    // Insere o item do pedido
    $stmt_item = $conn->prepare("
        INSERT INTO item_pedido (id_pedido, id_produto, qtd, preco_unitario)
        VALUES (?, ?, ?, ?)
    ");
    $stmt_item->bind_param("iiid", $id_pedido, $id_produto, $quantidade, $preco_unitario);
    $stmt_item->execute();

    // Redireciona para a página de checkout com o ID do pedido
    header("Location: checkout.php?codigo=$codigo_pedido");
    exit;
} else {
    echo "Erro ao criar o pedido: " . $conn->error;
}
?>
