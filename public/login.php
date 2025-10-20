<?php
session_start();
include 'db.php'; // Certifique-se de que o caminho está correto
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $mensagem = "<div class='alert alert-danger text-center'>Preencha todos os campos!</div>";
    } else {
        // Consulta usuário no banco
        $sql = "SELECT id_usuario, nome, senha, tipo_usuario FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();
            // Verifica senha
            if (password_verify($senha, $usuario['senha'])) {
                // Login bem-sucedido, inicia sessão
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
                header("Location: perfil.php"); // Redireciona para página principal
                exit;
            } else {
                $mensagem = "<div class='alert alert-danger text-center'>Senha incorreta!</div>";
            }
        } else {
            $mensagem = "<div class='alert alert-danger text-center'>Usuário não encontrado!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EcoRaiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
<nav class="navbar navbar-expand-lg" style="background-color: #f3f8f1;">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

        <!-- Botão responsivo na extrema esquerda -->
        <button class="navbar-toggler order-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Logo -->
        <a class="navbar-brand mx-3" href="./public/index.php">
            <img src="../img/logo.png" alt="Logo EcoRaiz" width="40">
        </a>

        <!-- Links do menu centralizados -->
        <div class="collapse navbar-collapse justify-content-center order-1" id="navbarNav">
            <ul class="navbar-nav mb-2 mb-lg-0 d-flex gap-5 ">
                <li class="nav-item"><a class="nav-link d-flex align-items-center" href="./Landing_Page/index.php"><i
                            class="bi bi-house-door me-1"></i> Início</a></li>
                <li class="nav-item"><a class="nav-link d-flex align-items-center" href="./catalogoprodutos.php"><i
                            class="bi bi-shop me-1"></i> Loja</a></li>
                <li class="nav-item"><a class="nav-link d-flex align-items-center" href="./doacoes.php"><i
                            class="bi bi-recycle me-1"></i> Doações</a></li>
                <li class="nav-item"><a class="nav-link d-flex align-items-center"
                        href="./Landing_Page/index.php#sobre"><i class="bi bi-info-circle me-1"></i> Institucional</a>
                </li>
                <li class="nav-item"><a class="nav-link d-flex align-items-center"
                        href="./Landing_Page/index.php#contato"><i class="bi bi-envelope me-1"></i> Contato</a></li>
            </ul>
        </div>

        <!-- Área de login/perfil à direita -->
        <div class="d-flex gap-3 align-items-center order-2">
            <a href="doacao.php" class="btn btn-success px-3 rounded-pill d-flex align-items-center">
                <i class="bi bi-heart-fill me-2"></i> Doar agora
            </a>
            <div class="d-flex flex-column align-items-center ms-2">
                <a href="perfil.php"> <i class="bi bi-person-circle fs-2 mb-1" style="color:#1E5E2E;"></i></a>
            </div>
        </div>

    </div>
</nav>

<section class="background-radial-gradient overflow-hidden">
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
        <div class="row gx-lg-5 align-items-center mb-5">
            <div class="col-lg-6 mb-6 mb-lg-0" style="z-index: 10">
                <h1 class="my-5 display-5 fw-bold ls-tight" style="color: #f7f5ec">
                    Bem-vindo de volta à
                    <br />
                    <span style="color: #78ac4d">Ecoraiz</span>
                </h1>
                <p class="mb-4 opacity-70" style="color: #f7f5ec">
                    Faça login para acessar sua conta e gerenciar suas doações, compras de fertilizantes naturais e informações de maneira segura.
                </p>
            </div>

            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div class="card bg-glass shadow-lg py-2">
                    <div class="card-body px-4 py-5 px-md-5">
                        <?= $mensagem ?>
                        <form method="POST" action="">
                            <div class="text-center mb-4">
                                <img src="../img/logo.png" alt="Logo EcoRaiz" width="120">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" placeholder="seu@email.com" name="email" required>
                            </div>
                            <div class="mb-3">
                                <div class="password-container">
                                    <input type="password" class="form-control" placeholder="********" name="senha" id="passwordInput" required>
                                    <i class="bi bi-eye" id="togglePassword"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="cadastro.php" class="text-decoration-none" style="color: #1E5E2E; font-weight: 500;">
                                    Ainda não possui conta?
                                </a>
                                <button type="submit" class="btn btn-register px-3">Entrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
const passwordInput = document.getElementById('passwordInput');
const togglePassword = document.getElementById('togglePassword');
togglePassword.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('bi-eye');
    this.classList.toggle('bi-eye-slash');
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
