<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['admin'])) {
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

include "db.php";
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Função para salvar imagens do produto (até 4)
function uploadImagensProduto($files, $oldImages = []) {
    $uploaded = [];
    if(isset($files['name']) && count($files['name']) > 0){
        for($i=0; $i<count($files['name']); $i++){
            if($files['error'][$i] === 0){
                $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $nome_img = uniqid().'.'.$ext;
                $destino = __DIR__.'/img/Produtos/'.$nome_img;
                move_uploaded_file($files['tmp_name'][$i], $destino);
                $uploaded[] = $nome_img;
            }
        }
    }
    // Retorna imagens novas ou mantém as antigas se nenhuma foi enviada
    return count($uploaded) > 0 ? $uploaded : $oldImages;
}

// === SALVAR PRODUTO ===
if ($action == 'save_produto') {
    $id = $_POST['id_produto'] ?? '';
    $nome = $_POST['nome_produto'] ?? '';
    $preco = $_POST['preco'] ?? 0;
    $qtd_estoque = $_POST['qtd_estoque'] ?? 0;
    $avaliacao = $_POST['avaliacao'] ?? 0;
    $descricao = $_POST['descricao'] ?? '';
    $status = $_POST['status'] ?? 'ativo';
    $categoria = $_POST['categoria'] ?? 'Outros';

    if (!$nome) {
        echo json_encode(['error' => 'Nome do produto obrigatório']);
        exit;
    }

    if ($id) {
        // Buscar imagens antigas
        $res = $conn->query("SELECT caminho_imagem FROM imagem_produto WHERE id_produto=$id");
        $oldImages = [];
        while($row = $res->fetch_assoc()) $oldImages[] = $row['caminho_imagem'];

        // Salvar novas imagens
        $uploadedImages = uploadImagensProduto($_FILES['imagens'] ?? null, $oldImages);

        // Atualizar produto
        $stmt = $conn->prepare("UPDATE produto SET nome_produto=?, preco=?, qtd_estoque=?, avaliacao=?, descricao=?, categoria=?, status=? WHERE id_produto=?");
        $stmt->bind_param("sdidsssi", $nome, $preco, $qtd_estoque, $avaliacao, $descricao, $categoria, $status, $id);
        $stmt->execute();
        $stmt->close();

        // Atualizar imagens: apagar antigas e inserir novas
        $conn->query("DELETE FROM imagem_produto WHERE id_produto=$id");
        if(count($uploadedImages) > 0){
            $stmt = $conn->prepare("INSERT INTO imagem_produto (id_produto, caminho_imagem) VALUES (?, ?)");
            foreach($uploadedImages as $img){
                $stmt->bind_param("is", $id, $img);
                $stmt->execute();
            }
            $stmt->close();
        }

    } else {
        // Inserir novo produto
        $stmt = $conn->prepare("INSERT INTO produto (nome_produto, preco, qtd_estoque, avaliacao, descricao, categoria, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdidsss", $nome, $preco, $qtd_estoque, $avaliacao, $descricao, $categoria, $status);
        $stmt->execute();
        $id_produto = $stmt->insert_id;
        $stmt->close();

        // Inserir imagens
        $uploadedImages = uploadImagensProduto($_FILES['imagens'] ?? null);
        if(count($uploadedImages) > 0){
            $stmt = $conn->prepare("INSERT INTO imagem_produto (id_produto, caminho_imagem) VALUES (?, ?)");
            foreach($uploadedImages as $img){
                $stmt->bind_param("is", $id_produto, $img);
                $stmt->execute();
            }
            $stmt->close();
        }
    }

    echo json_encode(['success' => true]);
    exit;
}

// === DELETAR PRODUTO ===
if ($action == 'delete_produto') {
    $id = $_POST['id_produto'] ?? '';
    if (!$id) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    // Apagar imagens do produto
    $res = $conn->query("SELECT caminho_imagem FROM imagem_produto WHERE id_produto=$id");
    while($img = $res->fetch_assoc()){
        if(file_exists(__DIR__.'/img/Produtos/'.$img['caminho_imagem'])){
            unlink(__DIR__.'/img/Produtos/'.$img['caminho_imagem']);
        }
    }

    $conn->query("DELETE FROM imagem_produto WHERE id_produto=$id");
    $conn->query("DELETE FROM produto WHERE id_produto=$id");

    echo json_encode(['success' => true]);
    exit;
}

// === OBTER PRODUTO ===
if ($action == 'get_produto') {
    $id = $_POST['id_produto'] ?? '';
    if (!$id) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    $res = $conn->query("SELECT * FROM produto WHERE id_produto=$id LIMIT 1");
    $produto = $res->fetch_assoc();
    if(!$produto){
        echo json_encode(['error' => 'Produto não encontrado']);
        exit;
    }

    // Buscar imagens
    $res = $conn->query("SELECT caminho_imagem FROM imagem_produto WHERE id_produto=$id");
    $imagens = [];
    while($row = $res->fetch_assoc()) $imagens[] = $row['caminho_imagem'];

    echo json_encode([
        'id_produto' => $produto['id_produto'],
        'nome_produto' => $produto['nome_produto'],
        'preco' => $produto['preco'],
        'qtd_estoque' => $produto['qtd_estoque'],
        'categoria' => $produto['categoria'],
        'avaliacao' => $produto['avaliacao'],
        'descricao' => $produto['descricao'],
        'status' => $produto['status'],
        'imagens' => $imagens
    ]);
    exit;
}

echo json_encode(['error' => 'Ação inválida']);
$conn->close();
?>
