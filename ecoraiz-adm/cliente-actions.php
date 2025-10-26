<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['admin'])) {
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

// Conexão com o banco
include "db.php";
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Função para salvar imagem
function uploadImagem($file, $oldName = null) {
    if(isset($file) && $file['error'] == 0){
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nome_img = uniqid().'.'.$ext;
        $destino = __DIR__.'/img/Usuarios/'.$nome_img;
        move_uploaded_file($file['tmp_name'], $destino);

        // Apaga a imagem antiga se existir
        if($oldName && file_exists(__DIR__.'/img/Usuarios/'.$oldName)){
            unlink(__DIR__.'/img/Usuarios/'.$oldName);
        }
        return $nome_img;
    }
    return $oldName; // mantém a antiga se não enviou nova
}

// === SALVAR PF ===
if ($action == 'save_pf') {
    $id = $_POST['id_usuario_pf'] ?? '';
    $nome = $_POST['nome_pf'] ?? '';
    $email = $_POST['email_pf'] ?? '';
    $telefone = $_POST['telefone_pf'] ?? '';
    $cpf = $_POST['cpf_pf'] ?? '';
    $nascimento = $_POST['nascimento_pf'] ?? '';
    $senha = $_POST['senha_pf'] ?? '';

    if (!$nome || !$email || (!$id && !$senha)) { // senha obrigatória só no cadastro
        echo json_encode(['error' => 'Nome, email e senha obrigatórios']);
        exit;
    }

    $senha_hash = $senha ? password_hash($senha, PASSWORD_DEFAULT) : null;

    if ($id) {
        // Pega imagem antiga
        $res = $conn->query("SELECT nome_img FROM usuario WHERE id_usuario=$id LIMIT 1");
        $old = $res->fetch_assoc();
        $nome_img = uploadImagem($_FILES['img_pf'] ?? null, $old['nome_img']);

        // Atualizar usuário
        if($senha_hash){
            $stmt = $conn->prepare("UPDATE usuario SET nome=?, email=?, tipo_usuario='PF', telefone=?, senha=?, nome_img=? WHERE id_usuario=?");
            $stmt->bind_param("sssssi", $nome, $email, $telefone, $senha_hash, $nome_img, $id);
        } else {
            $stmt = $conn->prepare("UPDATE usuario SET nome=?, email=?, tipo_usuario='PF', telefone=?, nome_img=? WHERE id_usuario=?");
            $stmt->bind_param("ssssi", $nome, $email, $telefone, $nome_img, $id);
        }
        $stmt->execute();
        $stmt->close();

        // Atualizar cliente_fisico
        $stmt = $conn->prepare("UPDATE cliente_fisico SET cpf=?, data_nascimento=? WHERE id_usuario=?");
        $stmt->bind_param("ssi", $cpf, $nascimento, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Inserir usuário
        $nome_img = uploadImagem($_FILES['img_pf'] ?? null);
        $stmt = $conn->prepare("INSERT INTO usuario (nome, email, tipo_usuario, telefone, senha, nome_img) VALUES (?, ?, 'PF', ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $email, $telefone, $senha_hash, $nome_img);
        $stmt->execute();
        $id_usuario = $stmt->insert_id;
        $stmt->close();

        // Inserir cliente_fisico
        $stmt = $conn->prepare("INSERT INTO cliente_fisico (id_usuario, cpf, data_nascimento) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_usuario, $cpf, $nascimento);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['success' => true]);
    exit;
}

// === SALVAR PJ ===
if ($action == 'save_pj') {
    $id = $_POST['id_usuario_pj'] ?? '';
    $razao = $_POST['razao_social_pj'] ?? '';
    $email = $_POST['email_pj'] ?? '';
    $telefone = $_POST['telefone_pj'] ?? '';
    $cnpj = $_POST['cnpj_pj'] ?? '';
    $senha = $_POST['senha_pj'] ?? '';

    if (!$razao || !$email || (!$id && !$senha)) {
        echo json_encode(['error' => 'Razão social, email e senha obrigatórios']);
        exit;
    }

    $senha_hash = $senha ? password_hash($senha, PASSWORD_DEFAULT) : null;

    if ($id) {
        $res = $conn->query("SELECT nome_img FROM usuario WHERE id_usuario=$id LIMIT 1");
        $old = $res->fetch_assoc();
        $nome_img = uploadImagem($_FILES['img_pj'] ?? null, $old['nome_img']);

        // Atualizar usuário
        if($senha_hash){
            $stmt = $conn->prepare("UPDATE usuario SET nome=?, email=?, tipo_usuario='PJ', telefone=?, senha=?, nome_img=? WHERE id_usuario=?");
            $stmt->bind_param("sssssi", $razao, $email, $telefone, $senha_hash, $nome_img, $id);
        } else {
            $stmt = $conn->prepare("UPDATE usuario SET nome=?, email=?, tipo_usuario='PJ', telefone=?, nome_img=? WHERE id_usuario=?");
            $stmt->bind_param("ssssi", $razao, $email, $telefone, $nome_img, $id);
        }
        $stmt->execute();
        $stmt->close();

        // Atualizar cliente_juridico
        $stmt = $conn->prepare("UPDATE cliente_juridico SET cnpj=?, razao_social=? WHERE id_usuario=?");
        $stmt->bind_param("ssi", $cnpj, $razao, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $nome_img = uploadImagem($_FILES['img_pj'] ?? null);

        // Inserir usuário
        $stmt = $conn->prepare("INSERT INTO usuario (nome, email, tipo_usuario, telefone, senha, nome_img) VALUES (?, ?, 'PJ', ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $razao, $email, $telefone, $senha_hash, $nome_img);
        $stmt->execute();
        $id_usuario = $stmt->insert_id;
        $stmt->close();

        // Inserir cliente_juridico
        $stmt = $conn->prepare("INSERT INTO cliente_juridico (id_usuario, cnpj, razao_social) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_usuario, $cnpj, $razao);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['success' => true]);
    exit;
}

// === DELETAR ===
if ($action == 'delete') {
    $id = $_POST['id_usuario'] ?? '';
    if (!$id) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    // Apagar imagem do usuário
    $res = $conn->query("SELECT nome_img FROM usuario WHERE id_usuario=$id LIMIT 1");
    if($img = $res->fetch_assoc()){
        if($img['nome_img'] && file_exists(__DIR__.'/img/Usuarios/'.$img['nome_img'])){
            unlink(__DIR__.'/img/Usuarios/'.$img['nome_img']);
        }
    }

    $conn->query("DELETE FROM cliente_fisico WHERE id_usuario=$id");
    $conn->query("DELETE FROM cliente_juridico WHERE id_usuario=$id");
    $conn->query("DELETE FROM usuario WHERE id_usuario=$id");

    echo json_encode(['success' => true]);
    exit;
}

// === OBTER USUÁRIO (EDITAR) ===
if ($action == 'get_user') {
    $id = $_POST['id_usuario'] ?? '';
    if (!$id) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    $res = $conn->query("
        SELECT u.id_usuario, u.nome, u.email, u.tipo_usuario, u.telefone, u.nome_img,
               cf.cpf AS cpf_cliente, cf.data_nascimento,
               cj.cnpj AS cnpj_cliente, cj.razao_social
        FROM usuario u
        LEFT JOIN cliente_fisico cf ON u.id_usuario = cf.id_usuario AND u.tipo_usuario='PF'
        LEFT JOIN cliente_juridico cj ON u.id_usuario = cj.id_usuario AND u.tipo_usuario='PJ'
        WHERE u.id_usuario=$id LIMIT 1
    ");

    $data = $res->fetch_assoc();
    if (!$data) {
        echo json_encode(['error' => 'Usuário não encontrado']);
    } else {
        echo json_encode([
            'id_usuario' => $data['id_usuario'],
            'nome' => $data['nome'],
            'email' => $data['email'],
            'tipo_usuario' => $data['tipo_usuario'],
            'telefone' => $data['telefone'],
            'nome_img' => $data['nome_img'], // envia nome da imagem
            'senha' => '', // campo vazio por segurança
            'cpf_cnpj' => $data['tipo_usuario'] == 'PF' ? $data['cpf_cliente'] : $data['cnpj_cliente'],
            'extra' => $data['tipo_usuario'] == 'PF' ? $data['data_nascimento'] : $data['razao_social']
        ]);
    }
    exit;
}

echo json_encode(['error' => 'Ação inválida']);
$conn->close();
?>
