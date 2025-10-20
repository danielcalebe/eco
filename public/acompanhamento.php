<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="../css/css_acompanhamento.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>EcoRaiz</title>
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

<div class="container">
    <article class="card">
        <header class="card-header"> Meus Pedidos / Rastrear Pedidos </header>
        <div class="card-body">
            <h6>Order ID: OD45345345435</h6>
            <article class="card">
                <div class="card-header2 row ">
                    <div class="col"> <strong>Data estimada:</strong> <br>29 nov 2025 </div>
                    <div class="col"> <strong>Vendido por:</strong> <br> EcoRaiz, |+ 55 31 3234 5675 <i class="fa fa-phone"></i> + 55 31 3234 5675 </div>
                    <div class="col"> <strong>Status:</strong> <br> Chegou ao Correio </div>
                    <div class="col"> <strong>Código da compra #:</strong> <br> BD045903594059 </div>
                </div>
            </article>
            <div class="track">
                <div class="step active"> <span class="icon"> <img src="../img/check.png" class="img-sm check" alt="Check Icon"></span> <span class="text">Pedido Confirmado</span> </div>
                <div class="step active"> <span class="icon"> <img src="../img/user.png" class="img-sm user" alt="User Icon"></span> <span class="text"> Enviado</span> </div>
                <div class="step"> <span class="icon"> <img src="../img/truck.png" class="img-sm3 truck" alt="Truck Icon"></span> <span class="text"> A caminho</span> </div>
                <div class="step"> <span class="icon"> <img src="../img/box.png" class="img-sm3 box" alt="Box Icon"></span> <span class="text">Entregue</span> </div>
            </div>
            <hr>
            <ul class="row">
                <li class="col-md-4">
                    <figure class="itemside mb-3">
                        <div class="aside"><img src="../img/produto1.png" class="img-sm2 border"></div>
                        <figcaption class="info align-self-center">
                            <p class="title">Adubo de milho 10kg Fastpower </p> <span class="text-muted">R$ 200,00 </span> <p class="text-muted">Quantidade: 1</p>
                        </figcaption>
                    </figure>
                </li>
            </ul>
            <hr>
            <a href="#" class="btn btn-register" data-abc="true"> <i class="bi bi-arrow-left"></i> Voltar</a>
        </div>
    </article>
</div>
 <!-- Footer -->
    <footer class="text-center text-lg-start " style="background-color: #F2F7EC; ">
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
    <!-- Footer --> 
</body>
</html>