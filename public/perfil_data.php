<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_usuario'])){
    echo json_encode(['error'=>'Usuário não logado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Consulta usuário
$sql = "SELECT nome, email, telefone, tipo_usuario FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 0){
    echo json_encode(['error'=>'Usuário não encontrado']);
    exit;
}
$usuario = $result->fetch_assoc();

// Consulta extras
if($usuario['tipo_usuario'] === 'PF'){
    $sqlFisico = "SELECT cpf, data_nascimento FROM cliente_fisico WHERE id_usuario = ?";
    $stmtFisico = $conn->prepare($sqlFisico);
    $stmtFisico->bind_param("i",$id_usuario);
    $stmtFisico->execute();
    $resFisico = $stmtFisico->get_result();
    $extra = $resFisico->fetch_assoc();
    $usuario = array_merge($usuario, $extra ?: []);
}else{
    $sqlJuridico = "SELECT cnpj, razao_social FROM cliente_juridico WHERE id_usuario = ?";
    $stmtJuridico = $conn->prepare($sqlJuridico);
    $stmtJuridico->bind_param("i",$id_usuario);
    $stmtJuridico->execute();
    $resJuridico = $stmtJuridico->get_result();
    $extra = $resJuridico->fetch_assoc();
    $usuario = array_merge($usuario, $extra ?: []);
}

// Retorna JSON
header('Content-Type: application/json');
echo json_encode($usuario);
