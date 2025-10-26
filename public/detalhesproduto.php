<?php
session_start();
include './db.php'; // Conexão com o banco

// Verifica se o ID do produto foi enviado
if (!isset($_GET['id'])) {
    
    header('Location: catalogoprodutos.php');

    exit;
}

$id_produto = intval($_GET['id']);

// Busca os dados do produto
$stmt = $conn->prepare("SELECT * FROM produto WHERE id_produto = ?");
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();

if (!$produto) {
    echo "Produto não encontrado!";
    exit;
}

// Busca imagens do produto
$stmt_img = $conn->prepare("SELECT * FROM imagem_produto WHERE id_produto = ? LIMIT 5");
$stmt_img->bind_param("i", $id_produto);
$stmt_img->execute();
$result_img = $stmt_img->get_result();
$imagens = $result_img->fetch_all(MYSQLI_ASSOC);

// Busca avaliações do produto  
$stmt_av = $conn->prepare("
    SELECT a.*, u.nome 
    FROM avaliacao a 
    JOIN usuario u ON a.id_usuario = u.id_usuario 
    WHERE a.id_produto = ? 
    ORDER BY a.data_avaliacao DESC
");
$stmt_av->bind_param("i", $id_produto);
$stmt_av->execute();
$result_av = $stmt_av->get_result();
$avaliacoes = $result_av->fetch_all(MYSQLI_ASSOC);

// Produtos relacionados (mesma categoria)
$stmt_rel = $conn->prepare("
    SELECT * FROM produto 
    WHERE categoria = ? AND id_produto != ? 
    LIMIT 5
");
$stmt_rel->bind_param("si", $produto['categoria'], $id_produto);
$stmt_rel->execute();
$result_rel = $stmt_rel->get_result();
$produtos_relacionados = $result_rel->fetch_all(MYSQLI_ASSOC);

// Buscar a primeira imagem de cada produto relacionado
foreach ($produtos_relacionados as $key => $p) {
    $stmt_img_rel = $conn->prepare("SELECT * FROM imagem_produto WHERE id_produto = ? LIMIT 1");
    $stmt_img_rel->bind_param("i", $p['id_produto']);
    $stmt_img_rel->execute();
    $result_img_rel = $stmt_img_rel->get_result();
    $img_rel = $result_img_rel->fetch_assoc();

    $produtos_relacionados[$key]['imagem_principal'] = $img_rel['caminho_imagem'] ?? 'produto1.png';
}

// Verifica se o usuário comprou o produto
$usuario_comprou = false;
$id_usuario = $_SESSION['id_usuario'] ?? null;

if ($id_usuario) {
    $stmt_check = $conn->prepare("
        SELECT 1
        FROM item_pedido ip
        JOIN pedido p ON ip.id_pedido = p.id_pedido
        WHERE ip.id_produto = ? AND p.id_usuario = ?
        LIMIT 1
    ");
    $stmt_check->bind_param("ii", $id_produto, $id_usuario);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    $usuario_comprou = $res_check->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($produto['nome_produto']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/catalogoprodutos.css">
<link rel="stylesheet" href="../css/detalhesproduto.css">
</head>
<body>

<!-- Navbar igual ao HTML fornecido -->
<nav class="navbar navbar-expand-lg" style="background-color: #f3f8f1;">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <button class="navbar-toggler order-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand mx-3" href="./Landing_Page/index.html">
            <img src="../img/logo.png" alt="Logo EcoRaiz" width="40">
        </a>
        <div class="collapse navbar-collapse justify-content-center order-1" id="navbarNav">
            <ul class="navbar-nav mb-2 mb-lg-0 d-flex gap-5 ">
                <li class="nav-item"><a class="nav-link" href="./Landing_Page/index.html"><i class="bi bi-house-door me-1"></i> Início</a></li>
                <li class="nav-item"><a class="nav-link" href="./catalogoprodutos.php"><i class="bi bi-shop me-1"></i> Loja</a></li>
                <li class="nav-item"><a class="nav-link" href="./doacoes.php"><i class="bi bi-recycle me-1"></i> Doações</a></li>
                <li class="nav-item"><a class="nav-link" href="./Landing_Page/index.html#sobre"><i class="bi bi-info-circle me-1"></i> Institucional</a></li>
                <li class="nav-item"><a class="nav-link" href="./Landing_Page/index.html#contato"><i class="bi bi-envelope me-1"></i> Contato</a></li>
            </ul>
        </div>
        <div class="d-flex gap-3 align-items-center order-2">
            <a href="doacao.php" class="btn btn-success px-3 rounded-pill d-flex align-items-center"><i class="bi bi-heart-fill me-2"></i> Doar agora</a>
            <div class="d-flex flex-column align-items-center ms-2">
                <a href="perfil.php"> <i class="bi bi-person-circle fs-2 mb-1" style="color:#1E5E2E;"></i></a>
            </div>
        </div>
    </div>
</nav>

<div class="p-4">
    <a style="color: #1E5E2E;" href="catalogoprodutos.php"><i class="bi bi-arrow-left h3"></i></a>
</div>

<section id="detalhes_produto">
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 align-items-center">

                <!-- Imagem principal -->
                <div class="col-md-6">
                    <div class="zoom-container rounded">
                        <img id="mainProductImage" src="../ecoraiz-adm/img/Produtos/<?= $imagens[0]['caminho_imagem'] ?? 'produto1.png' ?>" alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                        <div id="zoomResult" class="zoom-result"></div>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <?php foreach ($imagens as $img): ?>
                            <img onclick="changeImage(this)" src="../ecoraiz-adm/img/Produtos/<?= $img['caminho_imagem'] ?>" class="thumb-img" alt="thumb">
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Detalhes -->
                <div class="col-md-6">
                    <h1 class="display-5 fw-bolder fs-1"><?= htmlspecialchars($produto['nome_produto']) ?></h1>
                    <div class="fs-5 mb-5 d-flex justify-content-between">
                        <h1>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></h1>
                        <div class="rating mb-2 text-start">
                            <?php
                            $stars = floor($produto['avaliacao']);
                            for ($i = 0; $i < 5; $i++) {
                                echo $i < $stars ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star"></i>';
                            }
                            ?>
                            <small class="text-muted">(<?= count($avaliacoes) ?> Comentários)</small>
                        </div>
                    </div>
                    <p class="lead"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                    <form method="POST" action="pedido_processar.php" class="d-flex">
                        <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">
                        <input type="hidden" name="nome_produto" value="<?= htmlspecialchars($produto['nome_produto']) ?>">
                        <input type="hidden" name="preco_unitario" value="<?= $produto['preco'] ?>">
                        <input class="form-control text-center me-3 p-2 border-input" id="inputQuantity" name="quantidade" type="number" value="1" min="1" style="max-width: 3rem">
                        <button type="submit" class="btn btn-buy flex-shrink-0"><i class="bi bi-cart-fill me-1"></i> Comprar agora</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
<!-- Avaliações -->
<section class="py-5 border-top" style="background-color: #F2F7EC;">
    <div class="container px-4 px-lg-5 py-5 rounded" style="background-color: #517c58;">
        <h1 class="fw-bold mb-4 text-center" style="color: #F2F7EC;">Avaliações dos Clientes</h1>
        <div id="avalia" class="row g-4">

            <!-- Mensagens de sucesso ou erro -->
            <?php if (isset($_SESSION['sucesso_avaliacao'])): ?>
                <div class="col-12 mb-3">
                    <div class="alert alert-success"><?= $_SESSION['sucesso_avaliacao'] ?></div>
                </div>
                <?php unset($_SESSION['sucesso_avaliacao']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['erro_avaliacao'])): ?>
                <div class="col-12 mb-3">
                    <div class="alert alert-danger"><?= $_SESSION['erro_avaliacao'] ?></div>
                </div>
                <?php unset($_SESSION['erro_avaliacao']); ?>
            <?php endif; ?>

            <!-- Formulário de avaliação -->
            <?php if ($usuario_comprou): ?>
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100" style="background-color: #F7F5EC;">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">Deixe sua avaliação</h5>
                        <form method="POST" action="avaliacao_processar.php">
                            <div style="display: none;">    <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">
                            <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>"></div>
                        
                    
                            <div class="mb-3">
                                <select name="nota" class="form-select border-input" required>
                                    <option selected disabled>Selecione sua nota</option>
                                    <option value="5">5 estrelas</option>
                                    <option value="4">4 estrelas</option>
                                    <option value="3">3 estrelas</option>
                                    <option value="2">2 estrelas</option>
                                    <option value="1">1 estrela</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea name="comentario" class="form-control border-input" rows="3" placeholder="Escreva seu comentário..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-4"><i class="bi bi-send-fill me-1"></i> Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="col-12 mb-4">
                    <div class="alert alert-warning">Compre este produto para poder avaliá-lo.</div>
                </div>
            <?php endif; ?>

            <!-- Comentários existentes -->
            <div class="<?= $usuario_comprou ? 'col-lg-7' : 'col-12' ?>">
                <div class="row g-4">
                    <?php foreach ($avaliacoes as $av): ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body rounded-2 shadow" style="background-color: #F7F5EC;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0"><?= htmlspecialchars($av['nome']) ?></h6>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($av['data_avaliacao'])) ?></small>
                                </div>
                                <div class="mb-2 text-warning">
                                    <?php
                                    $stars = intval($av['nota']);
                                    for ($i = 0; $i < 5; $i++) {
                                        echo $i < $stars ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                    }
                                    ?>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($av['comentario'])) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($avaliacoes) === 0): ?>
                    <div class="col-12"><div class="alert alert-info">Nenhuma avaliação ainda.</div></div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>



    <!-- Produtos relacionados -->
    <section class="container mb-4">
        <div class="container px-4 px-lg-6 mt-4 p-5 rounded-3">
            <h1>Produtos Relacionados</h1>
            <div class="row gx-4 gx-lg-2 row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 justify-content-center">
                <?php foreach ($produtos_relacionados as $p): ?>
                <div class="col mb-4">
                    <div class="card product-card h-100 text-center">
                        <img class="card-img-top p-3" src="../ecoraiz-adm/img/Produtos/<?= $p['imagem_principal'] ?? 'produto1.png' ?>" alt="<?= htmlspecialchars($p['nome_produto']) ?>">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title text-start"><?= htmlspecialchars($p['nome_produto']) ?></h6>
                            <h4 class="fw-bold mb-3 text-start">R$ <?= number_format($p['preco'], 2, ',', '.') ?></h4>
                            <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
                                <a href="detalhesproduto.php?id=<?= $p['id_produto'] ?>" class="btn btn-buy flex-grow-1"><i class="bi bi-cart-plus"></i> Comprar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</section>

<script>
function changeImage(img) {
    document.getElementById("mainProductImage").src = img.src;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
