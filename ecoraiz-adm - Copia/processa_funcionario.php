<?php
include_once("db.php");

$action = $_POST['action'] ?? '';

if ($action == 'listar') {
    $result = $conn->query("SELECT * FROM funcionario ORDER BY id_funcionario ASC");
    $funcionarios = [];
    while ($row = $result->fetch_assoc()) {
        $row['senha'] = '********'; // não mostrar senha real
        $funcionarios[] = $row;
    }
    echo json_encode($funcionarios);
}

if ($action == 'adicionar') {
    $cpf = $_POST['cpf'];
    $cargo = $_POST['cargo'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO funcionario (cpf, cargo, email, senha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $cpf, $cargo, $email, $senha);
    echo $stmt->execute() ? 'success' : 'error';
}

if ($action == 'editar') {
    $id = $_POST['id'];
    $cpf = $_POST['cpf'];
    $cargo = $_POST['cargo'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (!empty($senha)) {
        $senha = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE funcionario SET cpf=?, cargo=?, email=?, senha=? WHERE id_funcionario=?");
        $stmt->bind_param("ssssi", $cpf, $cargo, $email, $senha, $id);
    } else {
        $stmt = $conn->prepare("UPDATE funcionario SET cpf=?, cargo=?, email=? WHERE id_funcionario=?");
        $stmt->bind_param("sssi", $cpf, $cargo, $email, $id);
    }
    echo $stmt->execute() ? 'success' : 'error';
}

if ($action == 'excluir') {
    $ids = explode(',', $_POST['ids']); // transforma string em array
    $ids_str = implode(',', array_map('intval', $ids));
    $result = $conn->query("DELETE FROM funcionario WHERE id_funcionario IN ($ids_str)");
    echo $result ? 'success' : 'error';
}
?>
