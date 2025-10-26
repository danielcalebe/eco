<?php
session_start();
if (!isset($_SESSION['admin'])) exit;

include "db.php";
// Evita notices
$_POST = $_POST ?? [];
$_GET = $_GET ?? [];

$action = $_REQUEST['action'] ?? 'save';

// Função auxiliar para retornar erro em JSON
function json_error($msg) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $msg]);
    exit;
}

if ($action == 'save') {
    $id = $_POST['id_pedido'] ?? '';
    $id_usuario = $_POST['id_usuario'] ?? null;
    $total_pedido = $_POST['total_pedido'] ?? null;
    $forma_pagamento = $_POST['forma_pagamento'] ?? '';
    $status = $_POST['status'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $endereco_entrega = $_POST['endereco_entrega'] ?? '';

    // valida campos obrigatórios
    if (!$id_usuario || !$total_pedido || !$forma_pagamento || !$status || !$endereco_entrega) {
        json_error('Campos obrigatórios não enviados.');
    }

    if ($id) { // EDITAR
        $stmt = $conn->prepare("
            UPDATE pedido SET 
                id_usuario = ?, 
                total_pedido = ?, 
                forma_pagamento = ?, 
                status = ?,
                endereco_entrega = ?
            WHERE id_pedido = ?
        ");
        $stmt->bind_param("idsssi", $id_usuario, $total_pedido, $forma_pagamento, $status, $endereco_entrega, $id);
    } else { // ADICIONAR
        // Gera código aleatório DOACAO-03232190923
        $codigo = 'DOACAO-' . date('mdHis') . rand(100, 999);
        $stmt = $conn->prepare("
            INSERT INTO pedido (id_usuario, total_pedido, forma_pagamento, status, endereco_entrega, codigo) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("idssss", $id_usuario, $total_pedido, $forma_pagamento, $status, $endereco_entrega, $codigo);
    }

    if (!$stmt->execute()) {
        json_error('Erro ao salvar pedido: ' . $stmt->error);
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id' => $id ?: $conn->insert_id]);
    exit;

} elseif ($action == 'delete') {
    $id = $_POST['id_pedido'] ?? 0;
    if (!$id) json_error('ID do pedido não informado.');

    $stmt = $conn->prepare("DELETE FROM pedido WHERE id_pedido=?");
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) json_error('Erro ao deletar pedido: ' . $stmt->error);
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;

} elseif ($action == 'get') {
    $id = $_GET['id_pedido'] ?? 0;
    if (!$id) json_error('ID do pedido não informado.');

    $stmt = $conn->prepare("SELECT * FROM pedido WHERE id_pedido=?");
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) json_error('Erro ao buscar pedido: ' . $stmt->error);

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($data ?: []);
    $stmt->close();
    exit;
}

$conn->close();
?>
