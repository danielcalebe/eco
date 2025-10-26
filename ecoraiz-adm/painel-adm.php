<?php
session_start();
include './db.php'; // Conexão com o banco

if (!isset($_SESSION['admin'])) {
    header("Location: login-adm.php");
    exit;
}

// Funções para pegar dados do banco
function getTotalVendas($conn)
{
    $sql = "SELECT SUM(total_pedido) AS total_vendas FROM pedido";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    return $row['total_vendas'] ?? 0;
}

function getTotalDoacoes($conn)
{
    $sql = "SELECT SUM(quantidade) AS total_doacoes FROM doacao";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    return $row['total_doacoes'] ?? 0;
}

function getTotalProducao($conn)
{
    $sql = "SELECT SUM(qtd_fertilizante_gerado) AS total_producao FROM impacto";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    return $row['total_producao'] ?? 0;
}

function getTotalClientes($conn)
{
    $sql = "SELECT COUNT(*) AS total_clientes FROM usuario";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    return $row['total_clientes'] ?? 0;
}

function getTotalProdutos($conn)
{
    $sql = "SELECT COUNT(*) AS total_produtos FROM produto";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    return $row['total_produtos'] ?? 0;
}

// Gráficos filtrados por ano
$anoFiltro = $_GET['ano'] ?? date('Y');

function getVendasMensais($conn, $ano)
{
    $sql = "SELECT MONTH(data_pedido) AS mes, SUM(total_pedido) AS total 
            FROM pedido 
            WHERE YEAR(data_pedido) = ? 
            GROUP BY MONTH(data_pedido)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ano);
    $stmt->execute();
    $res = $stmt->get_result();
    $vendas = array_fill(1, 12, 0);
    while ($row = $res->fetch_assoc()) {
        $vendas[intval($row['mes'])] = floatval($row['total']);
    }
    return $vendas;
}

function getProducaoMensal($conn, $ano)
{
    $sql = "SELECT MONTH(data) AS mes, SUM(qtd_fertilizante_gerado) AS total 
            FROM impacto 
            WHERE YEAR(data) = ? 
            GROUP BY MONTH(data)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ano);
    $stmt->execute();
    $res = $stmt->get_result();
    $producao = array_fill(1, 12, 0);
    while ($row = $res->fetch_assoc()) {
        $producao[intval($row['mes'])] = floatval($row['total']);
    }
    return $producao;
}

function getDoacoesMensais($conn, $ano)
{
    $sql = "SELECT MONTH(data) AS mes, SUM(quantidade) AS total 
            FROM doacao 
            WHERE YEAR(data) = ? 
            GROUP BY MONTH(data)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ano);
    $stmt->execute();
    $res = $stmt->get_result();
    $doacoes = array_fill(1, 12, 0);
    while ($row = $res->fetch_assoc()) {
        $doacoes[intval($row['mes'])] = floatval($row['total']);
    }
    return $doacoes;
}

function getClientesMensais($conn, $ano)
{
    $sql = "SELECT MONTH(data_cadastro) AS mes, COUNT(*) AS total 
            FROM usuario 
            WHERE YEAR(data_cadastro) = ? 
            GROUP BY MONTH(data_cadastro)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ano);
    $stmt->execute();
    $res = $stmt->get_result();
    $clientes = array_fill(1, 12, 0);
    while ($row = $res->fetch_assoc()) {
        $clientes[intval($row['mes'])] = intval($row['total']);
    }
    return $clientes;
}

// Dados dos cards
$totalVendas = getTotalVendas($conn);
$totalDoacoes = getTotalDoacoes($conn);
$totalProducao = getTotalProducao($conn);
$totalClientes = getTotalClientes($conn);
$totalProdutos = getTotalProdutos($conn);

// Dados dos gráficos
$vendasMensais = getVendasMensais($conn, $anoFiltro);
$producaoMensal = getProducaoMensal($conn, $anoFiltro);
$doacoesMensais = getDoacoesMensais($conn, $anoFiltro);
$clientesMensais = getClientesMensais($conn, $anoFiltro);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Impactos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="./css/styles-adm.css">
    <style>
        /* Sidebar */


        /* Content */
        .content {
            margin-left: 210px;
            padding: 20px;
        }

        /* Cards */
        .card-info {
            padding: 15px;
            border-radius: 12px;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: 0.3s;
            font-size: 0.9rem;
        }

        .card-info:hover {
            transform: translateY(-5px);
        }

        .bg-vendas {
            background: linear-gradient(135deg, #28a745, #218838);
        }

        .bg-doacoes {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .bg-producao {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .bg-clientes {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .bg-produtos {
            background: linear-gradient(135deg, #6f42c1, #5936a2);
        }

        /* Gráficos */
        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .filters {
            margin-bottom: 20px;
        }

        .row-cards {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .row-cards .col-card {
            flex: 1 1 calc(16.66% - 10px);
        }
        /* Centralizar cards e diminuir tamanho */
.card-info {
    flex: 0 0 150px; /* largura fixa menor */
    padding: 15px;
    border-radius: 12px;
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: 0.3s;
    font-size: 0.85rem;
}

.card-info i {
    font-size: 2rem;
}

.d-flex.justify-content-center {
    margin: 0 auto;
}
.chart-container-sm {
    background: #fff;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
    max-width: 350px; /* Largura máxima do container */
    width: 100%;      /* Responsivo */
    text-align: center;
}
    </style>


</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile"><img src="./img/logo-white.png" alt="Admin"></div>
        <a href="painel-adm.php" class="active"><i class="bi bi-house-door"></i> Home</a>
        <a href="doacao-adm.php"><i class="bi bi-box2-heart"></i> Doações</a>
        <a href="pedidos-adm.php"><i class="bi bi-basket"></i> Pedidos</a>
        <a href="impactos-adm.php"><i class="bi bi-bar-chart-line"></i> Impactos</a>
        <a href="cliente-adm.php"><i class="bi bi-people"></i> Clientes</a>
        <a href="produtos-adm.php"><i class="bi bi-bag"></i> Produtos</a>
        <a href="funcionario-adm.php"><i class="bi bi-person-badge"></i> Funcionário</a>
        <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>

    <div class="content " style="margin-left: 240px;">
        <h3 class="mb-4 ">Painel Admin - Ano <?= $anoFiltro ?></h3>
       


        <!-- Filtro por ano -->
         <div class="d-flex gap-4">
<a href="./api-rotas/rota_coletas.php" class="mb-2 btn btn-warning text-dark  align-items-center gap-2 shadow-sm">
    <i class="bi bi-map fs-5 "></i>
    <span>ROTAS DE HOJE</span>
</a>
        <form method="GET" class="filters row g-2 align-items-center">
            <div class="col-auto"><label for="ano" class="col-form-label">Filtrar por Ano:</label></div>
            <div class="col-auto">
                <select name="ano" id="ano" class="form-select">
                    <?php for ($i = date('Y'); $i >= 2020; $i--): ?>
                        <option value="<?= $i ?>" <?= ($i == $anoFiltro ? 'selected' : '') ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-primary">Filtrar</button></div>

            
        </form>

         </div>

        <!-- 6 Cards -->
        <div class="row-cards mb-4">
            <div class="col-card">
                <div class="card-info bg-vendas"><i class="bi bi-graph-up-arrow fs-3"></i>
                    <h6 class="mt-1">R$ <?= number_format($totalVendas, 2, ',', '.') ?></h6><small>Vendas</small>
                </div>
            </div>
            <div class="col-card">
                <div class="card-info bg-doacoes"><i class="bi bi-heart-fill fs-3"></i>
                    <h6 class="mt-1"><?= number_format($totalDoacoes, 2, ',', '.') ?> KG</h6><small>Doações</small>
                </div>
            </div>
            <div class="col-card">
                <div class="card-info bg-producao"><i class="bi bi-box-seam fs-3"></i>
                    <h6 class="mt-1"><?= number_format($totalProducao, 2, ',', '.') ?> KG</h6><small>Produção</small>
                </div>
            </div>
            <div class="col-card">
                <div class="card-info bg-clientes"><i class="bi bi-people-fill fs-3"></i>
                    <h6 class="mt-1"><?= $totalClientes ?></h6><small>Clientes</small>
                </div>
            </div>
            <div class="col-card">
                <div class="card-info bg-produtos"><i class="bi bi-bag-fill fs-3"></i>
                    <h6 class="mt-1"><?= $totalProdutos ?></h6><small>Produtos</small>
                </div>
            </div>
            <div class="col-card">
                <div class="card-info bg-vendas"><i class="bi bi-calendar fs-3"></i>
                    <h6 class="mt-1"><?= $anoFiltro ?></h6><small>Ano</small>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
<div class="row justify-content-center">
    <div class="col-auto">
        <div class="chart-container-sm">
            <h6 class="fw-bold text-center">Faturamento Mensal</h6>
            <canvas id="vendasChart"></canvas>
        </div>
    </div>
    <div class="col-auto">
        <div class="chart-container-sm">
            <h6 class="fw-bold text-center">Produção Mensal</h6>
            <canvas id="producaoChart"></canvas>
        </div>
    </div>
    <div class="col-auto">
        <div class="chart-container-sm">
            <h6 class="fw-bold text-center">Doações Mensais</h6>
            <canvas id="doacoesChart"></canvas>
        </div>
    </div>
    <div class="col-auto">
        <div class="chart-container-sm">
            <h6 class="fw-bold text-center">Novos Clientes Mensais</h6>
            <canvas id="clientesChart"></canvas>
        </div>
    </div>
</div>


    <script>
        const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        const vendasMensais = <?= json_encode(array_values($vendasMensais)) ?>;
        const producaoMensal = <?= json_encode(array_values($producaoMensal)) ?>;
        const doacoesMensais = <?= json_encode(array_values($doacoesMensais)) ?>;
        const clientesMensais = <?= json_encode(array_values($clientesMensais)) ?>;

        function criarChart(id, tipo, label, dados, cor, fill = false) {
            return new Chart(document.getElementById(id), {
                type: tipo,
                data: {
                    labels: meses,
                    datasets: [{
                        label: label,
                        data: dados,
                        borderColor: cor,
                        backgroundColor: fill ? cor.replace('1)', '0.2)') : cor,
                        fill: fill,
                        tension: 0.3,
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        criarChart('vendasChart', 'line', 'Vendas', vendasMensais, '#28a745', true);
        criarChart('producaoChart', 'bar', 'Produção', producaoMensal, 'rgba(0,123,255,0.6)');
        criarChart('doacoesChart', 'line', 'Doações', doacoesMensais, '#dc3545', true);
        criarChart('clientesChart', 'line', 'Clientes', clientesMensais, '#ffc107', true);
    </script>
</body>

</html>