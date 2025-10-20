<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../css/checkout.css">

    <style>

    </style>
</head>

<body>


    <nav class="navbar navbar-expand-lg" style="background-color: #f3f8f1;">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

        <!-- Botão responsivo na extrema esquerda -->
        <button class="navbar-toggler order-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Logo -->
        <a class="navbar-brand mx-3" href="./Landing_Page/index.php">
            <img src="../img/logo.png" alt="Logo EcoRaiz" width="40">
        </a>

        <!-- Links do menu centralizados -->
        <div class="collapse navbar-collapse justify-content-center order-1" id="navbarNav">
            <ul class="navbar-nav mb-2 mb-lg-0 d-flex gap-5 ">
                <li class="nav-item"><a class="nav-link d-flex align-items-center" href="./Landing_Page/index.php"><i
                            class="bi bi-house-door me-1"></i> Início</a></li>
                <li class="nav-item"><a class="nav-link d-flex align-items-center" href="./catalogoprodutos.php"><i
                            class="bi bi-shop me-1"></i> Loja</a></li>
                <li class="nav-item"><a class="nav-link d-flex align-items-center" href="./doacoes.php"><i
                            class="bi bi-recycle me-1"></i> Doações</a></li>
                <li class="nav-item"><a class="nav-link d-flex align-items-center"
                        href="./Landing_Page/index.php#sobre"><i class="bi bi-info-circle me-1"></i> Institucional</a>
                </li>
                <li class="nav-item"><a class="nav-link d-flex align-items-center"
                        href="./Landing_Page/index.php#contato"><i class="bi bi-envelope me-1"></i> Contato</a></li>
            </ul>
        </div>

        <!-- Área de login/perfil à direita -->
        <div class="d-flex gap-3 align-items-center order-2">
            <a href="doacao.php" class="btn btn-success px-3 rounded-pill d-flex align-items-center">
                <i class="bi bi-heart-fill me-2"></i> Doar agora
            </a>
            <div class="d-flex flex-column align-items-center ms-2">
                <a href="perfil.php"> <i class="bi bi-person-circle fs-2 mb-1" style="color:#1E5E2E;"></i></a>
            </div>
        </div>

    </div>
</nav>
    <!-- Passos -->
    <div class="step-indicator pt-5 d-flex align-items-center">
        <span id="step1"><span class="circle active">1</span> Endereço</span>
        <span id="step2"><span class="circle">2</span> Pagamento</span>
    </div>

    <div id="checkout-container" class="container checkout-container mb-5 pb-4 ">



        <div class="row">
            <!-- Form Endereço -->
            <div class="col-md-6 mb-4">
                <div class="card card-custom p-4">
                    <h5 class="mb-3">Endereço de entrega</h5>
                    <!--Quando a pessoa salvar o endereço ele aparece nessa lista-->
                    <p><input type="radio" checked class="border-input"> Adicionar novo endereço</p>
                    <p><input type="radio" class="border-input"> Rua ABC, 123 - Belo Horizonte - MG </p>

                    <form>
                        <div class="row mb-3">
                            <div class="col">
                                <input type="text" class="form-control border-input" placeholder="Nome">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control border-input" placeholder="Sobrenome">
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control border-input" placeholder="Endereço">
                        </div>
                        <div class="row mb-3">
                            <div class="col-4">
                                <input type="text" class="form-control border-input" placeholder="Número">
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control border-input" placeholder="Estado">
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control border-input" placeholder="Cidade">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-dark">Cancelar</button>
                            <button type="submit" class="btn btn-save">Salvar esse endereço</button>
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
                    <h5>Pedido</h5>
                    <div class="d-flex justify-content-between">
                        <span>Fertilizante para milho (3)</span>
                        
                        <span>R$ 56,73</span>
                    </div>
                        <div class="d-flex justify-content-between">
                        <span>Fertilizante para milho (3)</span>
                        
                        <span>R$ 56,73</span>
                    </div>    <div class="d-flex justify-content-between">
                        <span>Fertilizante para milho (3)</span>
                        
                        <span>R$ 56,73</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Taxa de entrega</span>
                        <span>R$ 5,50</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Valor:</strong>
                        <strong>R$ 62,23</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5>Total do Pedido:</h5>
                        <h5>R$ 62,23</h5>
                    </div>
                </div>
            </div>
        </div>

    </div>
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

    </div>
    </div>

    <footer class="text-center text-lg-start  " style="background-color: #F2F7EC; ">
        <!-- Grid container -->
        <div class="container p-4 pb-0">
            <!-- Section: Links -->
            <section class="">
                <!--Grid row-->
                <div class="row">
                    <!-- Grid column -->
                    <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                        <h1 class="text-uppercase mb-4 font-weight-bold">
                            Ecoraiz
                            <img src="../img/logo.png" alt="" width="50px">
                        </h1>
                        <p>
                            Na EcoRaiz, oferecemos produtos naturais e sustentáveis,
                            cuidando do meio ambiente e do seu bem-estar em cada detalhe
                        </p>
                    </div>
                    <!-- Grid column -->

                    <hr class="w-100 clearfix d-md-none" />

                    <!-- Grid column -->
                    <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                        <h6 class="text-uppercase mb-4 font-weight-bold">Produtos</h6>
                        <p>
                            <a class="">Fertilizantes</a>
                        </p>
                        <p>
                            <a class="">Equipamentos</a>
                        </p>
                        <p>
                            <a class="">Pecuária</a>
                        </p>
                        <p>
                            <a class="">Horta</a>
                        </p>
                    </div>
                    <!-- Grid column -->

                    <hr class="w-100 clearfix d-md-none" />

                    <!-- Grid column -->
                    <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
                        <h6 class="text-uppercase mb-4 font-weight-bold">
                            Links Úteis
                        </h6>
                        <p>
                            <a class="">Sua conta</a>
                        </p>
                        <p>
                            <a class="">Doe agora</a>
                        </p>
                        <p>
                            <a class="">Compre na loja</a>
                        </p>
                        <p>
                            <a class="">Ajuda</a>
                        </p>
                    </div>

                    <!-- Grid column -->
                    <hr class="w-100 clearfix d-md-none" />

                    <!-- Grid column -->
                    <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                        <h6 class="text-uppercase mb-4 font-weight-bold">Contato</h6>
                        <p><i class="fas fa-home mr-3"></i> Belo Horizonte - MG, Brasil</p>
                        <p><i class="fas fa-envelope mr-3"></i> ecoraiz@contato.com</p>
                        <p><i class="fas fa-phone mr-3"></i> + 55 31 3234 5675</p>
                        <p><i class="fas fa-print mr-3"></i> + 55 31 3234 5672</p>
                    </div>
                    <!-- Grid column -->
                </div>
                <!--Grid row-->
            </section>
            <!-- Section: Links -->

            <hr class="my-3">

            <!-- Section: Copyright -->
            <section class="p-3 pt-0">
                <div class="row d-flex align-items-center">
                    <!-- Grid column -->
                    <div class="col-md-7 col-lg-8 text-center text-md-start">
                        <!-- Copyright -->
                        <div class="p-3">
                            © 2025 Copyright:
                            <a class="" href="">www.ecoraiz.com</a>
                        </div>
                        <!-- Copyright -->
                    </div>
                    <!-- Grid column -->

                    <!-- Grid column -->
                    <div class="col-md-5 col-lg-4 ml-lg-0 text-center text-md-end">
                        <!-- Facebook -->
                        <a class="btn btn-outline-light btn-floating m-1" class="" role="button"><i
                                class="bi bi-facebook"></i>
                        </a>

                        <!-- Twitter -->
                        <a class="btn btn-outline-light btn-floating m-1" class="" role="button"><i
                                class="bi bi-twitter"></i></a>

                        <!-- Google -->
                        <a class="btn btn-outline-light btn-floating m-1" class="" role="button"><i
                                class="bi bi-google"></i></a>

                        <!-- Instagram -->
                        <a class="btn btn-outline-light btn-floating m-1" class="" role="button"><i
                                class="bi bi-instagram"></i></a>
                    </div>
                    <!-- Grid column -->
                </div>
            </section>
        </div>
        <!-- Grid container -->
    </footer>


    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const btnComprar = document.getElementById("btnComprar");
        const checkoutContainer = document.getElementById("checkout-container");
        const paymentContainer = document.getElementById("payment-container");
        // Passos
        const step1 = document.querySelector("#step1 .circle");
        const step2 = document.querySelector("#step2 .circle");

        // Remove a classe 'texto-original' do parágrafo
        btnComprar.addEventListener("click", async () => {
            // Esconde o checkout
            checkoutContainer.style.display = "none";
            paymentContainer.classList.remove('d-none');

            // Mostra o container de pagamento
            paymentContainer.style.display = "block";

            step1.classList.remove("active");
            step2.classList.add("active");
        });;


        const stripe = Stripe("pk_test_51SDmj71htB3038T8Ge7yJlZBDTSSVPu4hFyRAjqMpeC8OcZNDLLpGOh5jTwK3h2GmfVdT1YazJBflV4Z1DD3yy7j00HQp9du3k");

        async function init() {
            const res = await fetch("http://localhost:3000/create-payment-intent", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ amount: 6223 })
            });
            const { clientSecret } = await res.json();

            const elements = stripe.elements({ clientSecret });
            const paymentElement = elements.create("payment");
            paymentElement.mount("#payment-element");



            document.getElementById("submit").addEventListener("click", async () => {
                const { error } = await stripe.confirmPayment({
                    elements,
                    confirmParams: { return_url: "http://localhost:3000/login.php" }
                });
                if (error) console.log(error.message);
            });
        }

        init();


        
    </script>

</body>

</html>
```