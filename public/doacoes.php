<?php
// doacoes.php
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doações - EcoRaiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/doacoes.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg" style="background-color: #f3f8f1;">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

            <!-- Botão responsivo -->
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

    <!-- Banner Principal -->
    <section class="banner">
        <div class="banner-overlay"></div>
        <div class="banner-content">
            <h1>DINÂMICA ORGÂNICA</h1>
            <p>Temos um negócio social que coleta e transforma o seu lixo orgânico em fonte de vida!</p>
            <a href="doacao.php" class="btn btn-doar"><i class="bi bi-heart-fill me-2"></i> Doe agora</a>
        </div>
    </section>

    <!-- Conteúdo da página -->
    <section class="participar-section">
        <div class="container">
            <div class="participar-card mx-auto d-flex flex-column flex-md-row">
                <img src="../img/quatro-topicos.png" alt="Ciclo Orgânico">
                <div class="participar-text">
                    <h1>Porque participar?</h1>
                    <p>A Dinâmica Orgânica é a opção ideal para quem busca um estilo de vida mais sustentável e deseja
                        resolver a questão do lixo orgânico de forma eficiente!</p>
                    <p>Em apenas 10 anos, <strong>cada família que adere à Dinâmica evita que um caminhão completo de
                            resíduos orgânicos seja enviado para aterros sanitários</strong>.</p>
                    <p>Tudo de forma simples e descomplicada! <strong>Basta separar seus resíduos em casa, e nós
                            cuidamos de todo o processo. FAÇA SUA DOAÇÃO AGORA!</strong></p>
                </div>
            </div>
        </div>
    </section>

    <section class="container d-flex justify-content-center flex-column align-items-center">
        <h1 class="fw-bold text-dark px-4 rounded-4">O que pode compostar?</h1>
        <img style="width: 80%;" class="cursor-pointer" src="../img/Pode-ou-não-pode-compostar.jpg" alt="">
    </section>

    <!-- Seção Mapa e Bairros -->
    <section class="container">
        <div class="text-center mt-5">
            <h1 class="fw-bold">Bairros atendidos</h1>
            <p class="lead">Veja abaixo o mapa de coleta, trabalhamos em toda BH e coletamos por região. Basta cadastrar sua doação!</p>
        </div>

        <div class="position-relative w-100">
            <div class="container d-flex justify-content-center">
                <iframe class="rounded-3 shadow-lg"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d240097.52707591024!2d-44.128867424801776!3d-19.902317559052534!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xa690cacacf2c33%3A0x5b35795e3ad23997!2sBelo%20Horizonte%2C%20MG!5e0!3m2!1spt-BR!2sbr!4v1759861744483!5m2!1spt-BR!2sbr"
                    width="90%" height="500" style="border: 4px solid #517C58;" allowfullscreen="true" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

        <div class="container mt-3">
            <div class="row g-3 dias-semana">
                <!-- Cards dos dias -->
                <?php
                $dias = [
                    "Segunda" => "Norte e Venda Nova",
                    "Terça" => "Noroeste e Pampulha",
                    "Quarta" => "Oeste e Barreiro",
                    "Quinta" => "Leste e Nordeste",
                    "Sexta" => "Centro-Sul"
                ];
                foreach ($dias as $dia => $regiao) {
                    echo '<div class="col">
                            <div class="card day-card shadow-sm border-0 h-100">
                                <div class="card-header text-white text-center">
                                    <h1 class="card-title h5 mb-0 fw-bold">' . $dia . '</h1>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-text text-center">' . $regiao . '</h5>
                                </div>
                            </div>
                          </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Produtos Gerados -->
    <section class="container mt-5 rounded-3" id="produtos-gerados" style="background-color: #517C58;">
        <h1 class="pt-4 text-center text-light fw-bold">Produtos Gerados</h1>
        <div class="container px-4 px-lg-6 mt-4">
            <div class="row gx-4 gx-lg-2 row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 justify-content-center">
                <?php
                $produtos = [
                    ["img" => "condicionador-agricultura.png", "nome" => "Condicionador"],
                    ["img" => "composto-organico.png", "nome" => "Composto Orgânico"],
                    ["img" => "biofertilizante-liquido.png", "nome" => "Biofertilizante Líquido"],
                    ["img" => "adubo.png", "nome" => "Adubo Fertilizante"],
                    ["img" => "substrato-para-mudas.png", "nome" => "Substratos para Mudas"]
                ];
                foreach ($produtos as $produto) {
                    echo '<div class="col mb-4">
                            <div class="card product-card h-100 text-center position-relative">
                                <img class="card-img-top p-3" src="../img/' . $produto["img"] . '" alt="' . $produto["nome"] . '">
                                <div class="card-body d-flex flex-column text-start">
                                    <h5 class="card-title">' . $produto["nome"] . '</h5>
                                </div>
                                <div class="position-absolute bottom-0 end-0 p-3">
                                    <i class="bi bi-arrow-right fs-5"></i>
                                </div>
                            </div>
                          </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/doacoes.js"></script>
</body>

</html>
