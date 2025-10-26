<?php
header('Content-Type: application/json');
session_start();

// Ativar erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão
include './db.php';

// Verifica login
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    echo json_encode(['success' => false, 'error' => 'Usuário não logado.']);
    exit;
}

// Recebe dados
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$cpf = $_POST['cpf'] ?? null;
$data_nascimento = $_POST['data_nascimento'] ?? null;

// Formatar data para YYYY-MM-DD se existir
if ($data_nascimento) {
    $date = date_create($data_nascimento);
    $data_nascimento = $date ? date_format($date, 'Y-m-d') : null;
}

// Inicia transação
$conn->begin_transaction();

try {
    // Atualiza tabela usuario
    $stmt = $conn->prepare("UPDATE usuario SET nome=?, email=?, telefone=? WHERE id_usuario=?");
    if(!$stmt) throw new Exception("Erro prepare usuario: ".$conn->error);
    $stmt->bind_param("sssi", $nome, $email, $telefone, $id_usuario);
    if(!$stmt->execute()) throw new Exception("Erro execute usuario: ".$stmt->error);

    // Atualiza ou insere cliente_fisico se houver CPF e data
    if ($cpf && $data_nascimento) {
        $check = $conn->prepare("SELECT id_cliente_fisico FROM cliente_fisico WHERE id_usuario=?");
        if(!$check) throw new Exception("Erro prepare check: ".$conn->error);
        $check->bind_param("i", $id_usuario);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $stmt2 = $conn->prepare("UPDATE cliente_fisico SET cpf=?, data_nascimento=? WHERE id_usuario=?");
            if(!$stmt2) throw new Exception("Erro prepare update cliente_fisico: ".$conn->error);
            $stmt2->bind_param("ssi", $cpf, $data_nascimento, $id_usuario);
        } else {
            $stmt2 = $conn->prepare("INSERT INTO cliente_fisico (id_usuario, cpf, data_nascimento) VALUES (?, ?, ?)");
            if(!$stmt2) throw new Exception("Erro prepare insert cliente_fisico: ".$conn->error);
            $stmt2->bind_param("iss", $id_usuario, $cpf, $data_nascimento);
        }
        if(!$stmt2->execute()) throw new Exception("Erro execute cliente_fisico: ".$stmt2->error);
    }

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Erro update_perfil.php: ".$e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
