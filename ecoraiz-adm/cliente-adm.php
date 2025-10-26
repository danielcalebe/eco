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

// Buscar todos os usuários com dados de clientes PF e PJ
$sql = "
SELECT 
    u.id_usuario,
    u.nome,
    u.email,
    u.tipo_usuario,
    u.telefone,
        u.nome_img,

    cf.cpf AS cpf_cliente,
    cf.data_nascimento,
    cj.cnpj AS cnpj_cliente,
    cj.razao_social
FROM usuario u
LEFT JOIN cliente_fisico cf ON u.id_usuario = cf.id_usuario AND u.tipo_usuario = 'PF'
LEFT JOIN cliente_juridico cj ON u.id_usuario = cj.id_usuario AND u.tipo_usuario = 'PJ'
ORDER BY u.id_usuario DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Admin - Usuários/Clientes</title>
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
    <a href="impactos-adm.php"><i class="bi bi-bar-chart-line"></i> Impactos</a>
    <a href="cliente-adm.php" class="active"><i class="bi bi-people"></i> Clientes</a>
    <a href="produtos-adm.php"><i class="bi bi-bag"></i> Produtos</a>
    <a href="funcionario-adm.php"><i class="bi bi-person"></i> Funcionário</a>
    <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
</div>

<div class="content ml-2 my-4">
    <h3 class="fw-bold mb-4">Usuários / Clientes</h3>

    <div class="d-flex justify-content-end gap-2 mb-3">
        <div class="mb-3" style="max-width:400px;">
            <div class="input-group border border-2">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar por ID, nome, email ou tipo">
            </div>
        </div>
        <button class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#modalPF">
            <i class="bi bi-plus-lg"></i> Adicionar PF
        </button>
        <button class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#modalPJ">
            <i class="bi bi-plus-lg"></i> Adicionar PJ
        </button>
    </div>

 <table class="table table-bordered align-middle text-center">
    <thead class="table-light">
        <tr>
            <th class="d-flex gap-3" id="idHeader" style="cursor:pointer">ID <i class="bi bi-arrow-down-up"></i></th>
            <th>Nome / Razão Social</th>
            <th>Imagem</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Telefone</th>
            <th>CPF / CNPJ</th>
            <th>Data Nascimento / Razão Social</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody id="usuariosTableBody">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tipo = $row['tipo_usuario'] === 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica';
                $cpf_cnpj = $row['tipo_usuario'] === 'PF' ? $row['cpf_cliente'] : $row['cnpj_cliente'];
                $extra = $row['tipo_usuario'] === 'PF' ? $row['data_nascimento'] : $row['razao_social'];
                $img_html = !empty($row['nome_img'])
                    ? "<img src='img/Usuarios/{$row['nome_img']}' alt='Foto do usuário' style='width:50px; height:50px; object-fit:cover;'>"
                    : "<span>Sem imagem</span>";

                echo "<tr id='row-{$row['id_usuario']}'>
                        <td>{$row['id_usuario']}</td>
                        <td>{$row['nome']}</td>
                        <td>{$img_html}</td>
                        <td>{$row['email']}</td>
                        <td>{$tipo}</td>
                        <td>{$row['telefone']}</td>
                        <td>{$cpf_cnpj}</td>
                        <td>{$extra}</td>
                        <td>
                            <button class='btn btn-sm btn-warning editBtn' data-id='{$row['id_usuario']}' data-tipo='{$row['tipo_usuario']}'><i class='bi bi-pencil'></i></button>
                            <button class='btn btn-sm btn-danger deleteBtn' data-id='{$row['id_usuario']}'><i class='bi bi-trash'></i></button>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>Nenhum usuário encontrado</td></tr>";
        }
        ?>
    </tbody>
</table>

</div>

<!-- Modal PF -->
<div class="modal fade" id="modalPF" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formPF">
        <div class="modal-header">
          <h5 class="modal-title">Adicionar Cliente - Pessoa Física</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_usuario_pf" id="id_usuario_pf">
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome_pf" id="nome_pf" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email_pf" id="email_pf" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" class="form-control" name="senha_pf" id="senha_pf" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" class="form-control" name="telefone_pf" id="telefone_pf">
          </div>
          <div class="mb-3">
            <label class="form-label">CPF</label>
            <input type="text" class="form-control" name="cpf_pf" id="cpf_pf">
          </div>

          <div class="mb-3">
  <label class="form-label">Imagem do Usuário</label>
  <input type="file" class="form-control" name="img_pf" id="img_pf" accept="image/*">
</div>

          <div class="mb-3">
            <label class="form-label">Data Nascimento</label>
            <input type="date" class="form-control" name="nascimento_pf" id="nascimento_pf">
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

<!-- Modal PJ -->
<div class="modal fade" id="modalPJ" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formPJ">
        <div class="modal-header">
          <h5 class="modal-title">Adicionar Cliente - Pessoa Jurídica</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_usuario_pj" id="id_usuario_pj">
          <div class="mb-3">
            <label class="form-label">Razão Social</label>
            <input type="text" class="form-control" name="razao_social_pj" id="razao_social_pj" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email_pj" id="email_pj" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" class="form-control" name="senha_pj" id="senha_pj" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" class="form-control" name="telefone_pj" id="telefone_pj">
          </div>
          <div class="mb-3">
  <label class="form-label">Imagem do Usuário</label>
  <input type="file" class="form-control" name="img_pj" id="img_pj" accept="image/*">
</div>

          <div class="mb-3">
            <label class="form-label">CNPJ</label>
            <input type="text" class="form-control" name="cnpj_pj" id="cnpj_pj">
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


  
    // Salvar PF
$('#formPF').submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);
    formData.append('action','save_pf');

    $.ajax({
        url: 'cliente-actions.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(resp){
            if(resp.error) alert(resp.error);
            else location.reload();
        },
        error: function(xhr){ alert(xhr.responseText); }
    });
});


    // Salvar PJ com suporte a imagem
$('#formPJ').submit(function(e){
    e.preventDefault();
    let formData = new FormData(this); // pega todos os campos do formulário, inclusive o file
    formData.append('action', 'save_pj'); // adiciona a ação

    $.ajax({
        url: 'cliente-actions.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        contentType: false, // necessário para FormData
        processData: false, // necessário para FormData
        success: function(resp){
            if(resp.error) alert(resp.error);
            else location.reload();
        },
        error: function(xhr){
            alert(xhr.responseText);
        }
    });
});



    // Abrir modal de edição e preencher os campos
$(document).on('click', '.editBtn', function() {
    let id = $(this).data('id');
    let tipo = $(this).data('tipo');

    // Buscar os dados do usuário via AJAX
    $.ajax({
        url: 'cliente-actions.php',
        type: 'POST',
        data: { action: 'get_user', id_usuario: id },
        dataType: 'json',
        success: function(resp) {
            if(resp.error){
                alert(resp.error);
                return;
            }

            if(tipo === 'PF'){
                $('#id_usuario_pf').val(resp.id_usuario);
                $('#nome_pf').val(resp.nome);
                $('#email_pf').val(resp.email);
                $('#senha_pf').val(''); // senha sempre vazia para segurança
                $('#telefone_pf').val(resp.telefone);
                $('#cpf_pf').val(resp.cpf);
                $('#nascimento_pf').val(resp.data_nascimento);
                var modalPF = new bootstrap.Modal(document.getElementById('modalPF'));
                modalPF.show();
            } else if(tipo === 'PJ'){
                $('#id_usuario_pj').val(resp.id_usuario);
                $('#razao_social_pj').val(resp.razao_social);
                $('#email_pj').val(resp.email);
                $('#senha_pj').val('');
                $('#telefone_pj').val(resp.telefone);
                $('#cnpj_pj').val(resp.cnpj);
                var modalPJ = new bootstrap.Modal(document.getElementById('modalPJ'));
                modalPJ.show();
            }
        },
        error: function(xhr){
            alert(xhr.responseText);
        }
    });
});

    // Deletar cliente
    $(document).on('click', '.deleteBtn', function() {
        if(confirm('Tem certeza que deseja apagar este cliente?')){
            let id = $(this).data('id');
            $.ajax({
                url: 'cliente-actions.php',
                type: 'POST',
                data: { action: 'delete', id_usuario: id },
                dataType: 'json',
                success: function(response) {
                    if(response.error){
                        alert(response.error);
                    } else {
                        location.reload();
                    }
                }
            });
        }
    });

    // Pesquisa em tempo real
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#usuariosTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Ordenar tabela pelo ID
    let asc = true;
    $('#idHeader').click(function() {
        let rows = $('#usuariosTableBody tr').get();
        rows.sort(function(a, b) {
            let A = parseInt($(a).children('td').eq(0).text());
            let B = parseInt($(b).children('td').eq(0).text());
            return asc ? A - B : B - A;
        });
        $.each(rows, function(index, row) {
            $('#usuariosTableBody').append(row);
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
