<?php
// doacao-adm.php
session_start();

// (Opcional) Verifica se o administrador está logado
 if (!isset($_SESSION['admin'])) {
     header("Location: login-adm.php");
     exit;
 }


// (Opcional) Aqui você pode incluir o arquivo de conexão com o banco de dados
// include_once("conexao.php");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Admin - Doações</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles-adm.css">
</head>

<body>
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

  <div class="content">
    <h3 class="fw-bold mb-4">Doações</h3>

    <div class="table-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="fw-bold mb-0">Doações</h6>
          <small class="text-muted">As informações sobre doações aparecerão aqui</small>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-danger btn-sm" id="deleteSelectedBtn">
            <i class="bi bi-trash"></i> Apagar
          </button>
          <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#addDoacaoModal">
            <i class="bi bi-plus-lg"></i> Adicionar doação
          </button>
        </div>
      </div>

      <div class="mb-3">
        <div class="input-group">
          <span class="input-group-text" id="search-addon"><i class="bi bi-search"></i></span>
          <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar produto por nome, categoria ou status..." aria-label="Pesquisar" aria-describedby="search-addon">
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th id="idHeader" style="cursor:pointer">ID <i class="bi bi-arrow-down-up"></i></th>
              <th>Doador</th>
              <th>Tipos de resíduo</th>
              <th>Quantidade</th>
              <th>Local de coleta</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="doacoesTableBody">
  <?php
  // Exemplo estático de doações — substitua futuramente por dados do banco de dados
  $doacoes = [
    ["id" => 1, "doador" => "Eliel", "tipo" => "Restos de frutas", "quantidade" => "5kg", "local" => "Rua ABC, N: 123", "status" => "Recebido"],
    ["id" => 7, "doador" => "Eliel", "tipo" => "Restos de frutas", "quantidade" => "5kg", "local" => "Rua ABC, N: 123", "status" => "Cancelado"]
  ];

  foreach ($doacoes as $doacao) {
    echo "<tr>
            <td>{$doacao['id']}</td>
            <td>{$doacao['doador']}</td>
            <td>{$doacao['tipo']}</td>
            <td>{$doacao['quantidade']}</td>
            <td>{$doacao['local']}</td>
            <td>{$doacao['status']}</td>
            <td>
              <button class='btn btn-sm btn-warning'><i class='bi bi-pencil'></i></button>
              <button class='btn btn-sm btn-danger'><i class='bi bi-trash'></i></button>
            </td>
          </tr>";
  }
  ?>
</tbody>
