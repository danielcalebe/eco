<?php
session_start();
if (!isset($_SESSION['admin'])) exit;

// Configuração do banco
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecoraiz";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Falha na conexão: " . $conn->connect_error);

// Evita notices
$_POST = $_POST ?? [];
$_GET = $_GET ?? [];

$action = $_REQUEST['action'] ?? '';

function json_error($msg){
    header('Content-Type: application/json');
    echo json_encode(['error'=>$msg]);
    exit;
}

if ($action == 'save') {
    $id = $_POST['id_funcionario'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $cargo = $_POST['cargo'] ?? '';
    $status = $_POST['status'] ?? '';

    // validação
    if (!$nome || !$email || (!$id && !$senha)) json_error('Campos obrigatórios não enviados.');

    if ($id) { // EDITAR
        if ($senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE funcionario SET nome_funcionario=?, email=?, senha=?, telefone=?, cpf=?, cargo=?, status=? WHERE id_funcionario=?");
            $stmt->bind_param("sssssssi", $nome, $email, $senha_hash, $telefone, $cpf, $cargo, $status, $id);
        } else {
            $stmt = $conn->prepare("UPDATE funcionario SET nome_funcionario=?, email=?, telefone=?, cpf=?, cargo=?, status=? WHERE id_funcionario=?");
            $stmt->bind_param("ssssssi", $nome, $email, $telefone, $cpf, $cargo, $status, $id);
        }
    } else { // ADICIONAR
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO funcionario (nome_funcionario, email, senha, telefone, cpf, cargo, status) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssss", $nome, $email, $senha_hash, $telefone, $cpf, $cargo, $status);
    }

    if (!$stmt->execute()) json_error('Erro ao salvar funcionário: ' . $stmt->error);

    echo json_encode(['success' => true, 'id' => $id ?: $conn->insert_id]);
    $stmt->close();
    exit;
}

elseif ($action == 'delete') {
    $id = $_POST['id_funcionario'] ?? 0;
    if (!$id) json_error('ID não informado.');

    $stmt = $conn->prepare("DELETE FROM funcionario WHERE id_funcionario=?");
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) json_error('Erro ao deletar funcionário: ' . $stmt->error);

    echo json_encode(['success' => true]);
    $stmt->close();
    exit;
}

elseif ($action == 'get_funcionario') {
    $id = $_GET['id_funcionario'] ?? 0;
    if (!$id) json_error('ID não informado.');

    $stmt = $conn->prepare("SELECT id_funcionario, nome_funcionario AS nome, email, telefone, cpf, cargo, status FROM funcionario WHERE id_funcionario=?");
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) json_error('Erro ao buscar funcionário: ' . $stmt->error);

    $res = $stmt->get_result();
    $data = $res->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($data ?: []);
    $stmt->close();
    exit;
}

$conn->close();
?>
