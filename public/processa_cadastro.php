<?php
include 'db.php'; // Certifique-se de que db.php está na mesma pasta

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo = $_POST['tipoPessoa'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['endereco_email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // Verificação de campos obrigatórios
    if (empty($nome) || empty($email) || empty($telefone) || empty($senha)) {
        echo "<div class='alert alert-danger text-center'>Preencha todos os campos obrigatórios.</div>";
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se e-mail já existe
    $check = $conn->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
    if (!$check) die("Erro no prepare: " . $conn->error);

    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<div class='alert alert-danger text-center'>E-mail já cadastrado!</div>";
        exit;
    }

    // Cadastro Pessoa Física
    if ($tipo === 'pf') {
        $cpf = trim($_POST['cpf'] ?? '');
        $data_nascimento = $_POST['data_nascimento'] ?? '';

        $sqlUsuario = "INSERT INTO usuario (nome, email, senha, tipo_usuario, telefone)
                       VALUES (?, ?, ?, 'pf', ?)";
        $stmt = $conn->prepare($sqlUsuario);
        if (!$stmt) die("Erro no prepare: " . $conn->error);
        $stmt->bind_param("ssss", $nome, $email, $senhaHash, $telefone);

        if ($stmt->execute()) {
            $id_usuario = $stmt->insert_id;

            $sqlPF = "INSERT INTO cliente_fisico (id_usuario, cpf, data_nascimento)
                      VALUES (?, ?, ?)";
            $stmtPF = $conn->prepare($sqlPF);
            $stmtPF->bind_param("iss", $id_usuario, $cpf, $data_nascimento);
            $stmtPF->execute();

            echo "<div class='alert alert-success text-center'>Cadastro realizado com sucesso! Redirecionando...</div>";
            echo "<script>setTimeout(() => { window.location.href='login.php'; }, 3000);</script>";
        } else {
            echo "<div class='alert alert-danger text-center'>Erro ao cadastrar usuário: {$stmt->error}</div>";
        }

    // Cadastro Pessoa Jurídica
    } elseif ($tipo === 'pj') {
        $cnpj = trim($_POST['cnpj'] ?? '');
        $responsavel = trim($_POST['responsavel'] ?? '');
        $razao_social = $nome; // Razão social é o nome da empresa

        $sqlUsuario = "INSERT INTO usuario (nome, email, senha, tipo_usuario, telefone)
                       VALUES (?, ?, ?, 'pj', ?)";
        $stmt = $conn->prepare($sqlUsuario);
        if (!$stmt) die("Erro no prepare: " . $conn->error);
        $stmt->bind_param("ssss", $razao_social, $email, $senhaHash, $telefone);

        if ($stmt->execute()) {
            $id_usuario = $stmt->insert_id;

            $sqlPJ = "INSERT INTO cliente_juridico (id_usuario, cnpj, razao_social, nome_responsavel)
                      VALUES (?, ?, ?, ?)";
            $stmtPJ = $conn->prepare($sqlPJ);
            $stmtPJ->bind_param("isss", $id_usuario, $cnpj, $razao_social, $responsavel);
            $stmtPJ->execute();

            echo "";
            echo "<script>setTimeout(() => { window.location.href='login.php'; }, 3000);</script>";
        } else {
            echo "<div class='alert alert-danger text-center'>Erro ao cadastrar empresa: {$stmt->error}</div>";
        }

    } else {
        echo "<div class='alert alert-warning text-center'>Tipo de cadastro inválido.</div>";
    }
}
?>
