<?php
session_start();

// Conexão com o banco
$conn = new mysqli("localhost", "root", "", "ecoraiz");
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// Obtém o código do pedido da URL
$codigo = $_GET['codigo'] ?? null;

if (!$codigo) {
header("Location: perfil.php");
}

// Consulta o pedido
$sql = "SELECT * FROM pedido WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();
$pedido = $result->fetch_assoc();

if (!$pedido) {
  die("<h3 style='text-align:center; margin-top:50px;'>Pedido não encontrado.</h3>");
}

// Consulta os itens do pedido (pegando apenas a primeira imagem)
$sql_itens = "
  SELECT 
    p.nome_produto, 
    p.preco, 
    i.qtd, 
    (SELECT imgp.caminho_imagem FROM imagem_produto imgp WHERE imgp.id_produto = p.id_produto LIMIT 1) AS imagem
  FROM item_pedido i
  INNER JOIN produto p ON p.id_produto = i.id_produto
  WHERE i.id_pedido = ?
";
$stmt_itens = $conn->prepare($sql_itens);
$stmt_itens->bind_param("i", $pedido['id_pedido']);
$stmt_itens->execute();
$itens = $stmt_itens->get_result()->fetch_all(MYSQLI_ASSOC);

// Define etapas concluídas
$status = $pedido['status']; // Valor exato do ENUM
$etapa_confirmado = in_array($status, ['Recebido pela empresa', 'Enviado', 'Pedido a caminho', 'Entregue']);
$etapa_enviado     = in_array($status, ['Enviado', 'Pedido a caminho', 'Entregue']);
$etapa_caminho     = in_array($status, ['Pedido a caminho', 'Entregue']);
$etapa_entregue    = in_array($status, ['Entregue']);
$etapa_cancelado   = $status === 'Cancelado';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acompanhamento - EcoRaiz</title>
    <?php include '../elements/head.php'; ?>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/css_acompanhamento.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>



  <?php include '../elements/navbar.php'; ?>

<div class="container mt-5 pt-5">
  <article class="card">
    <header class="card-header"> Meus Pedidos / Rastrear Pedido </header>
    <div class="card-body">
      <h6><strong>Código da compra #:</strong> <?= htmlspecialchars($pedido['codigo']) ?></h6>
      <article class="card">
        <div class="card-header2 row ">
          <div class="col"><strong>Data do Pedido:</strong><br><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></div>
          <div class="col"><strong>Vendido por:</strong><br>EcoRaiz | +55 31 3234 5675</div>
          <div class="col"><strong>Status:</strong><br><?= htmlspecialchars($pedido['status']) ?></div>
          <div class="col"><strong>Data Entrega:</strong><br><?= date('d/m/Y', strtotime($pedido['data_entrega'] ?? '+5 dias')) ?></div>
        </div>
      </article>

<?php if ($pedido['status'] != 'Cancelado') { ?>
    <!-- Etapas do pedido normais -->
    <div class="track">
        <div class="step <?= $etapa_confirmado ? 'active' : '' ?>">
            <span class="icon"><i class="bi bi-check-lg"></i></span>
            <span class="text">Pedido Confirmado</span>
        </div>
        <div class="step <?= $etapa_enviado ? 'active' : '' ?>">
            <span class="icon"><i class="bi bi-send"></i></span>
            <span class="text">Enviado</span>
        </div>
        <div class="step <?= $etapa_caminho ? 'active' : '' ?>">
            <span class="icon"><i class="bi bi-truck"></i></span>
            <span class="text">A caminho</span>
        </div>
        <div class="step <?= $etapa_entregue ? 'active' : '' ?>">
            <span class="icon"><i class="bi bi-box-seam"></i></span>
            <span class="text">Entregue</span>
        </div>
    </div>
<?php } else { ?>
    <!-- Pedido cancelado -->
    <div class="track canceled">
        <div class="step">
            <span class="icon" style="background:#dc3545;"><i style="color: white;" class="bi bi-x-lg"></i></span>
            <span class="text" style="color:#dc3545;">Pedido Cancelado</span>
        </div>
    </div>
<?php } ?>



      <hr>

      <!-- Produtos -->
      <ul class="row">
        <?php foreach ($itens as $item): ?>
        <li class="col-md-4">
          <figure class="itemside mb-3">
            <div class="aside">
              <img 
                src="../ecoraiz-adm/img/Produtos/<?= htmlspecialchars($item['imagem'] ?? 'sem-imagem.png') ?>" 
                class="img-sm2 border" 
                alt="<?= htmlspecialchars($item['nome_produto']) ?>"
              >
            </div>
            <figcaption class="info align-self-center">
              <p class="title"><?= htmlspecialchars($item['nome_produto']) ?></p>
              <span class="text-muted">R$ <?= number_format($item['preco'], 2, ',', '.') ?></span>
              <p class="text-muted">Quantidade: <?= $item['qtd'] ?></p>
            </figcaption>
          </figure>
        </li>
        <?php endforeach; ?>
      </ul>

      <hr>
      <a href="perfil.php" class="btn btn-register" data-abc="true">
        <i class="bi bi-arrow-left"></i> Voltar
      </a>
    </div>
  </article>
</div>
<!-- Footer -->
  <footer class="text-center text-lg-start" style="background-color: #F2F7EC;">
    <div class="container p-4">
      <section class="">
        <div class="row">
          <div class="col-md-3 mx-auto mt-3">
            <h1 class="text-uppercase mb-4 font-weight-bold">
              Ecoraiz
              <img src="../img/logo.png" alt="" width="50px">
            </h1>
            <p>Na EcoRaiz, oferecemos produtos naturais e sustentáveis, cuidando do meio ambiente e do seu bem-estar.</p>
          </div>

          <div class="col-md-2 mx-auto mt-3">
            <h6 class="text-uppercase mb-4 font-weight-bold">Produtos</h6>
            <p>Fertilizantes</p>
            <p>Equipamentos</p>
            <p>Pecuária</p>
            <p>Horta</p>
          </div>

          <div class="col-md-3 mx-auto mt-3">
            <h6 class="text-uppercase mb-4 font-weight-bold">Links Úteis</h6>
            <p>Sua conta</p>
            <p>Doe agora</p>
            <p>Compre na loja</p>
            <p>Ajuda</p>
          </div>

          <div class="col-md-4 mx-auto mt-3">
            <h6 class="text-uppercase mb-4 font-weight-bold">Contato</h6>
            <p>Belo Horizonte - MG</p>
            <p>ecoraiz@contato.com</p>
            <p>+55 31 3234-5675</p>
          </div>
        </div>
      </section>
      <hr class="my-3">
      <section class="p-3 pt-0">
        <div class="row d-flex align-items-center">
          <div class="col-md-7 text-center text-md-start">
            <div class="p-3">© 2025 Copyright: <a href="#">www.ecoraiz.com</a></div>
          </div>
          <div class="col-md-5 text-center text-md-end">
            <a class="btn btn-outline-light btn-floating m-1"><i class="bi bi-facebook"></i></a>
            <a class="btn btn-outline-light btn-floating m-1"><i class="bi bi-instagram"></i></a>
          </div>
        </div>
      </section>
    </div>
  </footer>

</body>
</html>
