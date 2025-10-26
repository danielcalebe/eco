<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login-adm.php");
    exit;
}

include "db.php";
// Buscar todos os produtos e imagens (até 4)
$sql = "
SELECT 
    p.id_produto,
    p.nome_produto,
    p.preco,
    p.qtd_estoque,
    p.avaliacao,
    p.descricao,
    ip.caminho_imagem,
    p.categoria
FROM produto p
LEFT JOIN imagem_produto ip ON p.id_produto = ip.id_produto
ORDER BY p.id_produto DESC
";

$result = $conn->query($sql);

// Organizar imagens por produto
$produtos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_produto'];
        if (!isset($produtos[$id])) {
            $produtos[$id] = [
                'id_produto' => $id,
                'nome_produto' => $row['nome_produto'],
                'preco' => $row['preco'],
                'qtd_estoque' => $row['qtd_estoque'],
                'avaliacao' => $row['avaliacao'],
                'descricao' => $row['descricao'],
                'categoria' => $row['categoria'],

                'imagens' => []
            ];
        }
        if ($row['caminho_imagem']) $produtos[$id]['imagens'][] = $row['caminho_imagem'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles-adm.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <div class="profile">
            <img src="./img/logo-white.png" alt="Admin">
        </div>
        <a href="painel-adm.php"><i class="bi bi-house-door"></i> Home</a>
        <a href="doacao-adm.php"><i class="bi bi-box2-heart"></i> Doações</a>
        <a href="pedidos-adm.php"><i class="bi bi-basket"></i> Pedidos</a>
        <a href="impactos-adm.php"><i class="bi bi-bar-chart-line"></i> Impactos</a>
        <a href="cliente-adm.php"><i class="bi bi-people"></i> Clientes</a>
        <a href="produtos-adm.php" class="active"><i class="bi bi-bag"></i> Produtos</a>
        <a href="funcionario-adm.php"><i class="bi bi-person"></i> Funcionário</a>
        <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>

    <div class="content ml-2 my-4">
        <h3 class="fw-bold mb-4">Produtos</h3>

        <div class="d-flex justify-content-end gap-2 mb-3">
            <div class="mb-3" style="max-width:400px;">
                <div class="input-group border border-2">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar por ID, nome ou descrição">
                </div>
            </div>
            <button class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#modalProduto">
                <i class="bi bi-plus-lg"></i> Adicionar Produto
            </button>
        </div>

        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th class="d-flex gap-3" id="idHeader" style="cursor:pointer">ID <i class="bi bi-arrow-down-up"></i></th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Qtd Estoque</th>
                    <th>Avaliação</th>
                    <th>Descrição</th>
                    <th>Categoria</th>

                    <th>Imagens</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="produtosTableBody">
                <?php
                if ($produtos) {
                    foreach ($produtos as $p) {
                        $imagens_html = '';
                        if ($p['imagens']) {
                            foreach ($p['imagens'] as $img) {
                                $imagens_html .= "<img src='img/Produtos/{$img}' style='width:50px;height:50px;object-fit:cover;margin:2px;' />";
                            }
                        } else {
                            $imagens_html = "<span>Sem imagem</span>";
                        }

                        echo "<tr id='row-{$p['id_produto']}'>
                        <td>{$p['id_produto']}</td>
                        <td>{$p['nome_produto']}</td>
                        <td>{$p['preco']}</td>
                        <td>{$p['qtd_estoque']}</td>
                        <td>{$p['avaliacao']}</td>
                        <td>{$p['descricao']}</td>
                                                <td>{$p['categoria']}</td>

                        <td>{$imagens_html}</td>
                        <td>
                            <button class='btn btn-sm btn-warning editBtn' data-id='{$p['id_produto']}'><i class='bi bi-pencil'></i></button>
                            <button class='btn btn-sm btn-danger deleteBtn' data-id='{$p['id_produto']}'><i class='bi bi-trash'></i></button>
                        </td>
                    </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Nenhum produto encontrado</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Produto -->
    <div class="modal fade" id="modalProduto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formProduto" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar / Editar Produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_produto" id="id_produto">
                        <div class="mb-3">
                            <label class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" name="nome_produto" id="nome_produto" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preço</label>
                            <input type="number" step="0.01" class="form-control" name="preco" id="preco" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <input type="text" step="0.01" class="form-control" name="categoria" id="categoria" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade em Estoque</label>
                            <input type="number" class="form-control" name="qtd_estoque" id="qtd_estoque" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Avaliação</label>
                            <input type="number" step="0.1" class="form-control" name="avaliacao" id="avaliacao">
                        </div>
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="status">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="descricao" id="descricao"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagens do Produto (até 4)</label>
                            <input type="file" class="form-control" name="imagens[]" id="imagens" accept="image/*" multiple>
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

            // Salvar Produto
            $('#formProduto').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                formData.append('action', 'save_produto');

                $.ajax({
                    url: 'produto-actions.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        if (resp.error) alert(resp.error);
                        else location.reload();
                    },
                    error: function(xhr) {
                        alert(xhr.responseText);
                    }
                });
            });

            // Abrir modal de edição
            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: 'produto-actions.php',
                    type: 'POST',
                    data: {
                        action: 'get_produto',
                        id_produto: id
                    },
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.error) {
                            alert(resp.error);
                            return;
                        }
                        $('#status').val(resp.status || 'ativo');

                        $('#id_produto').val(resp.id_produto);
                        $('#nome_produto').val(resp.nome_produto);
                        $('#preco').val(resp.preco);
                        $('#categoria').val(resp.categoria);

                        $('#qtd_estoque').val(resp.qtd_estoque);
                        $('#avaliacao').val(resp.avaliacao);
                        $('#descricao').val(resp.descricao);
                        var modal = new bootstrap.Modal(document.getElementById('modalProduto'));
                        modal.show();
                    },
                    error: function(xhr) {
                        alert(xhr.responseText);
                    }
                });
            });

            // Deletar Produto
            $(document).on('click', '.deleteBtn', function() {
                if (confirm('Tem certeza que deseja apagar este produto?')) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: 'produto-actions.php',
                        type: 'POST',
                        data: {
                            action: 'delete_produto',
                            id_produto: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.error) {
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
                $('#produtosTableBody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Ordenar tabela pelo ID
            let asc = true;
            $('#idHeader').click(function() {
                let rows = $('#produtosTableBody tr').get();
                rows.sort(function(a, b) {
                    let A = parseInt($(a).children('td').eq(0).text());
                    let B = parseInt($(b).children('td').eq(0).text());
                    return asc ? A - B : B - A;
                });
                $.each(rows, function(index, row) {
                    $('#produtosTableBody').append(row);
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
