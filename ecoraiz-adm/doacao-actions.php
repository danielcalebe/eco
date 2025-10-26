<?php
session_start();
if (!isset($_SESSION['admin'])) exit;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecoraiz";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Falha na conexão: " . $conn->connect_error);

$action = $_REQUEST['action'] ?? 'save';

if ($action == 'save') {
    $id = $_POST['id_doacao'] ?? '';
    $id_usuario = $_POST['id_usuario'];
    $tipo_residuo = $_POST['tipo_residuo'];
    $quantidade = $_POST['quantidade'];
    $regiao = $_POST['regiao'];
    $endereco_coleta = $_POST['endereco_coleta'];
    $status = $_POST['status'];
    $data_coleta = $_POST['data_coleta']; // novo campo

    if($id){ // editar
        $stmt = $conn->prepare("UPDATE doacao SET id_usuario=?, tipo_residuo=?, quantidade=?, endereco_coleta=?, regiao=?, status=?, data_coleta=? WHERE id_doacao=?");
        $stmt->bind_param("isdssssi", $id_usuario, $tipo_residuo, $quantidade, $endereco_coleta, $regiao, $status, $data_coleta, $id);
    } else { // adicionar
        $stmt = $conn->prepare("INSERT INTO doacao (id_usuario, tipo_residuo, quantidade, endereco_coleta, regiao, status, data_coleta, , horario_coleta, tipo_coleta, telefone, codigo) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), 'Coleta', '(31)99999-0000', CONCAT('DOA-', LPAD(FLOOR(RAND()*9999),4,'0')) )");
        $stmt->bind_param("isdssss", $id_usuario, $tipo_residuo, $quantidade, $endereco_coleta, $regiao, $status, $data_coleta);
    }
    $stmt->execute();
    $stmt->close();
    exit('ok');

} elseif($action == 'delete') {
    $id = $_POST['id_doacao'];
    $stmt = $conn->prepare("DELETE FROM doacao WHERE id_doacao=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    exit('ok');

} elseif($action == 'get') {
    $id = $_GET['id_doacao'];
    $result = $conn->query("SELECT * FROM doacao WHERE id_doacao=$id");
    echo json_encode($result->fetch_assoc());
}

$conn->close();
?>
