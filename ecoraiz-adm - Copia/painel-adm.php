
<?php
session_start();

 if (!isset($_SESSION['admin'])) {
     header("Location: login-adm.php");
     exit;
 }

 ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Impactos</title>

    <!-- Bootstrap e Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/painel-adm.css">
    <!-- Chart.js -->
     <link rel="stylesheet" href="./css/styles-adm.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>

    </style>
</head>

<body>
    <!-- Sidebar -->
  <div class="sidebar">
    <div class="profile">
      <img src="./img/logo-white.png" alt="Admin">
    </div>
    <a href="painel-adm.php" class="active"><i class="bi bi-house-door"></i> Home</a>
    <a href="doacao-adm.php"><i class="bi bi-box2-heart"></i> Doações</a>
    <a href="pedidos-adm.php"><i class="bi bi-basket"></i> Pedidos</a>
    <a href="impactos-adm.php"><i class="bi bi-bar-chart-line"></i> Impactos</a>
    <a href="cliente-adm.php  " ><i class="bi bi-people"></i> Clientes</a>
    <a href="produtos-adm.php" ><i class="bi bi-bag"></i> Produtos</a>
        <a href="funcionario-adm.php" ><i class="bi bi-bag"></i> Funcionário</a>
            <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>

  </div>

    <!-- Conteúdo -->
    <div class="content">
        <h3 class="mb-4">Desempenho</h3>

        <!-- Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card-info">
                    <div class="card-icon text-success"><i class="bi bi-graph-up-arrow"></i></div>
                    <h5 class="fw-bold">250K</h5>
                    <p class="text-muted mb-0">Vendas</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-info">
                    <div class="card-icon text-danger"><i class="bi bi-heart-fill"></i></div>
                    <h5 class="fw-bold">1 Tonelada</h5>
                    <p class="text-muted mb-0">Doações</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-info">
                    <div class="card-icon text-primary"><i class="bi bi-box-seam"></i></div>
                    <h5 class="fw-bold">350K</h5>
                    <p class="text-muted mb-0">Produção</p>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h6 class="fw-bold text-secondary text-center">Vendas de Fertilizantes (Toneladas)</h6>
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h6 class="fw-bold text-secondary text-center">Produção Mensal (K)</h6>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Linha - Vendas
        const ctx1 = document.getElementById('lineChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Vendas',
                    data: [100, 150, 180, 200, 220, 210, 250, 270, 290, 310, 320, 340],
                    borderColor: '#007bff',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Gráfico de Barras - Produção
        const ctx2 = document.getElementById('barChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Produção',
                    data: [50, 70, 90, 100, 120, 150, 180, 200, 230, 250, 280, 300],
                    backgroundColor: 'rgba(0, 123, 255, 0.6)',
                    borderRadius: 6
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

</body>

</html>