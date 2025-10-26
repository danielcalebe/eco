<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php'; // conexão com o banco

$id_usuario_navbar = $_SESSION['id_usuario'] ?? null;
$usuario_navbar = null;

if ($id_usuario_navbar) {
    $sql = "SELECT nome, nome_img FROM usuario WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario_navbar);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $usuario_navbar = $result->fetch_assoc();
    }
}
?>

<nav class="navbar navbar-expand-lg" style="background-color: #f3f8f1;">
  <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

    <!-- Botão responsivo -->
    <button class="navbar-toggler order-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Logo -->
    <a class="navbar-brand mx-3" href="index.php">
      <img src="../img/logo.png" alt="Logo EcoRaiz" width="40">
    </a>

    <!-- Links centrais -->
    <div class="collapse navbar-collapse justify-content-center order-1" id="navbarNav">
      <ul class="navbar-nav mb-2 mb-lg-0 d-flex gap-5">
        <li class="nav-item"><a class="nav-link d-flex align-items-center" href="index.php"><i class="bi bi-house-door me-1"></i> Início</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center" href="catalogoprodutos.php"><i class="bi bi-shop me-1"></i> Loja</a></li>  
        <li class="nav-item"><a class="nav-link d-flex align-items-center" href="doacoes.php"><i class="bi bi-recycle me-1"></i> Doações</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center" href="index.php#sobre"><i class="bi bi-info-circle me-1"></i> Institucional</a></li>
        <li class="nav-item"><a class="nav-link d-flex align-items-center" href="index.php#contato"><i class="bi bi-envelope me-1"></i> Contato</a></li>
      </ul>
    </div>

    <!-- Área direita -->
    <div class="d-flex gap-3 align-items-center order-2">
      <a href="doacao.php" class="btn btn-success px-3 rounded-pill d-flex align-items-center">
        <i class="bi bi-heart-fill me-2"></i> Doar agora
      </a>

      <?php if ($usuario_navbar): ?>
        <!-- Usuário logado -->
        <div class="dropdown">
          <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <?php if (!empty($usuario_navbar['img_perfil'])): ?>
              <img src="../uploads/<?php echo htmlspecialchars($usuario_navbar['img_perfil']); ?>" alt="Perfil" width="40" height="40" class="rounded-circle me-2">
            <?php else: ?>
              <i class="bi bi-person-circle fs-2 me-2" style="color:#1E5E2E;"></i>
            <?php endif; ?>
            <span style="color:#1E5E2E;"><?php echo htmlspecialchars($usuario_navbar['nome']); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="perfil.php">Meu Perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
          </ul>
        </div>
      <?php else: ?>
        <!-- Não logado -->
        <a href="login.php" class="btn btn-outline-success px-3 rounded-pill d-flex align-items-center">
          <i class="bi bi-person-fill me-2"></i> Entrar
        </a>
      <?php endif; ?>
    </div>

  </div>
</nav>


