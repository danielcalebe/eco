<?php
session_start();

// Verifica se o administrador está logado
if (!isset($_SESSION['admin'])) {
    header("Location: login-adm.php");
    exit;
}

// Conexão com o banco
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecoraiz";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Buscar todos os pedidos com o nome do usuário
$sql = "
    SELECT 
        p.id_pedido,
        u.nome AS usuario,
        p.total_pedido,
        p.data_pedido,
        p.forma_pagamento,
        p.codigo,
        p.status,
        p.endereco_entrega AS endereco 
    FROM pedido p
    INNER JOIN usuario u ON p.id_usuario = u.id_usuario
    ORDER BY p.id_pedido DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Admin - Pedidos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="./css/styles-adm.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="menu-toggle" id="menu-toggle">
  <i class="bi bi-list"></i>
</div>

<div class="sidebar" id="sidebar">
    <div class="profile">
        <img src="./img/logo-white.png" alt="Admin">
    </div>
    <a href="painel-adm.php"><i class="bi bi-house-door"></i> Home</a>
    <a href="doacao-adm.php"><i class="bi bi-box2-heart"></i> Doações</a>
    <a href="pedidos-adm.php" class="active"><i class="bi bi-basket"></i> Pedidos</a>
    <a href="impactos-adm.php"><i class="bi bi-bar-chart-line"></i> Impactos</a>
    <a href="cliente-adm.php"><i class="bi bi-people"></i> Clientes</a>
    <a href="produtos-adm.php"><i class="bi bi-bag"></i> Produtos</a>
    <a href="funcionario-adm.php"><i class="bi bi-person"></i> Funcionário</a>
    <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
</div>

<div class="content ml-2 my-4">
    <h3 class="fw-bold mb-4">Pedidos</h3>
    <div class="d-flex justify-content-end gap-4 mb-3">
        <div class="mb-3" style="max-width:400px;">
            <div class="input-group border border-2">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar pedido por ID, usuário ou status">
            </div>
        </div>
        <button class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#addPedidoModal">
            <i class="bi bi-plus-lg"></i> Adicionar Pedido
        </button>
    </div>

    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th class="d-flex gap-3" id="idHeader" style="cursor:pointer">ID <i class="bi bi-arrow-down-up"></i></th>
                <th>Usuário</th>
                <th>Total Pedido (R$)</th>
                <th>Data Pedido</th>
                <th>Forma Pagamento</th>
                <th>Status</th>
                <th>Código</th>
                    <th>Endereço</th>

                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="pedidosTableBody">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='row-{$row['id_pedido']}'>
                        <td>{$row['id_pedido']}</td>
                        <td>{$row['usuario']}</td>
                        <td>".number_format($row['total_pedido'], 2, ',', '.')."</td>
                        <td>{$row['data_pedido']}</td>
                        <td>{$row['forma_pagamento']}</td>

                        <td>{$row['status']}</td>
                        <td>{$row['codigo']}</td>
                                                                        <td>{$row['endereco']}</td>

                        <td>
                            <button class='btn btn-sm btn-warning editBtn' data-id='{$row['id_pedido']}'><i class='bi bi-pencil'></i></button>
                            <button class='btn btn-sm btn-danger deleteBtn' data-id='{$row['id_pedido']}'><i class='bi bi-trash'></i></button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Nenhum pedido encontrado</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal Adicionar / Editar Pedido -->
<div class="modal fade" id="addPedidoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="pedidoForm" method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Adicionar Pedido</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id_pedido" name="id_pedido">

            <div class="mb-3">
                <label for="id_usuario" class="form-label">Usuário (ID) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="id_usuario" id="id_usuario" placeholder="Informe o ID do usuário" required>
            </div>

            <div class="mb-3">
                <label for="total_pedido" class="form-label">Total Pedido (R$) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" name="total_pedido" id="total_pedido" placeholder="Ex: 150.50" required>
            </div>

            <div class="mb-3">
                <label for="forma_pagamento" class="form-label">Forma de Pagamento <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="forma_pagamento" id="forma_pagamento" placeholder="Ex: Cartão, Dinheiro" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" name="status" id="status" required>
                    <option value="Recebido pela empresa">Recebido pela empresa</option>
                    <option value="Enviado">Enviado</option>
                    <option value="Pedido a caminho">Pedido a caminho</option>
                    <option value="Entregue">Entregue</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="endereco_entrega" class="form-label">Endereço <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="endereco_entrega" id="endereco_entrega" placeholder="Rua ABc, 98 " required>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-dark" id="saveBtn">Salvar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
$('#pedidoForm').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: 'pedido-actions.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            location.reload();
        }
    });
});



    // Editar pedido
    // Editar pedido
// Editar pedido
// Delegação de eventos para botões que podem ser adicionados dinamicamente
$(document).on('click', '.editBtn', function() {
    let id = $(this).data('id');
    $.ajax({
        url: 'pedido-actions.php',
        type: 'GET',
        data: { action: 'get', id_pedido: id },
        dataType: 'json',
        success: function(data) {
            $('#id_pedido').val(data.id_pedido);
            $('#id_usuario').val(data.id_usuario);
            $('#total_pedido').val(data.total_pedido);
            $('#forma_pagamento').val(data.forma_pagamento);
            $('#status').val(data.status);
            $('#endereco_entrega').val(data.endereco_entrega);

            // Atualiza título
            $('#modalTitle').text('Editar Pedido');

            // Abre modal usando Bootstrap 5
            var modalEl = document.getElementById('addPedidoModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        },
        error: function(err) {
            console.log('Erro ao buscar pedido:', err);
        }
    });
});



    // Deletar pedido
    $('.deleteBtn').click(function() {
        if (confirm('Tem certeza que deseja apagar este pedido?')) {
            let id = $(this).data('id');
            $.ajax({
                url: 'pedido-actions.php',
                type: 'POST',
                data: { action: 'delete', id_pedido: id },
                success: function() {
                    $('#row-' + id).remove();
                }
            });
        }
    });

    // Pesquisa em tempo real
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#pedidosTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Ordenar tabela pelo ID
    let asc = true;
    $('#idHeader').click(function() {
        let rows = $('#pedidosTableBody tr').get();
        rows.sort(function(a, b) {
            let A = parseInt($(a).children('td').eq(0).text());
            let B = parseInt($(b).children('td').eq(0).text());
            return asc ? A - B : B - A;
        });
        $.each(rows, function(index, row) {
            $('#pedidosTableBody').append(row);
        });
        asc = !asc;
    });

    // Sidebar toggle
    const toggleButton = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
});
</script>
</body>
</html>

<?php $conn->close(); ?>
