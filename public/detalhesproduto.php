<?php 
include '../db.php'; // conexão com banco

// Verifica se o ID do produto foi passado
if (!isset($_GET['id'])) {
    header("Location: catalogoprodutos.php");
    exit;
}

$produto_id = intval($_GET['id']);

// Pega os dados do produto
$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();

if (!$produto) {
    echo "Produto não encontrado!";
    exit;
}

// Mensagem de feedback
$mensagem = "";

// Se o formulário de compra for enviado
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comprar'])) {
    $quantidade = intval($_POST['quantidade']);
    
    if ($quantidade < 1) $quantidade = 1;

    // Exemplo: salva o item em uma tabela "carrinho" ou redireciona direto ao checkout
    // Aqui você pode adaptar conforme sua lógica (sessão, banco, etc.)
    session_start();
    $_SESSION['compra'] = [
        'id' => $produto['id'],
        'nome' => $produto['nome'],
        'preco' => $produto['preco'],
        'quantidade' => $quantidade,
        'total' => $produto['preco'] * $quantidade
    ];

    header("Location: checkout.php");
    exit;
}

// Tratamento de envio de avaliação
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['avaliar'])) {
    $nome = htmlspecialchars($_POST['nome']);
    $nota = intval($_POST['nota']);
    $comentario = htmlspecialchars($_POST['comentario']);

    $stmt2 = $conn->prepare("INSERT INTO avaliacoes (produto_id, nome_cliente, nota, comentario, data) VALUES (?, ?, ?, ?, NOW())");
    $stmt2->bind_param("isis", $produto_id, $nome, $nota, $comentario);

    if ($stmt2->execute()) {
        $mensagem = "<div class='alert alert-success'>Avaliação enviada com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao enviar avaliação: " . $conn->error . "</div>";
    }
}

// Pega as avaliações do produto
$stmt3 = $conn->prepare("SELECT * FROM avaliacoes WHERE produto_id = ? ORDER BY data DESC");
$stmt3->bind_param("i", $produto_id);
$stmt3->execute();
$avaliacoes = $stmt3->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto - <?php echo htmlspecialchars($produto['nome']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/catalogoprodutos.css">
    <link rel="stylesheet" href="../css/detalhesproduto.css">
</head>
<body>
<nav class="navbar navbar-expand-lg" style="background-color: #f3f8f1;">
    <!-- Navbar -->
</nav>

<div class="p-4">
    <a style="color: #1E5E2E;" href="catalogoprodutos.php"> <i class="bi bi-arrow-left h3"></i></a>
</div>

<section id="detalhes_produto">
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 align-items-center">
                <div class="col-md-6">
                    <div class="zoom-container rounded">
                        <img id="mainProductImage" src="../img/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" />
                        <div id="zoomResult" class="zoom-result"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="small mb-1">SKU: <?php echo htmlspecialchars($produto['sku']); ?></div>
                    <h1 class="display-5 fw-bolder fs-1"><?php echo htmlspecialchars($produto['nome']); ?></h1>
                    <div class="fs-5 mb-5 d-flex justify-content-between">
                        <h1>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></h1>
                    </div>
                    <p class="lead"><?php echo htmlspecialchars($produto['descricao']); ?></p>

                    <!-- Formulário de compra -->
                    <form method="POST" action="">
                        <div class="d-flex">
                            <input class="form-control text-center me-3 p-2 border-input" name="quantidade" type="number" value="1" min="1" style="max-width: 4rem" />
                            <button type="submit" name="comprar" class="btn flex-shrink-0 btn-buy">
                                <i class="bi-cart-fill me-1"></i> Comprar agora
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Avaliações -->
    <section class="py-5 border-top" style="background-color: #F2F7EC;">
        <div class="container px-4 px-lg-5 py-5 rounded" style="background-color: #517c58;">
            <h1 class="fw-bold mb-4 text-center" style="color: #F2F7EC;">Avaliações dos Clientes</h1>

            <?php echo $mensagem; ?>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 h-100" style="background-color: #F7F5EC;">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-3">Deixe sua avaliação</h5>
                            <form method="POST" action="">
                                <input type="hidden" name="avaliar" value="1">
                                <div class="mb-3">
                                    <input type="text" name="nome" class="form-control border-input" placeholder="Seu nome" required>
                                </div>
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
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="bi bi-send-fill me-1"></i> Enviar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <?php while($av = $avaliacoes->fetch_assoc()) { ?>
                        <div class="card border-0 shadow-sm h-100 mb-3">
                            <div class="card-body rounded-2 shadow" style="background-color: #F7F5EC;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($av['nome_cliente']); ?></h6>
                                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($av['data'])); ?></small>
                                </div>
                                <div class="mb-2 text-warning">
                                    <?php for($i=1; $i<=5; $i++) {
                                        echo $i <= $av['nota'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                    } ?>
                                </div>
                                <p class="mb-0"><?php echo htmlspecialchars($av['comentario']); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</section>

<footer class="text-center text-lg-start" style="background-color: #F2F7EC;">
    <!-- Footer -->
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/detalhesproduto.js"></script>
</body>
</html>
