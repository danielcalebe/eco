<?php
session_start();
include __DIR__ . '/db.php';

// Redireciona usuário não logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Verifica conexão
if (!isset($conn) || !$conn) {
    die("Erro: conexão com o banco de dados não estabelecida.");
}

$mensagem = "";
$tipo_alerta = "";

// Função para gerar código de doação
function gerarCodigoDoacao($tamanho = 9) {
    $prefixo = "DOACAO";
    $numeros = '';
    for ($i = 0; $i < $tamanho; $i++) {
        $numeros .= rand(0, 9);
    }
    return $prefixo . $numeros;
}

// Processa formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = gerarCodigoDoacao();
    $usuario_id = $_SESSION['id_usuario'];

    $tipoEntrega = $_POST['tipoEntrega'] ?? '';
    $regiaoBH = $_POST['regiaoBH'] ?? null;
    $endereco = $_POST['endereco'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $tipoResiduo = $_POST['tipoResiduo'] ?? '';
    $quantidade = intval($_POST['quantidade'] ?? 0);
    $dataDisponibilidade = $_POST['dataDisponibilidade'] ?? '';
    $horarioColeta = $_POST['horario_coleta'] ?? '';

    // Valida campos obrigatórios
    if (empty($tipoEntrega) || empty($endereco) || empty($telefone) || empty($tipoResiduo) || $quantidade <= 0 || empty($dataDisponibilidade) || empty($horarioColeta)) {
        $mensagem = "⚠️ Por favor, preencha todos os campos obrigatórios corretamente.";
        $tipo_alerta = "warning";
    } else {
        // Trata regiaoBH se estiver vazio
        $regiaoBH = !empty($regiaoBH) ? $regiaoBH : null;

        $stmt = $conn->prepare("INSERT INTO doacao 
            (id_usuario, tipo_coleta, regiao, endereco_coleta, telefone, tipo_residuo, quantidade, data_coleta, horario_coleta, codigo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            $mensagem = "❌ Erro na preparação da query: " . htmlspecialchars($conn->error);
            $tipo_alerta = "danger";
        } else {
            // Tipos corrigidos: i = inteiro, s = string
            $stmt->bind_param(
                "isssssisss",
                $usuario_id,
                $tipoEntrega,
                $regiaoBH,
                $endereco,
                $telefone,
                $tipoResiduo,
                $quantidade,
                $dataDisponibilidade,
                $horarioColeta,
                $codigo
            );

            if ($stmt->execute()) {
                $mensagem = "✅ Sua doação foi registrada com sucesso! Código: <a href='acompanhamento_doacao.php?codigo=$codigo'><strong>$codigo</strong></a>";
                $tipo_alerta = "success";
            } else {
                $mensagem = "❌ Erro ao registrar a doação: " . htmlspecialchars($stmt->error);
                $tipo_alerta = "danger";
            }

            $stmt->close();
        }
    }
}

$conn->close();
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
    <?php include '../elements/navbar.php'; ?>

    <section class="background-radial-gradient overflow-hidden">
        <div class="container px-4 py-5 text-center text-lg-start my-5">
            <div class="row gx-lg-5 mt-5 mb-5">

                <!-- Texto lateral -->
                <div class="col-lg-6 mb-lg-0">
                    <h1 class="my-5 display-5 fw-bold ls-tight text-light-eco">
                        Doe e fortaleça a <br>
                        <span style="color: #78ac4d">sustentabilidade</span>
                    </h1>
                    <p class="mb-4 opacity-75 text-light-eco">
                        Registre aqui sua doação de resíduos orgânicos e ajude a transformar o que seria lixo em adubo natural.
                        Faça parte da mudança verde com a <strong>EcoRaiz</strong>!<br>
                        Lembre-se:<br>
                        - As opções de coleta e ponto de encontro estão disponíveis apenas para doações de 15kg ou mais.<br>
                        - O registro de doação não garante a coleta ou ponto de encontro. Fique atento ao seu telefone e email.
                    </p>
                </div>

                <!-- Formulário -->
                <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                    <div class="card bg-glass shadow-lg py-2">
                        <div class="card-body px-4 py-5 px-md-5">

                            <div class="text-center mb-4">
                                <img src="../img/logo.png" alt="Logo EcoRaiz" width="120">
                            </div>

                            <!-- Alert -->
                            <div id="alertContainer">
                                <?php if (!empty($mensagem)) : ?>
                                    <div class="alert alert-<?= $tipo_alerta ?> alert-dismissible fade show" role="alert">
                                        <?= $mensagem ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <form id="formDoacao" method="POST" action="doacao.php">
                                <!-- Tipo de entrega -->
                                <div class="mb-3">
                                    <label for="tipoEntrega" class="form-label">Tipo de entrega</label>
                                    <select id="tipoEntrega" name="tipoEntrega" class="form-select" required>
                                        <option value="" selected disabled>Selecione o tipo de entrega...</option>
                                        <option value="coleta">Por coleta – entregarei no dia da coleta</option>
                                        <option value="entrega">Entrega – levarei o resíduo até a empresa</option>
                                        <option value="ponto_encontro">Ponto de encontro – combinarei com a equipe</option>
                                    </select>
                                </div>

                                <!-- Região BH -->
                                <div class="mb-3 d-none" id="campoRegiao">
                                    <label for="regiaoBH" class="form-label">Região de Belo Horizonte</label>
                                    <select id="regiaoBH" name="regiaoBH" class="form-select">
                                        <option value="" selected disabled>Selecione sua região...</option>
                                        <option value="Centro Sul">Centro-Sul</option>
                                        <option value="Leste">Leste</option>
                                        <option value="Oeste">Oeste</option>
                                        <option value="Norte">Norte</option>
                                        <option value="Nordeste">Nordeste</option>
                                        <option value="Noroeste">Noroeste</option>
                                        <option value="Pampulha">Pampulha</option>
                                        <option value="Venda Nova">Venda Nova</option>
                                        <option value="Barreiro">Barreiro</option>
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
                                        <option value="Restos de alimentos">Restos de alimentos</option>
                                        <option value="Cascas de frutas">Cascas de frutas</option>
                                        <option value="Folhas de poda">Folhas e podas</option>
                                        <option value="Borra café">Borra de café</option>
                                        <option value="Outros">Outros</option>
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

                                <!-- Horário -->
                                <div class="mb-4">
                                    <label for="horario_coleta" class="form-label">Horário de disponibilidade</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                        <input type="time" id="horario_coleta" name="horario_coleta" class="form-control" required>
                                    </div>
                                </div>

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
        const tipoEntrega = document.getElementById("tipoEntrega");
        const enderecoInput = document.getElementById("endereco");
        const campoRegiao = document.getElementById("campoRegiao");
        const regiaoBH = document.getElementById("regiaoBH");
        const form = document.getElementById("formDoacao");
        const quantidadeInput = document.getElementById("quantidade");
        const alertContainer = document.getElementById("alertContainer");

        function showAlert(message, type = "info", timeout = 7000) {
            const alertDiv = document.createElement("div");
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alertDiv.role = "alert";
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            `;
            alertContainer.appendChild(alertDiv);
            setTimeout(() => alertDiv.classList.remove("show"), timeout - 500);
            setTimeout(() => alertDiv.remove(), timeout);
        }

        tipoEntrega.addEventListener("change", () => {
            const valor = tipoEntrega.value;
            enderecoInput.disabled = false;
            campoRegiao.classList.add("d-none");
            enderecoInput.value = "";
            enderecoInput.placeholder = "Rua, número, bairro, cidade";

            if (valor === "entrega") {
                enderecoInput.value = "Rua ABC 123, Belo Horizonte - MG";
                enderecoInput.disabled = true;
            } else if (valor === "ponto_encontro") {
                enderecoInput.placeholder = "Digite o local de encontro combinado";
            } else if (valor === "coleta") {
                campoRegiao.classList.remove("d-none");
                enderecoInput.disabled = false;
                enderecoInput.placeholder = "Selecione a região acima";
            }
        });

        regiaoBH.addEventListener("change", () => {
            const regiao = regiaoBH.value;
            let dia = "";
            switch (regiao) {
                case "Norte":
                case "Venda Nova": dia = "Segunda-feira"; break;
                case "Noroeste":
                case "Pampulha": dia = "Terça-feira"; break;
                case "Oeste":
                case "Barreiro": dia = "Quarta-feira"; break;
                case "Leste":
                case "Nordeste":dia = "Quinta-feira"; break;
                case "Centro Sul": dia = "Sexta-feira"; break;
            }
            if (dia) {
                scrollTo(0, 0);
                showAlert(`🗓️ A coleta para a região selecionada ocorre na <strong>${dia}</strong>.`, "info");
            }
        });

        form.addEventListener("submit", function(e) {
            const tipo = tipoEntrega.value;
            const quantidade = parseInt(quantidadeInput.value);
            if ((tipo === "coleta" || tipo === "ponto_encontro") && quantidade < 15) {
                e.preventDefault();
                scrollTo(0, 0);
                showAlert("⚠️ As opções <strong>Por coleta</strong> e <strong>Ponto de encontro</strong> estão disponíveis apenas para doações a partir de <strong>15 kg</strong>.", "warning");
                showAlert("⚠️ Tente alguma outra opção de doação ou junte um pouco mais de resíduos orgânicos!", "success");
            }
        });
    </script>
</body>
</html>
