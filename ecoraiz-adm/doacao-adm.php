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

// Buscar todas as doações
$sql = "
    SELECT 
        d.id_doacao AS id,
        u.nome AS doador,
        d.tipo_residuo AS tipo,
        d.quantidade AS quantidade,
        d.endereco_coleta AS local,
        d.regiao AS regiao,
        d.status AS status,
        d.data_coleta AS data_coleta
    FROM doacao d
    INNER JOIN usuario u ON d.id_usuario = u.id_usuario
    ORDER BY d.id_doacao DESC
";


$result = $conn->query($sql);
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
    <a href="doacao-adm.php" class="active"><i class="bi bi-box2-heart"></i> Doações</a>
    <a href="pedidos-adm.php"><i class="bi bi-basket"></i> Pedidos</a>
    <a href="impactos-adm.php"><i class="bi bi-bar-chart-line"></i> Impactos</a>
    <a href="cliente-adm.php"><i class="bi bi-people"></i> Clientes</a>
    <a href="produtos-adm.php"><i class="bi bi-bag"></i> Produtos</a>
    <a href="funcionario-adm.php"><i class="bi bi-person"></i> Funcionário</a>
    <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
</div>
  <div class="content    ml-2 my-4">
    <h3 class="fw-bold mb-4">Doações</h3>
    <div class="d-flex justify-content-end gap-4">

      <div class="mb-3" style="max-width:400px;">
        <div class="input-group border border-2">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar por doador, tipo ou status">
        </div>
      </div>
      <button class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#addDoacaoModal">
        <i class="bi bi-plus-lg"></i> Adicionar Doação
      </button>
    </div>


    <table class="table table-bordered align-middle text-center">
      <thead class="table-light">
        <tr>
          <th class="d-flex gap-3" id="idHeader" style="cursor:pointer">ID <i class="bi bi-arrow-down-up"></i></th>
          <th>Doador</th>
          <th>Tipo de Resíduo</th>
          <th>Quantidade</th>
          <th>Região</th>
          <th>Data de Coleta</th>

          <th>Local de Coleta</th>
          <th>Status</th>

          <th>Ações</th>
        </tr>
      </thead>
      <tbody id="doacoesTableBody">
        <?php
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr id='row-{$row['id']}'>
                            <td>{$row['id']}</td>
                            <td>{$row['doador']}</td>
                            <td>{$row['tipo']}</td>
                            <td>{$row['quantidade']}</td>
                                                        <td>{$row['regiao']}</td>

                                                                                                    <td>{$row['data_coleta']}</td>

                            <td>{$row['local']}</td>
                            <td>{$row['status']}</td>
                            <td>
                                <button class='btn btn-sm btn-warning editBtn' data-id='{$row['id']}'><i class='bi bi-pencil'></i></button>
                                <button class='btn btn-sm btn-danger deleteBtn' data-id='{$row['id']}'><i class='bi bi-trash'></i></button>
                            </td>
                          </tr>";
          }
        } else {
          echo "<tr><td colspan='7'>Nenhuma doação encontrada</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Modal Adicionar / Editar Doação -->
  <div class="modal fade" id="addDoacaoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="doacaoForm">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Adicionar Doação</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="id_doacao" name="id_doacao">
            <div class="mb-3">
              <label>Doador (ID do usuário)</label>


              <input type="number" class="form-control" name="id_usuario" id="id_usuario" required>
            </div>
            <div class="mb-3">
              <label>Tipo de Resíduo</label>
              <input type="text" class="form-control" name="tipo_residuo" id="tipo_residuo" required>
            </div>
            <div class="mb-3">
              <label>Quantidade (kg)</label>
              <input type="number" step="0.01" class="form-control" name="quantidade" id="quantidade" required>
            </div>
            <div class="mb-3">
              <label>Região</label>
              <input type="text" class="form-control" name="regiao" id="regiao" required>
            </div>
            <div class="mb-3">
              <label>Data de Coleta</label>
              <input type="text" class="form-control" name="data_coleta" id="data_coleta" required>
            </div>

            <div class="mb-3">
              <label>Endereço de Coleta</label>
              <input type="text" class="form-control" name="endereco_coleta" id="endereco_coleta" required>
            </div>

            <div class="mb-3">
              <label>Status</label>
              <select class="form-select" name="status" id="status">
                <option value="Doação cadastrada">Doação cadastrada</option>
                <option value="Aceita pela ecoraiz">Aceita pela ecoraiz</option>
                <option value="Em Compostagem">Em Compostagem</option>
                <option value="Reciclada">Reciclada</option>
                <option value="Cancelada">Cancelada</option>
              </select>
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
  <script>
    $(document).ready(function() {

      // Adicionar / Editar doação
      $('#doacaoForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
          url: 'doacao-actions.php',
          type: 'POST',
          data: $(this).serialize(),
          success: function(response) {
            location.reload(); // recarrega a página para atualizar a tabela
          }
        });
      });

      // Editar doação
      $('.editBtn').click(function() {
        let id = $(this).data('id');
        $.ajax({
          url: 'doacao-actions.php',
          type: 'GET',
          data: {
            action: 'get',
            id_doacao: id
          },
          dataType: 'json',
          success: function(data) {
            $('#id_doacao').val(data.id_doacao);
            $('#regiao').val(data.regiao);
            $('#data_coleta').val(data.data_coleta);

            $('#id_usuario').val(data.id_usuario);
            $('#tipo_residuo').val(data.tipo_residuo);
            $('#quantidade').val(data.quantidade);
            $('#endereco_coleta').val(data.endereco_coleta);
            $('#status').val(data.status);
            $('#modalTitle').text('Editar Doação');
            $('#addDoacaoModal').modal('show');
          }
        });
      });

      // Deletar doação
      $('.deleteBtn').click(function() {
        if (confirm('Tem certeza que deseja apagar esta doação?')) {
          let id = $(this).data('id');
          $.ajax({
            url: 'doacao-actions.php',
            type: 'POST',
            data: {
              action: 'delete',
              id_doacao: id
            },
            success: function() {
              $('#row-' + id).remove();
            }
          });
        }
      });
    });
    // Pesquisa em tempo real
    $('#searchInput').on('keyup', function() {
      var value = $(this).val().toLowerCase();

      $('#doacoesTableBody tr').filter(function() {
        $(this).toggle(
          $(this).text().toLowerCase().indexOf(value) > -1
        );
      });
    });


    // Ordenar tabela pelo ID
    let asc = true; // Flag para alternar entre crescente e decrescente

    $('#idHeader').click(function() {
      let rows = $('#doacoesTableBody tr').get();

      rows.sort(function(a, b) {
        let A = parseInt($(a).children('td').eq(0).text());
        let B = parseInt($(b).children('td').eq(0).text());

        if (asc) {
          return A - B; // Crescente
        } else {
          return B - A; // Decrescente
        }
      });

      $.each(rows, function(index, row) {
        $('#doacoesTableBody').append(row);
      });

      asc = !asc; // Inverte a ordem na próxima vez que clicar
    });

    const toggleButton = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');

toggleButton.addEventListener('click', () => {
  sidebar.classList.toggle('active');
});

  </script>
</body>

</html>

<?php $conn->close(); ?>