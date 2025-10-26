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

// Buscar todos os impactos com nome do funcionário e doação
$sql = "
    SELECT 
        i.id_impacto,
        i.qtd_fertilizante_gerado,
        i.medida_impacto,
        i.descricao_impacto,
        i.data,
        f.nome_funcionario AS funcionario,
        d.id_doacao
    FROM impacto i
    LEFT JOIN funcionario f ON i.id_funcionario = f.id_funcionario
    LEFT JOIN doacao d ON i.id_doacao = d.id_doacao
    ORDER BY i.id_impacto DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Admin - Impactos</title>
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
    <a href="pedidos-adm.php"><i class="bi bi-basket"></i> Pedidos</a>
    <a href="impactos-adm.php"class="active" ><i class="bi bi-bar-chart-line"></i> Impactos</a>
    <a href="cliente-adm.php" ><i class="bi bi-people"></i> Clientes</a>
    <a href="produtos-adm.php"><i class="bi bi-bag"></i> Produtos</a>
    <a href="funcionario-adm.php"><i class="bi bi-person"></i> Funcionário</a>
    <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
</div>

<div class="content ml-2 my-4">
    <h3 class="fw-bold mb-4">Impactos</h3>
    <div class="d-flex justify-content-end gap-4 mb-3">
        <div class="mb-3" style="max-width:400px;">
            <div class="input-group border border-2">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar impacto por ID, funcionário ou doação">
            </div>
        </div>
        <button class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#impactoModal">
            <i class="bi bi-plus-lg"></i> Adicionar Impacto
        </button>
    </div>

    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Fertilizante Gerado</th>
                <th>Medida</th>
                <th>Descrição</th>
                <th>Data</th>
                <th>Funcionário</th>
                <th>ID Doação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="impactosTableBody">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='row-{$row['id_impacto']}'>
                        <td>{$row['id_impacto']}</td>
                        <td>{$row['qtd_fertilizante_gerado']}</td>
                        <td>{$row['medida_impacto']}</td>
                        <td>{$row['descricao_impacto']}</td>
                        <td>{$row['data']}</td>
                        <td>" . ($row['funcionario'] ?? '-') . "</td>
                        <td>{$row['id_doacao']}</td>
                        <td>
                            <button class='btn btn-sm btn-warning editBtn' data-id='{$row['id_impacto']}'><i class='bi bi-pencil'></i></button>
                            <button class='btn btn-sm btn-danger deleteBtn' data-id='{$row['id_impacto']}'><i class='bi bi-trash'></i></button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Nenhum impacto encontrado</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="impactoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="impactoForm">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Adicionar Impacto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_impacto" id="id_impacto">

          <div class="mb-3">
            <label class="form-label">Quantidade de Fertilizante Gerado</label>
            <input type="number" step="0.01" class="form-control" name="qtd_fertilizante_gerado" id="qtd_fertilizante_gerado" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Medida do Impacto</label>
            <input type="text" class="form-control" name="medida_impacto" id="medida_impacto" placeholder="Ex: kg, toneladas" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea class="form-control" name="descricao_impacto" id="descricao_impacto" rows="3" required></textarea>
          </div>

        <input type="hidden" name="data" id="data" value="<?php echo date('Y-m-d'); ?>">


          <div class="mb-3">
            <label class="form-label">ID Funcionário</label>
            <input type="number" class="form-control" name="id_funcionario" id="id_funcionario" required>
          </div>

          <div class="mb-3">
            <label class="form-label">ID Doação</label>
            <input type="number" class="form-control" name="id_doacao" id="id_doacao" required>
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
    // Salvar impacto
    $('#impactoForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'impacto-actions.php',
            type: 'POST',
            data: $(this).serialize() + '&action=save',
            success: function(response) {
                if(response.error){
                    alert(response.error);
                } else {
                    // Fecha modal
                    var modalEl = document.getElementById('impactoModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                    // Recarrega a página
                    location.reload();
                }
            }
        });
    });

    // Editar impacto
    $(document).on('click', '.editBtn', function() {
        let id = $(this).data('id');
        $.ajax({
            url: 'impacto-actions.php',
            type: 'GET',
            data: { action: 'get', id_impacto: id },
            dataType: 'json',
            success: function(data) {
                $('#id_impacto').val(data.id_impacto);
                $('#qtd_fertilizante_gerado').val(data.qtd_fertilizante_gerado);
                $('#medida_impacto').val(data.medida_impacto);
                $('#descricao_impacto').val(data.descricao_impacto);
                $('#id_funcionario').val(data.id_funcionario);
                $('#id_doacao').val(data.id_doacao);
                $('#modalTitle').text('Editar Impacto');
                var modalEl = document.getElementById('impactoModal');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            },
            error: function(err) {
                console.log('Erro ao buscar impacto:', err);
            }
        });
    });

    // Deletar impacto
    $(document).on('click', '.deleteBtn', function() {
        if (confirm('Tem certeza que deseja apagar este impacto?')) {
            let id = $(this).data('id');
            $.ajax({
                url: 'impacto-actions.php',
                type: 'POST',
                data: { action: 'delete', id_impacto: id },
                success: function(response) {
                    if(response.error){
                        alert(response.error);
                    } else {
                        // Recarrega a página após exclusão
                        location.reload();
                    }
                }
            });
        }
    });

    // Pesquisa em tempo real
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#impactosTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Ordenar tabela pelo ID
    let asc = true;
    $('#idHeader').click(function() {
        let rows = $('#impactosTableBody tr').get();
        rows.sort(function(a, b) {
            let A = parseInt($(a).children('td').eq(0).text());
            let B = parseInt($(b).children('td').eq(0).text());
            return asc ? A - B : B - A;
        });
        $.each(rows, function(index, row) {
            $('#impactosTableBody').append(row);
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
