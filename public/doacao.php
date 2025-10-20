<?php
include __DIR__ . '//db.php'; // Conexão com banco (corrigido caminho)

// Verifica se a conexão foi estabelecida
if (!isset($conn) || !$conn) {
    die("Erro: conexão com o banco de dados não estabelecida.");
}
// Inicializa mensagens de alerta
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipoEntrega = $_POST['tipoEntrega'];
    $regiaoBH = isset($_POST['regiaoBH']) ? $_POST['regiaoBH'] : null;
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $tipoResiduo = $_POST['tipoResiduo'];
    $quantidade = $_POST['quantidade'];
    $dataDisponibilidade = $_POST['dataDisponibilidade'];

    $stmt = $conn->prepare("INSERT INTO doacoes 
        (tipo_entrega, regiao, endereco, telefone, tipo_residuo, quantidade, data_disponibilidade) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $tipoEntrega, $regiaoBH, $endereco, $telefone, $tipoResiduo, $quantidade, $dataDisponibilidade);

    if ($stmt->execute()) {
        $mensagem = "<div class='alert alert-success'>Doação registrada com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao registrar a doação: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doação - EcoRaiz</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/doacao.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg" style="background-color: #f3f8f1;">
    <!-- navbar aqui (igual ao seu código HTML) -->
  </nav>

  <section class="background-radial-gradient overflow-hidden">
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
      <div class="row gx-lg-5 mt-5 mb-5">

        <!-- Texto lateral -->
        <div class="col-lg-6  mb-lg-0">
          <h1 class="my-5 display-5 fw-bold ls-tight text-light-eco">
            Doe e fortaleça a <br />
            <span style="color: #78ac4d">sustentabilidade</span>
          </h1>
          <p class="mb-4 opacity-75 text-light-eco">
            Registre aqui sua doação de resíduos orgânicos e ajude a transformar o que seria lixo em adubo natural.
          </p>
        </div>

        <!-- FORMULÁRIO -->
        <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
          <div class="card bg-glass shadow-lg py-2">
            <div class="card-body px-4 py-5 px-md-5">

              <div class="text-center mb-4">
                <img src="../img/logo.png" alt="Logo EcoRaiz" width="120">
              </div>

              <!-- Alerta PHP -->
              <?php echo $mensagem; ?>

              <form method="POST" action="doacao.php">
                <!-- Tipo de entrega -->
                <div class="mb-3">
                  <label for="tipoEntrega" class="form-label">Tipo de entrega</label>
                  <select id="tipoEntrega" name="tipoEntrega" class="form-select" required>
                    <option value="" selected disabled>Selecione o tipo de entrega...</option>
                    <option value="coleta">Por coleta</option>
                    <option value="entrega">Entrega</option>
                    <option value="ponto_encontro">Ponto de encontro</option>
                  </select>
                </div>

                <!-- Região de BH -->
                <div class="mb-3 d-none" id="campoRegiao">
                  <label for="regiaoBH" class="form-label">Região de Belo Horizonte</label>
                  <select id="regiaoBH" name="regiaoBH" class="form-select">
                    <option value="" selected disabled>Selecione sua região...</option>
                    <option value="centro-sul">Centro-Sul</option>
                    <option value="leste">Leste</option>
                    <option value="oeste">Oeste</option>
                    <option value="norte">Norte</option>
                    <option value="nordeste">Nordeste</option>
                    <option value="noroeste">Noroeste</option>
                    <option value="pampulha">Pampulha</option>
                    <option value="venda-nova">Venda Nova</option>
                    <option value="barreiro">Barreiro</option>
                  </select>
                </div>

                <!-- Endereço -->
                <div class="mb-3">
                  <label for="endereco" class="form-label">Endereço</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" id="endereco" name="endereco" class="form-control" placeholder="Rua, número, bairro, cidade" required>
                  </div>
                </div>

                <!-- Telefone -->
                <div class="mb-3">
                  <label for="telefone" class="form-label">Telefone</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input type="tel" id="telefone" name="telefone" class="form-control" placeholder="(31) 99999-9999" required>
                  </div>
                </div>

                <!-- Tipo de resíduo -->
                <div class="mb-3">
                  <label for="tipoResiduo" class="form-label">Tipo de resíduo</label>
                  <select id="tipoResiduo" name="tipoResiduo" class="form-select" required>
                    <option value="" selected disabled>Selecione...</option>
                    <option value="restos_alimentos">Restos de alimentos</option>
                    <option value="cascas_frutas">Cascas de frutas</option>
                    <option value="folhas_podas">Folhas e podas</option>
                    <option value="borra_cafe">Borra de café</option>
                    <option value="outros">Outros</option>
                  </select>
                </div>

                <!-- Quantidade -->
                <div class="mb-3">
                  <label for="quantidade" class="form-label">Quantidade (kg)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                    <input type="number" id="quantidade" name="quantidade" min="1" class="form-control" placeholder="Informe a quantidade aproximada" required>
                  </div>
                </div>

                <!-- Data -->
                <div class="mb-4">
                  <label for="dataDisponibilidade" class="form-label">Data de disponibilidade</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                    <input type="date" id="dataDisponibilidade" name="dataDisponibilidade" class="form-control" required>
                  </div>
                </div>

                <!-- Botão -->
                <div class="text-end">
                  <button type="submit" class="btn btn-register w-20"><i class="bi bi-gift"></i> Registrar Doação</button>
                </div>
              </form>

            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Mostrar campo de região apenas se tipo de entrega for "coleta"
    const tipoEntrega = document.getElementById('tipoEntrega');
    const campoRegiao = document.getElementById('campoRegiao');

    tipoEntrega.addEventListener('change', () => {
      campoRegiao.classList.toggle('d-none', tipoEntrega.value !== 'coleta');
    });
  </script>

</body>
</html>
