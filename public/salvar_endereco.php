<?php
// salvar_endereco.php
header('Content-Type: application/json');
session_start();

include './db.php';

// Recebe os dados JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['codigoPedido'], $input['nome'], $input['endereco'])) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit;
}

$codigoPedido = $input['codigoPedido'];
$nome = $input['nome'];
$endereco = $input['endereco'];
$forma_pagamento = "Cartão";

// Atualiza o nome, endereço e forma de pagamento do pedido
$stmt = $conn->prepare("UPDATE pedido SET nome_endereco = ?, endereco_entrega = ?, forma_pagamento = ? WHERE codigo = ?");
$stmt->bind_param("ssss", $nome, $endereco, $forma_pagamento, $codigoPedido);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
