<?php 
session_start();

 if (!isset($_SESSION['admin'])) {
     header("Location: login-adm.php");
     exit;
 }

 ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Admin - Pedidos</title>

  <!-- Bootstrap CSS -->
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
    <h3 class="fw-bold mb-4">Pedidos</h3>

    <div class="table-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h6 class="fw-bold mb-0">Pedidos</h6>
          <small class="text-muted">As informações sobre pedidos aparecerão aqui</small>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-danger btn-sm" id="deleteSelectedBtn">
            <i class="bi bi-trash"></i> Apagar
          </button>
          <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#addPedidoModal">
            <i class="bi bi-plus-lg"></i> Adicionar Pedido
          </button>
        </div>
      </div>

      <div class="mb-3">
        <div class="input-group">
          <span class="input-group-text" id="search-addon"><i class="bi bi-search"></i></span>
          <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar pedido por ID, doador ou status..." aria-label="Pesquisar" aria-describedby="search-addon">
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th id="idHeader" style="cursor:pointer">ID <i class="bi bi-arrow-down-up"></i></th>
              <th>Id Doador</th>
              <th>Quantidade total</th>
              <th>Fertilizante gerado</th>
              <th>Impacto</th>
              <th>Observações</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="pedidosTableBody">
            <!-- Exemplo de dados (futuro: puxar do banco via PHP/MySQL) -->
            <tr>
              <td>1</td>
              <td>5</td>
              <td>10kg</td>
              <td>Composto orgânico</td>
              <td>Redução CO₂</td>
              <td>Entregar amanhã</td>
              <td class="text-end">
                <button class="btn btn-sm btn-editar"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-excluir"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Adicionar Pedido -->
  <div class="modal fade" id="addPedidoModal" tabindex="-1" aria-labelledby="addPedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addPedidoModalLabel">Adicionar Pedido</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <form id="addPedidoForm">
            <div class="mb-3">
              <label class="form-label">Id Doador</label>
              <input type="number" id="addIdDoador" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Quantidade total</label>
              <input type="text" id="addQuantidadeTotal" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Fertilizante gerado</label>
              <input type="text" id="addFertilizante" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Impacto</label>
              <input type="text" id="addImpacto" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Observações</label>
              <input type="text" id="addObservacoes" class="form-control">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-dark" id="addPedidoBtn">Adicionar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Confirmação Excluir -->
  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Confirmar Exclusão</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <p>Deseja excluir esta(s) linha(s)?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Sim</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./scripts/pedidos-adm.js"></script>
</body>

</html>
