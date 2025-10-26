<?php
header('Content-Type: application/json');
session_start();

include './db.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  echo json_encode(['success' => false, 'error' => 'Falha na conexão.']);
  exit;
}

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
  echo json_encode(['success' => false, 'error' => 'Usuário não logado.']);
  exit;
}

// Busca informações do usuário base
$sqlUser = "SELECT u.id_usuario, u.nome, u.email, u.telefone, u.img_caminho,
                   cf.cpf, cf.data_nascimento,
                   cj.cnpj, cj.razao_social
            FROM usuario u
            LEFT JOIN cliente_fisico cf ON cf.id_usuario = u.id_usuario
            LEFT JOIN cliente_juridico cj ON cj.id_usuario = u.id_usuario
            WHERE u.id_usuario = ?";
$stmt = $conn->prepare($sqlUser);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultUser = $stmt->get_result();
$usuario = $resultUser->fetch_assoc();

// Busca histórico de doações
$sqlDoa = "SELECT codigo, quantidade, data, status FROM doacao WHERE id_usuario = ? ORDER BY data DESC";
$stmt2 = $conn->prepare($sqlDoa);
$stmt2->bind_param("i", $id_usuario);
$stmt2->execute();
$resDoa = $stmt2->get_result();
$doacoes = [];
while ($row = $resDoa->fetch_assoc()) {
  $doacoes[] = $row;
}

echo json_encode([
  'success' => true,
  'usuario' => $usuario,
  'doacoes' => $doacoes
]);
?>
