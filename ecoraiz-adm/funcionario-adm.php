<?php
session_start();
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
if ($conn->connect_error) die("Falha na conexão: " . $conn->connect_error);

// Buscar todos os funcionários
$sql = "SELECT id_funcionario, nome_funcionario AS nome, email, telefone, cargo,cpf, status FROM funcionario ORDER BY id_funcionario DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Admin - Funcionários</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="./css/styles-adm.css">
</head>
<body>
<div class="menu-toggle" id="menu-toggle"><i class="bi bi-list"></i></div>

<div class="sidebar" id="sidebar">
    <div class="profile"><img src="./img/logo-white.png" alt="Admin"></div>
    <a href="painel-adm.php"><i class="bi bi-house-door"></i> Home</a>
    <a href="doacao-adm.php"><i class="bi bi-box2-heart"></i> Doações</a>
    <a href="pedidos-adm.php"><i class="bi bi-basket"></i> Pedidos</a>
    <a href="impactos-adm.php"><i class="bi bi-bar-chart-line"></i> Impactos</a>
    <a href="cliente-adm.php"><i class="bi bi-people"></i> Clientes</a>
    <a href="produtos-adm.php"><i class="bi bi-bag"></i> Produtos</a>
    <a href="funcionario-adm.php" class="active"><i class="bi bi-person"></i> Funcionário</a>
    <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
</div>

<div class="content ml-2 my-4">
    <h3 class="fw-bold mb-4">Funcionários</h3>
    <div class="d-flex justify-content-end gap-4 mb-3">
        <div class="mb-3" style="max-width:400px;">
            <div class="input-group border border-2">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar por ID, nome, email ou cargo">
            </div>
        </div>
        <button class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#modalFuncionario">
            <i class="bi bi-plus-lg"></i> Adicionar Funcionário
        </button>
    </div>

<table class="table table-bordered align-middle text-center">
    <thead class="table-light">
        <tr>
            <th class="d-flex gap-3" id="idHeader" style="cursor:pointer">ID <i class="bi bi-arrow-down-up"></i></th>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>CPF</th> <!-- nova coluna -->
            <th>Cargo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody id="funcionariosTableBody">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr id='row-{$row['id_funcionario']}'>
                    <td>{$row['id_funcionario']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['telefone']}</td>
                    <td>{$row['cpf']}</td> <!-- CPF -->
                    <td>{$row['cargo']}</td>
                    <td>{$row['status']}</td>
                    <td>
                        <button class='btn btn-sm btn-warning editBtn' data-id='{$row['id_funcionario']}'><i class='bi bi-pencil'></i></button>
                        <button class='btn btn-sm btn-danger deleteBtn' data-id='{$row['id_funcionario']}'><i class='bi bi-trash'></i></button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>Nenhum funcionário encontrado</td></tr>";
        }
        ?>
    </tbody>
</table>

</div>

<!-- Modal -->
<div class="modal fade" id="modalFuncionario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="funcionarioForm">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id_funcionario" id="id_funcionario">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Adicionar Funcionário</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nome" id="nome" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="senha" id="senha">
            </div>
            <div class="mb-3">
                <label class="form-label">Telefone</label>
                <input type="text" class="form-control" name="telefone" id="telefone">
            </div>
                   <div class="mb-3">
                <label class="form-label">CPF</label>
                <input type="text" class="form-control" name="cpf" id="cpf">
            </div>
            <div class="mb-3">
                <label class="form-label">Cargo</label>
                <input type="text" class="form-control" name="cargo" id="cargo">
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" id="status">
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-dark">Salvar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {

    // Salvar / Editar
    $('#funcionarioForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'funcionario-actions.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp) {
                if(resp.error) alert(resp.error);
                else location.reload();
            },
            error: function(xhr) {
                alert("Erro AJAX: " + xhr.responseText);
            }
        });
    });

    // Editar
    $(document).on('click', '.editBtn', function() {
        let id = $(this).data('id');
        $.getJSON('funcionario-actions.php', {action:'get_funcionario', id_funcionario:id}, function(data) {
            $('#id_funcionario').val(data.id_funcionario);
            $('#nome').val(data.nome);
            $('#email').val(data.email);
            $('#senha').val('');
            $('#telefone').val(data.telefone);
                        $('#cpf').val(data.cpf);

            $('#cargo').val(data.cargo);
            $('#status').val(data.status);
            $('#modalTitle').text('Editar Funcionário');
            new bootstrap.Modal(document.getElementById('modalFuncionario')).show();
        });
    });

    // Deletar
    $(document).on('click', '.deleteBtn', function() {
        if(confirm('Deseja realmente deletar?')) {
            let id = $(this).data('id');
            $.post('funcionario-actions.php', {action:'delete', id_funcionario:id}, function(resp){
                if(resp.error) alert(resp.error);
                else $('#row-' + id).remove();
            }, 'json');
        }
    });

    // Pesquisa
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#funcionariosTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Ordenar ID
    let asc=true;
    $('#idHeader').click(function() {
        let rows = $('#funcionariosTableBody tr').get();
        rows.sort(function(a,b){
            let A = parseInt($(a).children('td').eq(0).text());
            let B = parseInt($(b).children('td').eq(0).text());
            return asc ? A-B : B-A;
        });
        $.each(rows, function(i,row){ $('#funcionariosTableBody').append(row); });
        asc = !asc;
    });

    // Sidebar toggle
    $('#menu-toggle').click(function(){ $('#sidebar').toggleClass('active'); });

});
</script>

</body>
</html>
<?php $conn->close(); ?>
