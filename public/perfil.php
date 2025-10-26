<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Buscar dados do usuário
$sql = "SELECT nome, email, telefone, tipo_usuario, nome_img FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Buscar dados do cliente (PF ou PJ)
$cliente_fisico = [];
$cliente_juridico = [];

// Buscar dados do cliente (PF ou PJ)
$cliente_fisico = [];
$cliente_juridico = [];

if ($usuario['tipo_usuario'] == 'PF') {
    // --- Pessoa Física ---
    $sql_pf = "SELECT id_cliente_fisico, cpf, data_nascimento 
               FROM cliente_fisico 
               WHERE id_usuario = ?";
    
    $stmt_pf = $conn->prepare($sql_pf);

    if (!$stmt_pf) {
        die("Erro ao preparar statement (PF): " . $conn->error);
    }

    $stmt_pf->bind_param("i", $id_usuario);
    $stmt_pf->execute();
    $cliente_fisico = $stmt_pf->get_result()->fetch_assoc();
    $stmt_pf->close();

} elseif ($usuario['tipo_usuario'] == 'PJ') {
    // --- Pessoa Jurídica ---
    $sql_pj = "SELECT id_cliente_juridico, cnpj, razao_social 
               FROM cliente_juridico 
               WHERE id_usuario = ?";
    
    $stmt_pj = $conn->prepare($sql_pj);

    if (!$stmt_pj) {
        die("Erro ao preparar statement (PJ): " . $conn->error);
    }

    $stmt_pj->bind_param("i", $id_usuario);
    $stmt_pj->execute();

    $result_pj = $stmt_pj->get_result();

    if ($result_pj && $result_pj->num_rows > 0) {
        $cliente_juridico = $result_pj->fetch_assoc();

        echo "<pre>";
        print_r($cliente_juridico);
        echo "</pre>";
    } else {
    }

    $stmt_pj->close();

} else {
    echo "Tipo de usuário não reconhecido.";
}



// Histórico de pedidos
$sql_pedidos = "SELECT id_pedido, codigo, data_pedido, total_pedido, status FROM pedido WHERE id_usuario = ? ORDER BY data_pedido DESC";
$stmt_pedidos = $conn->prepare($sql_pedidos);
$stmt_pedidos->bind_param("i", $id_usuario);
$stmt_pedidos->execute();
$pedidos = $stmt_pedidos->get_result()->fetch_all(MYSQLI_ASSOC);

// Histórico de doações com impactos
$sql_doacoes = "
SELECT d.id_doacao, d.codigo, d.quantidade, d.tipo_residuo, d.data, d.status, 
       COALESCE(SUM(i.qtd_fertilizante_gerado),0) AS total_fertilizante,
       GROUP_CONCAT(i.descricao_impacto SEPARATOR '; ') AS descricoes_impacto,
       COALESCE(SUM(i.medida_impacto),0) AS medida_impacto_total
FROM doacao d
LEFT JOIN impacto i ON i.id_doacao = d.id_doacao
WHERE d.id_usuario = ?
GROUP BY d.id_doacao, d.codigo, d.quantidade, d.tipo_residuo, d.data, d.status
ORDER BY d.data DESC";

$stmt_doacoes = $conn->prepare($sql_doacoes);
$stmt_doacoes->bind_param("i", $id_usuario);
$stmt_doacoes->execute();
$doacoes = $stmt_doacoes->get_result()->fetch_all(MYSQLI_ASSOC);

// Impacto total de todas as doações do usuário
$sql_impacto_total = "
SELECT COALESCE(SUM(i.qtd_fertilizante_gerado),0) AS impacto_total
FROM doacao d
LEFT JOIN impacto i ON i.id_doacao = d.id_doacao
WHERE d.id_usuario = ?";
$stmt_total = $conn->prepare($sql_impacto_total);
$stmt_total->bind_param("i", $id_usuario);
$stmt_total->execute();
$impacto_total = $stmt_total->get_result()->fetch_assoc()['impacto_total'];

// Agora $impacto_total contém a soma de todos os impactos do usuário
?>

  <!DOCTYPE html>
  <html lang="pt-BR">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil do Usuário - EcoRaiz</title>
    <?php include '../elements/head.php'; ?>

  <link rel="stylesheet" href="../css/perfil.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/style.css">


  </head>
  <body>
    <?php include '../elements/navbar.php'; ?>
  



  <!-- Contêiner de alertas Bootstrap -->
  <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

  <div class="profile-container">
  <h2 class="mb-4 text-center">Perfil do Usuário</h2>

  <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab">Dados Pessoais</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="historico-tab" data-bs-toggle="tab" data-bs-target="#historico" type="button" role="tab">Histórico de Compras</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="doacoes-tab" data-bs-toggle="tab" data-bs-target="#doacoes" type="button" role="tab">Histórico de Doações</button>
  </li>

   <li class="nav-item" role="presentation">
    <button class="nav-link" id="alertas-tab" data-bs-toggle="tab" data-bs-target="#alertas" type="button" role="tab">Alertas Personalizados</button>
  </li>
  </ul>

  <div class="tab-content" id="profileTabsContent">

  <!-- Dados Pessoais -->
  <div class="tab-pane fade show active" id="dados" role="tabpanel">
    <div class="profile-header text-center mb-4 position-relative">
      <img src="../ecoraiz-adm/img/Usuarios/<?= $usuario['nome_img'] ?: 'default-profile.jpg' ?>" alt="Foto de perfil" class="profile-photo rounded-circle border border-3 border-success" id="profilePhoto">
      <button class="btn btn-success position-absolute" style="bottom:0; right:50%; transform:translateX(50%);" id="editPhotoBtn">
        <i class="bi bi-pencil-fill"></i>
      </button>
      <input type="file" id="photoInput" accept="image/*" style="display:none;">
    </div>

    <div class="d-flex flex-wrap gap-3 mb-4">
      <div class="info-card">
        <label>Nome Completo</label>
        <div class="text-field"><?= $usuario['nome'] ?></div>
        <input type="text" name="nome" value="<?= $usuario['nome'] ?>">
      </div>

      <?php if($usuario['tipo_usuario'] == 'PF'): ?>
        <div class="info-card">
          <label>CPF</label>
          <div class="text-field"><?= $cliente_fisico['cpf'] ?? '' ?></div>
          <input type="text" name="cpf" value="<?= $cliente_fisico['cpf'] ?? '' ?>">
        </div>
        <div class="info-card">
          <label>Data de Nascimento</label>
          <div class="text-field"><?= $cliente_fisico['data_nascimento'] ?? '' ?></div>
          <input type="date" name="data_nascimento" value="<?= $cliente_fisico['data_nascimento'] ?? '' ?>">
        </div>
      <?php elseif($usuario['tipo_usuario'] == 'PJ'): ?>
        <div class="info-card">
          <label>CNPJ</label>
          <div class="text-field"><?= $cliente_juridico['cnpj'] ?? '-' ?></div>
          <input type="text" name="cnpj" value="<?= $cliente_juridico['cnpj'] ?? '' ?>">
        </div>
        <div class="info-card">
          <label>Razão Social</label>
          <div class="text-field"><?= $cliente_juridico['razao_social'] ?? '' ?></div>
          <input type="text" name="razao_social" value="<?= $cliente_juridico['razao_social'] ?? '' ?>">
        </div>
      <?php endif; ?>

      <div class="info-card">
        <label>E-mail</label>
        <div class="text-field"><?= $usuario['email'] ?></div>
        <input type="email" name="email" value="<?= $usuario['email'] ?>">
      </div>
      <div class="info-card">
        <label>Telefone</label>
        <div class="text-field"><?= $usuario['telefone'] ?></div>
        <input type="tel" name="telefone" value="<?= $usuario['telefone'] ?>">
      </div>
    </div>

    <div class="text-end mb-4">
      <button class="btn-edit btn btn-success" id="editBtn"><i class="bi bi-pen"></i> Editar perfil</button>
    </div>
<!-- Informações de Compostagem -->
<div class="compost-card">
  <h5>Informações das doações</h5>
  <div class="compost-row">
    <?php
      // Filtrar apenas doações recicladas
      $doacoes_recicladas = array_filter($doacoes, function($d) {
          return $d['status'] === 'Reciclada';
      });
    ?>

    <div class="compost-item">
      <strong>Quantidade de resíduos compostados:</strong>
      <?= array_sum(array_column($doacoes_recicladas, 'quantidade')) ?> kg
    </div>
    <div class="compost-item">
      <strong>Impacto ambiental estimado:</strong>
      <?= array_sum(array_column($doacoes_recicladas, 'total_fertilizante')) * 0.33 ?> kg de CO<sub>2</sub> evitados
    </div>
    <div class="compost-item">
      <strong>Tipos de resíduos compostados:</strong>
      <?php
        $tipos = array_column($doacoes_recicladas, 'tipo_residuo');
        echo $tipos ? implode(', ', array_unique($tipos)) : '-';
      ?>
    </div>
    <div class="compost-item">
      <strong>Fertilizante produzido:</strong>
      <?= array_sum(array_column($doacoes_recicladas, 'total_fertilizante')) ?> kg de composto orgânico
    </div>
    <div class="compost-item">
      <strong>Última atualização:</strong>
      <?= !empty($doacoes_recicladas) ? date('d/m/Y', strtotime(reset($doacoes_recicladas)['data'])) : '-' ?>
    </div>
    <div class="compost-item">
  <strong>Impacto:</strong>
  <?= array_sum(array_column($doacoes_recicladas, 'medida_impacto_total')) ?> raizes
</div>
<button id="btnCertificado" class="btn btn-success">Gerar Certificado</button>

  </div>
</div>


  </div>


  
  <!-- Histórico de Compras -->
  <div class="tab-pane fade" id="historico" role="tabpanel">
    <div class="table-responsive">
      <table class="table table-striped text-center align-middle">
        <thead>
          <tr>
            <th>Pedido</th>
            <th>Data</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($pedidos as $pedido): ?>
            <tr>
              <td><a href="./acompanhamento.php?codigo=<?= $pedido['codigo'] ?>">#<?= $pedido['codigo'] ?></a></td>
              <td><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></td>
              <td>R$ <?= number_format($pedido['total_pedido'],2,',','.') ?></td>
              <td><?= $pedido['status'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="tab-pane fade" id="alertas" role="tabpanel">
<p>Em breve, alertas personalizados...</p>
  </div>
  <!-- Histórico de Doações -->
  <div class="tab-pane fade" id="doacoes" role="tabpanel">
    <div class="table-responsive">
      <table class="table table-striped text-center align-middle">
        <thead>
          <tr>
            <th>Doação</th>
            <th>Data</th>
            <th>Quantidade</th>
            <th>Fertilizante Gerado</th>
            <th>Impacto</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($doacoes as $doacao): ?>
            <tr>
              <td><a href="./acompanhamento_doacao.php?codigo=<?= $doacao['codigo'] ?>">#<?= $doacao['codigo'] ?></a></td>
              <td><?= date('d/m/Y', strtotime($doacao['data'])) ?></td>
              <td><?= $doacao['quantidade'] ?> kg</td>
              <td><?= $doacao['qtd_fertilizante_gerado'] ?? 0 ?> kg</td>
              <td><?= $doacao['descricao_impacto'] ?? '-' ?></td>
              <td><?= $doacao['status'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  </div>
  </div>

  <footer>
    <!-- Footer existente -->
  </footer>
<script>
document.getElementById('btnCertificado').addEventListener('click', () => {
    // Dados do usuário (pegar dinamicamente do PHP)
    const dados = {
        usuario: "<?= $usuario['nome'] ?>",
        impactoTotal: <?= array_sum(array_column($doacoes_recicladas, 'medida_impacto_total')) ?>,
        quantidadeResiduos: <?= array_sum(array_column($doacoes_recicladas, 'quantidade')) ?>,
        tiposResiduos: "<?= implode(', ', array_column($doacoes_recicladas, 'tipo_residuo')) ?>",
        fertilizanteProduzido: <?= array_sum(array_column($doacoes_recicladas, 'total_fertilizante')) ?>
    };

    fetch('http://localhost:4000/certificado', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dados)
    })
    .then(response => response.blob())
    .then(blob => {
        // Criar link temporário e abrir PDF
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'certificado.pdf'; // se quiser baixar diretamente
        a.target = '_blank'; // se quiser abrir em nova aba
        a.click();
        window.URL.revokeObjectURL(url);
    })
    .catch(err => console.error(err));
});


</script>

  <script src="../js/perfil.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
  </html>
