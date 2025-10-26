<?php
session_start();

// Conexão com o banco
include './db.php';

// Obtém o código da doação da URL
$codigo = $_GET['codigo'] ?? null;

if (!$codigo) {
    header("Location: perfil.php");
    exit;
}

// Consulta a doação
$sql = "SELECT * FROM doacao WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();
$doacao = $result->fetch_assoc();

if (!$doacao) {
    die("<h3 style='text-align:center; margin-top:50px;'>Doação não encontrada.</h3>");
}

// Define etapas concluídas
$status = $doacao['status'];
$etapa_cadastrada = in_array($status, ['Doação cadastrada','Aceita pela ecoraiz','Em Compostagem','Reciclada']);
$etapa_aceita      = in_array($status, ['Aceita pela ecoraiz','Em Compostagem','Reciclada']);
$etapa_compostagem = in_array($status, ['Em Compostagem','Reciclada']);
$etapa_reciclada   = in_array($status, ['Reciclada']);
$etapa_cancelada   = $status === 'Cancelada';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rastreamento da Doação - EcoRaiz</title>
  <?php include '../elements/head.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/css_acompanhamento.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>
  <?php include '../elements/navbar.php'; ?>

<div class="container mt-5 pt-5">
  <article class="card">
    <header class="card-header"> Minhas Doações / Rastrear Doação </header>
    <div class="card-body">
      <h6><strong>Código da Doação #:</strong> <?= htmlspecialchars($doacao['codigo']) ?></h6>
      <article class="card">
        <div class="card-header2 row">
          <div class="col"><strong>Data da Doação:</strong><br><?= date('d/m/Y', strtotime($doacao['data'])) ?></div>
          <div class="col"><strong>Endereço:</strong><br><?= htmlspecialchars($doacao['endereco_coleta']) ?></div>
          <div class="col"><strong>Status:</strong><br><?= htmlspecialchars($doacao['status']) ?></div>
          <div class="col"><strong>Data Coleta:</strong><br><?= $doacao['data_coleta'] ? date('d/m/Y', strtotime($doacao['data_coleta'])) : '-' ?></div>
        </div>
      </article>

<?php if (!$etapa_cancelada) { ?>
    <div class="track">
        <div class="step <?= $etapa_cadastrada ? 'active' : '' ?>">
            <span class="icon"><i class="bi bi-check-lg"></i></span>
            <span class="text">Doação Cadastrada</span>
        </div>
        <div class="step <?= $etapa_aceita ? 'active' : '' ?>">
            <span class="icon"><i class="bi bi-person-check"></i></span>
            <span class="text">Aceita pela EcoRaiz</span>
        </div>
        <div class="step <?= $etapa_compostagem ? 'active' : '' ?>">
            <span class="icon"><i class="bi bi-tree"></i></span>
            <span class="text">Em Compostagem</span>
        </div>
        <div class="step <?= $etapa_reciclada ? 'active' : '' ?>">
            <span class="icon"><i class="bi bi-box-seam"></i></span>
            <span class="text">Reciclada</span>
        </div>
    </div>
<?php } else { ?>
    <div class="track canceled">
        <div class="step">
            <span class="icon" style="background:#dc3545;"><i style="color:white;" class="bi bi-x-lg"></i></span>
            <span class="text" style="color:#dc3545;">Doação Cancelada</span>
        </div>
    </div>
<?php } ?>

      <hr>
      <p><strong>Quantidade:</strong> <?= number_format($doacao['quantidade'], 2, ',', '.') ?> kg</p>
      <p><strong>Tipo de Resíduo:</strong> <?= htmlspecialchars($doacao['tipo_residuo']) ?></p>
      <p><strong>Telefone:</strong> <?= htmlspecialchars($doacao['telefone']) ?></p>
      <p><strong>Região:</strong> <?= htmlspecialchars($doacao['regiao'] ?? '-') ?></p>

      <hr>
      <a href="perfil.php" class="btn btn-register"><i class="bi bi-arrow-left"></i> Voltar</a>
    </div>
  </article>
</div>

<footer class="text-center text-lg-start" style="background-color: #F2F7EC;">
  <div class="container p-4">
    <div class="row">
      <div class="col-md-3 mx-auto mt-3">
        <h1 class="text-uppercase mb-4 font-weight-bold">Ecoraiz <img src="../img/logo.png" alt="" width="50px"></h1>
        <p>Na EcoRaiz, oferecemos produtos naturais e sustentáveis, cuidando do meio ambiente e do seu bem-estar.</p>
      </div>
      <div class="col-md-2 mx-auto mt-3">
        <h6 class="text-uppercase mb-4 font-weight-bold">Produtos</h6>
        <p>Fertilizantes</p><p>Equipamentos</p><p>Pecuária</p><p>Horta</p>
      </div>
      <div class="col-md-3 mx-auto mt-3">
        <h6 class="text-uppercase mb-4 font-weight-bold">Links Úteis</h6>
        <p>Sua conta</p><p>Doe agora</p><p>Compre na loja</p><p>Ajuda</p>
      </div>
      <div class="col-md-4 mx-auto mt-3">
        <h6 class="text-uppercase mb-4 font-weight-bold">Contato</h6>
        <p>Belo Horizonte - MG</p><p>ecoraiz@contato.com</p><p>+55 31 3234-5675</p>
      </div>
    </div>
    <hr class="my-3">
    <div class="row d-flex align-items-center">
      <div class="col-md-7 text-center text-md-start">
        <div class="p-3">© 2025 Copyright: <a href="#">www.ecoraiz.com</a></div>
      </div>
      <div class="col-md-5 text-center text-md-end">
        <a class="btn btn-outline-light btn-floating m-1"><i class="bi bi-facebook"></i></a>
        <a class="btn btn-outline-light btn-floating m-1"><i class="bi bi-instagram"></i></a>
      </div>
    </div>
  </div>
</footer>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
