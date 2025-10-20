<?php
session_start();

// Configurações de conexão
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecoraiz";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se os campos foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senhaDigitada = $_POST['senha'] ?? '';

    if (empty($email) || empty($senhaDigitada)) {
        header("Location: login-adm.php?erro=1");
        exit();
    }

    // Consulta o funcionário pelo e-mail
    $stmt = $conn->prepare("SELECT * FROM funcionario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Verifica senha
    if ($usuario && password_verify($senhaDigitada, $usuario['senha'])) {


        echo("Oi");
        // Login bem-sucedido → define sessão de admin
        $_SESSION['admin'] = [
            'id' => $usuario['id_funcionario'],
            'cpf' => $usuario['cpf'],
            'cargo' => $usuario['cargo'],
            'email' => $usuario['email']
        ];

        header("Location: painel-adm.php");
        exit();
    } else {
        // Falha no login
        header("Location: login-adm.php?erro=1");
        exit();
    }
} else {
    // Acesso direto não permitido
    header("Location: login-adm.php");
    exit();
}
?>
