<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['id_usuario'])){
    echo json_encode(['success'=>false,'error'=>'Usuário não logado']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar arquivo
if(!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK){
    echo json_encode(['success'=>false,'error'=>'Nenhuma imagem enviada ou erro no upload']);
    exit();
}

include 'db.php';



$file = $_FILES['photo'];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$nome_img = 'user_'.$id_usuario.'.'.$ext;
$uploadDir = __DIR__ . '/../ecoraiz-adm/img/Usuarios/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
$destino = $uploadDir . $nome_img;

if(move_uploaded_file($file['tmp_name'], $destino)){
    $stmt = $conn->prepare("UPDATE usuario SET nome_img=? WHERE id_usuario=?");
    $stmt->bind_param("si", $nome_img, $id_usuario);
    $stmt->execute();
    echo json_encode(['success'=>true,'nome_img'=>$nome_img]);
} else {
    echo json_encode(['success'=>false,'error'=>'Erro ao salvar imagem']);
}
?>
