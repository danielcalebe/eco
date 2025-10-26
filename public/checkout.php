<?php
// checkout.php
session_start();

// Verifica se veio o código do pedido via GET
if (!isset($_GET['codigo'])) {
    die("Código do pedido não fornecido.");
}

$codigoPedido = $_GET['codigo'];

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecoraiz";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consulta os dados do pedido
$stmt = $conn->prepare("
    SELECT p.id_pedido, p.total_pedido, p.nome_endereco, p.endereco_entrega, p.status, p.forma_pagamento, p.codigo
    FROM pedido p
    WHERE p.codigo = ?
");
$stmt->bind_param("s", $codigoPedido);
$stmt->execute();
$result = $stmt->get_result();
$pedido = $result->fetch_assoc();

if (!$pedido) {
    die("Pedido não encontrado.");
}

// Consulta os itens do pedido
$stmtItens = $conn->prepare("
    SELECT i.qtd, i.preco_unitario, pr.nome_produto
    FROM item_pedido i
    JOIN produto pr ON i.id_produto = pr.id_produto
    WHERE i.id_pedido = ?
");
$stmtItens->bind_param("i", $pedido['id_pedido']);
$stmtItens->execute();
$resultItens = $stmtItens->get_result();
$itens = $resultItens->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$stmtItens->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <!-- Bootstrap CSS -->
       <?php include '../elements/head.php'; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/checkout.css">
</head>
<body>
   <?php include '../elements/navbar.php'; ?>


    <!-- Contêiner de alertas -->
    <div class="container mt-5 pt-4" id="alert-container"></div>

    <!-- Passos -->
    <div class="step-indicator pt-5 d-flex align-items-center justify-content-center gap-5">
        <span id="step1"><span class="circle active">1</span> Endereço</span>
        <span id="step2"><span class="circle">2</span> Pagamento</span>
    </div>

    <div id="checkout-container" class="container checkout-container mb-5 pb-4">
        <div class="row">
            <!-- Form Endereço -->
            <div class="col-md-6 mb-4">
                <div class="card card-custom p-4">
                    <h5 class="mb-3">Endereço de entrega</h5>
                    <form id="endereco-form">
                        <div class="mb-3">
                            <input type="text" id="nome" class="form-control border-input" placeholder="Nome para recebimento" value="<?= $pedido["nome_endereco"] ?   htmlspecialchars($pedido['nome_endereco']) : "" ?>">
                        </div>
                        <div class="mb-3">
                            <input type="text" id="endereco" class="form-control border-input" placeholder="Endereço" value="<?= $pedido["endereco_entrega"] ?   htmlspecialchars($pedido['endereco_entrega']) : "" ?>">
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-dark" id="btnCancelar">Cancelar</button>
                            <button type="submit" class="btn btn-save" id="btnSalvarEndereco">Salvar esse endereço</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resumo do Pedido -->
            <div class="col-md-6 mb-4">
                <div class="card card-custom p-4">
                    <button id="btnComprar" class="btn btn-order w-100 mb-3">Fazer pedido</button>
                    <small>Ao fazer seu pedido você concorda com nossa <a href="#">Política de Privacidade</a>.</small>
                    <hr>
                    <h5>Itens do Pedido</h5>
                    <?php if ($itens): ?>
                        <?php foreach ($itens as $item): ?>
                            <div class="d-flex justify-content-between">
                                <span><?= htmlspecialchars($item['nome_produto']) ?> (<?= $item['qtd'] ?>)</span>
                                <span>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Nenhum item encontrado neste pedido.</p>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5>Total do Pedido:</h5>
                        <h5>R$ <?= number_format($pedido['total_pedido'], 2, ',', '.') ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagamento -->
    <div class="container py-5 d-none" id="payment-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow">
                    <h5 class="mb-4">Pagamento</h5>
                    <form id="stripe-payment-form">
                        <div id="payment-element"></div>
                        <button id="submit" class="btn btn-success mt-3">Pagar</button>
                        <div id="payment-message"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    
    <script src="https://js.stripe.com/v3/"></script>
  <script>
const btnComprar = document.getElementById("btnComprar");
const checkoutContainer = document.getElementById("checkout-container");
const paymentContainer = document.getElementById("payment-container");
const step1 = document.querySelector("#step1 .circle");
const step2 = document.querySelector("#step2 .circle");
const enderecoForm = document.getElementById("endereco-form");
const alertContainer = document.getElementById("alert-container");
let enderecoSalvo = false;

function showAlert(message, type = "info") {
    const wrapper = document.createElement("div");
    wrapper.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    alertContainer.appendChild(wrapper);
    setTimeout(() => {
        const alert = bootstrap.Alert.getOrCreateInstance(wrapper.querySelector(".alert"));
        alert.close();
    }, 4000);
}

// Salvar endereço
enderecoForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const nome = document.getElementById("nome").value;
    const endereco = document.getElementById("endereco").value;

    if (!nome || !endereco) {
        showAlert("Preencha todos os campos do endereço.", "warning");
        return;
    }

    try {
        const res = await fetch("salvar_endereco.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                codigoPedido: "<?= $codigoPedido ?>",
                nome,
                endereco
            })
        });

        const data = await res.json();
        if (data.success) {
            showAlert("Endereço salvo com sucesso!", "success");
            enderecoSalvo = true;
        } else {
            showAlert("Erro ao salvar endereço: " + data.error, "danger");
        }
    } catch (err) {
        showAlert("Erro de conexão: " + err.message, "danger");
    }
});

// Passar para pagamento
btnComprar.addEventListener("click", () => {
    if (!enderecoSalvo) {
        showAlert("Por favor, salve o endereço antes de continuar para o pagamento.", "warning");
        return;
    }
    checkoutContainer.style.display = "none";
    paymentContainer.classList.remove('d-none');
    step1.classList.remove("active");
    step2.classList.add("active");
});

// Stripe
const stripe = Stripe("pk_test_51SDmj71htB3038T8Ge7yJlZBDTSSVPu4hFyRAjqMpeC8OcZNDLLpGOh5jTwK3h2GmfVdT1YazJBflV4Z1DD3yy7j00HQp9du3k");

async function init() {
    const res = await fetch("http://localhost:3000/create-payment-intent", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ amount: <?= intval($pedido['total_pedido'] * 100) ?> })
    });
    const { clientSecret } = await res.json();

    const elements = stripe.elements({ clientSecret });
    const paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");

    document.getElementById("submit").addEventListener("click", async (e) => {
        e.preventDefault();
        const { error, paymentIntent } = await stripe.confirmPayment({
            elements,
            confirmParams: { 
    return_url: "http://localhost/ecoraiz/public/Landing_Page/index.php?success=1" 
            }
        });
        if (error) showAlert(error.message, "danger");
    });
}

init();
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
