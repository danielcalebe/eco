<?php
session_start();
include 'db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// ======================
// 🔹 Consulta principal na tabela usuario
// ======================
$sql = "SELECT nome, email, telefone, tipo_usuario FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Usuário não encontrado.");
}

$usuario = $result->fetch_assoc();

// ======================
// 🔹 Busca dados adicionais conforme tipo
// ======================
if ($usuario['tipo_usuario'] === 'fisico') {
    $sqlFisico = "SELECT cpf, data_nascimento FROM cliente_fisico WHERE id_usuario = ?";
    $stmtFisico = $conn->prepare($sqlFisico);
    $stmtFisico->bind_param("i", $id_usuario);
    $stmtFisico->execute();
    $resFisico = $stmtFisico->get_result();
    $dadosExtra = $resFisico->fetch_assoc();
    if ($dadosExtra) $usuario = array_merge($usuario, $dadosExtra);
} elseif ($usuario['tipo_usuario'] === 'juridico') {
    $sqlJuridico = "SELECT cnpj, razao_social FROM cliente_juridico WHERE id_usuario = ?";
    $stmtJuridico = $conn->prepare($sqlJuridico);
    $stmtJuridico->bind_param("i", $id_usuario);
    $stmtJuridico->execute();
    $resJuridico = $stmtJuridico->get_result();
    $dadosExtra = $resJuridico->fetch_assoc();
    if ($dadosExtra) $usuario = array_merge($usuario, $dadosExtra);
}

// ======================
// 🔹 Histórico de doações do usuário
// ======================
$sqlDoacoes = "
    SELECT 
        id_doacao,
        codigo,
        quantidade,
        tipo_residuo,
        tipo_coleta,
        status,
        status_aprofundado,
        data,
        data_coleta,
        endereco_coleta,
        horario_coleta
    FROM doacao
    WHERE id_usuario = ?
    ORDER BY data DESC
";
$stmtDoacoes = $conn->prepare($sqlDoacoes);
$stmtDoacoes->bind_param("i", $id_usuario);
$stmtDoacoes->execute();
$resDoacoes = $stmtDoacoes->get_result();

$doacoes = [];
while ($row = $resDoacoes->fetch_assoc()) {
    $doacoes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil do Usuário - EcoRaiz</title>
  <link rel="stylesheet" href="../css/perfil.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>

<!-- 🔹 Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: #f3f8f1;">
  <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
    <button class="navbar-toggler order-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <a class="navbar-brand mx-3" href="./Landing_Page/index.php">
      <img src="../img/logo.png" alt="Logo EcoRaiz" width="40">
    </a>

    <div class="collapse navbar-collapse justify-content-center order-1" id="navbarNav">
      <ul class="navbar-nav mb-2 mb-lg-0 d-flex gap-5">
        <li class="nav-item"><a class="nav-link" href="./Landing_Page/index.php"><i class="bi bi-house-door me-1"></i> Início</a></li>
        <li class="nav-item"><a class="nav-link" href="./catalogoprodutos.php"><i class="bi bi-shop me-1"></i> Loja</a></li>
        <li class="nav-item"><a class="nav-link" href="./doacoes.php"><i class="bi bi-recycle me-1"></i> Doações</a></li>
        <li class="nav-item"><a class="nav-link" href="./Landing_Page/index.php#sobre"><i class="bi bi-info-circle me-1"></i> Institucional</a></li>
        <li class="nav-item"><a class="nav-link" href="./Landing_Page/index.php#contato"><i class="bi bi-envelope me-1"></i> Contato</a></li>
      </ul>
    </div>

    <div class="d-flex gap-3 align-items-center order-2">
      <a href="doacao.php" class="btn btn-success px-3 rounded-pill"><i class="bi bi-heart-fill me-2"></i> Doar agora</a>
      <a href="perfil.php"><i class="bi bi-person-circle fs-2 mb-1" style="color:#1E5E2E;"></i></a>
    </div>
  </div>
</nav>

<!-- 🔹 Conteúdo principal -->
<div class="profile-container container py-5">
  <h2 class="mb-4 text-center">Perfil do Usuário</h2>

  <!-- Abas -->
  <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados">Dados Pessoais</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#historico">Histórico de Doações</button></li>
  </ul>

  <div class="tab-content">
    <!-- 🔸 Dados Pessoais -->
    <div class="tab-pane fade show active" id="dados">
      <div class="profile-header text-center mb-4 position-relative">
        <img src="../img/default-profile.jpg" class="profile-photo rounded-circle border border-3 border-success" id="profilePhoto">
      </div>

      <div class="d-flex flex-wrap gap-3 mb-4">
        <div class="info-card"><label>Nome Completo</label><div class="text-field"><?= htmlspecialchars($usuario['nome']) ?></div></div>

        <?php if ($usuario['tipo_usuario'] === 'fisico' && isset($usuario['cpf'])): ?>
          <div class="info-card"><label>CPF</label><div class="text-field"><?= htmlspecialchars($usuario['cpf']) ?></div></div>
          <div class="info-card"><label>Data de Nascimento</label><div class="text-field"><?= date('d/m/Y', strtotime($usuario['data_nascimento'])) ?></div></div>
        <?php elseif ($usuario['tipo_usuario'] === 'juridico' && isset($usuario['cnpj'])): ?>
          <div class="info-card"><label>CNPJ</label><div class="text-field"><?= htmlspecialchars($usuario['cnpj']) ?></div></div>
          <div class="info-card"><label>Razão Social</label><div class="text-field"><?= htmlspecialchars($usuario['razao_social']) ?></div></div>
        <?php endif; ?>

        <div class="info-card"><label>E-mail</label><div class="text-field"><?= htmlspecialchars($usuario['email']) ?></div></div>
        <div class="info-card"><label>Telefone</label><div class="text-field"><?= htmlspecialchars($usuario['telefone']) ?></div></div>
      </div>
    </div>

    <!-- 🔸 Histórico de Doações -->
    <div class="tab-pane fade" id="historico">
      <div class="table-responsive">
        <table class="table table-striped text-center align-middle">
          <thead>
            <tr>
              <th>Código</th>
              <th>Quantidade</th>
              <th>Tipo de Resíduo</th>
              <th>Tipo de Coleta</th>
              <th>Data Solicitação</th>
              <th>Data Coleta</th>
              <th>Status</th>
              <th>Detalhe</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($doacoes) > 0): ?>
              <?php foreach($doacoes as $d): ?>
                <tr>
                  <td><?= htmlspecialchars($d['codigo']) ?></td>
                  <td><?= htmlspecialchars($d['quantidade']) ?></td>
                  <td><?= htmlspecialchars($d['tipo_residuo']) ?></td>
                  <td><?= htmlspecialchars($d['tipo_coleta']) ?></td>
                  <td><?= htmlspecialchars($d['data']) ?></td>
                  <td><?= htmlspecialchars($d['data_coleta']) ?></td>
                  <td><?= htmlspecialchars($d['status']) ?></td>
                  <td><?= htmlspecialchars($d['status_aprofundado'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="text-center">Nenhuma doação registrada.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- 🔹 Footer -->
<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
