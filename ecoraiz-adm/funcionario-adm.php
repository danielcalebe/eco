<?php
session_start();
// Verificar login do admin (opcional)
 if (!isset($_SESSION['admin'])) {
     header("Location: login-adm.php");
     exit;
 }

include_once("db.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Admin - Funcionários</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles-adm.css">
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
    <h3 class="fw-bold mb-4">Funcionários</h3>

    <div class="table-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="fw-bold mb-0">Funcionários</h6>
          <small class="text-muted">As informações sobre funcionários aparecerão aqui</small>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-danger btn-sm" id="deleteSelectedBtn">
            <i class="bi bi-trash"></i> Apagar
          </button>
          <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#addFuncionarioModal">
            <i class="bi bi-plus-lg"></i> Adicionar Funcionário
          </button>
        </div>
      </div>

      <!-- Busca -->
      <div class="mb-3">
        <div class="input-group">
          <span class="input-group-text" id="search-addon"><i class="bi bi-search"></i></span>
          <input type="text" id="searchInput" class="form-control"
            placeholder="Pesquisar funcionário por CPF, cargo ou email..."
            aria-label="Pesquisar" aria-describedby="search-addon">
        </div>
      </div>

      <!-- Tabela -->
      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>CPF</th>
              <th>Cargo</th>
              <th>Email</th>
              <th>Senha</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="funcionariosTableBody">
            <!-- Linhas carregadas via JS/AJAX -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Adicionar Funcionário -->
  <div class="modal fade" id="addFuncionarioModal" tabindex="-1" aria-labelledby="addFuncionarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addFuncionarioModalLabel">Adicionar Funcionário</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <form id="addFuncionarioForm">
            <div class="mb-3">
              <label class="form-label">CPF</label>
              <input type="text" id="addCpf" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Cargo</label>
              <input type="text" id="addCargo" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" id="addEmail" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Senha</label>
              <input type="password" id="addSenha" class="form-control">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-dark" id="addFuncionarioBtn">Adicionar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./scripts/funcionario.js"></script>
</body>
</html>
