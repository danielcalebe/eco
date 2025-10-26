<?php
// (Opcional) Controle de sessão
// session_start();
// if (!isset($_SESSION['usuario_logado'])) {
//     header("Location: login.php");
//     exit();
// }


// Consulta todas as categorias distintas no banco

include "./db.php";
$stmt_cat = $conn->prepare("SELECT DISTINCT categoria FROM produto ORDER BY categoria ASC");
$stmt_cat->execute();
$result_cat = $stmt_cat->get_result();
$categorias = $result_cat->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Catálogo de Produtos - EcoRaiz</title>
  <?php include '../elements/head.php'; ?>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/catalogoprodutos.css">
  <link href="../css/style.css" rel="stylesheet" />
</head>

<body>
  <!-- NAVBAR -->
    <?php include '../elements/navbar.php'; ?>


  <!-- Header com Banner -->
  <header class="px-lg-5 mt-lg-4">
    <div id="ecoCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active"><img src="../img/1.png" class="d-block w-100" style="max-height: 500px; object-fit: cover;" alt="Banner 1"></div>
        <div class="carousel-item"><img src="../img/2.png" class="d-block w-100" style="max-height: 500px; object-fit: cover;" alt="Banner 2"></div>
        <div class="carousel-item"><img src="../img/3.png" class="d-block w-100" style="max-height: 500px; object-fit: cover;" alt="Banner 3"></div>
      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#ecoCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span><span class="visually-hidden">Anterior</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#ecoCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span><span class="visually-hidden">Próximo</span>
      </button>

      <div class="carousel-indicators">
        <button type="button" data-bs-target="#ecoCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#ecoCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#ecoCarousel" data-bs-slide-to="2"></button>
      </div>
    </div>
  </header>

  <!-- Filtros -->
  <section class="mt-5">
    <div class="container px-4">
      <div class="row g-3 align-items-center">
        <div class="col-md-4">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar produtos...">
          </div>
        </div>



<div class="col-md-3">
  <div class="input-group">
    <span class="input-group-text"><i class="bi bi-tags"></i></span>
    <select class="form-select" id="categoryFilter">
      <option value="">Todas as categorias</option>
      <?php foreach ($categorias as $cat): ?>
        <option value="<?= htmlspecialchars($cat['categoria']) ?>"><?= htmlspecialchars(ucfirst($cat['categoria'])) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>


        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
            <select class="form-select" id="priceFilter">
              <option value="">Todos os preços</option>
              <option value="0-50">Até R$ 50</option>
              <option value="51-100">R$ 51 - R$ 100</option>
              <option value="101-200">R$ 101 - R$ 200</option>
              <option value="201-9999">Acima de R$ 200</option>
            </select>
          </div>
        </div>

        <div class="col-md-2">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-star-fill"></i></span>
            <select class="form-select" id="ratingFilter">
              <option value="">Todas as avaliações</option>
              <option value="5">5 estrelas</option>
              <option value="4">4 estrelas ou mais</option>
              <option value="3">3 estrelas ou mais</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Catálogo -->
  <section id="catalogo">
    <div class="container px-4 mt-4">
      <div class="row gx-4 row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 justify-content-center">
        <!-- Produtos serão carregados via JS -->
      </div>
    </div>
  </section>

  <!-- Paginação -->
  <div class="d-flex justify-content-center mt-4">
    <nav><ul class="pagination" id="pagination"></ul></nav>
  </div>

  <!-- Footer -->
  <footer class="text-center text-lg-start" style="background-color: #F2F7EC;">
    <div class="container p-4">
      <section class="">
        <div class="row">
          <div class="col-md-3 mx-auto mt-3">
            <h1 class="text-uppercase mb-4 font-weight-bold">
              Ecoraiz
              <img src="../img/logo.png" alt="" width="50px">
            </h1>
            <p>Na EcoRaiz, oferecemos produtos naturais e sustentáveis, cuidando do meio ambiente e do seu bem-estar.</p>
          </div>

          <div class="col-md-2 mx-auto mt-3">
            <h6 class="text-uppercase mb-4 font-weight-bold">Produtos</h6>
            <p>Fertilizantes</p>
            <p>Equipamentos</p>
            <p>Pecuária</p>
            <p>Horta</p>
          </div>

          <div class="col-md-3 mx-auto mt-3">
            <h6 class="text-uppercase mb-4 font-weight-bold">Links Úteis</h6>
            <p>Sua conta</p>
            <p>Doe agora</p>
            <p>Compre na loja</p>
            <p>Ajuda</p>
          </div>

          <div class="col-md-4 mx-auto mt-3">
            <h6 class="text-uppercase mb-4 font-weight-bold">Contato</h6>
            <p>Belo Horizonte - MG</p>
            <p>ecoraiz@contato.com</p>
            <p>+55 31 3234-5675</p>
          </div>
        </div>
      </section>
      <hr class="my-3">
      <section class="p-3 pt-0">
        <div class="row d-flex align-items-center">
          <div class="col-md-7 text-center text-md-start">
            <div class="p-3">© 2025 Copyright: <a href="#">www.ecoraiz.com</a></div>
          </div>
          <div class="col-md-5 text-center text-md-end">
            <a class="btn btn-outline-light btn-floating m-1"><i class="bi bi-facebook"></i></a>
            <a class="btn btn-outline-light btn-floating m-1"><i class="bi bi-instagram"></i></a>
          </div>
        </div>
      </section>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Script do catálogo -->
  <script>
    let currentPage = 1;
const productsPerPage = 10;
let allProducts = [];

// Carrega produtos do servidor PHP
async function loadProducts() {
  try {
    console.log("Iniciando carregamento de produtos...");
    const search = document.getElementById("searchInput").value;
    const category = document.getElementById("categoryFilter").value;
    const price = document.getElementById("priceFilter").value;
    const rating = document.getElementById("ratingFilter").value;

    const params = new URLSearchParams({ search, categoria: category, preco: price, avaliacao: rating });
    console.log("Carregando produtos com parâmetros:", Object.fromEntries(params));

const response = await fetch(`get_produtos.php?${params}`);
      const data = await response.json();
      
    console.log("Resposta do servidor:", data);

    if (!Array.isArray(data)) {
      console.error("Erro: resposta não é um array", data);
      allProducts = [];
    } else {
      allProducts = data;
    }

    currentPage = 1;
    displayProducts();
  } catch (err) {
    console.error("Erro ao carregar produtos:", err);
  }
}


// Exibe os produtos da página atual
function displayProducts() {
  const totalPages = Math.ceil(allProducts.length / productsPerPage);
  const start = (currentPage - 1) * productsPerPage;
  const end = start + productsPerPage;
  const paginated = allProducts.slice(start, end);

  const catalog = document.querySelector("#catalogo .row");
  catalog.innerHTML = "";

  if (paginated.length === 0) {
    catalog.innerHTML = `<p class="text-center text-muted">Nenhum produto encontrado.</p>`;
    return;
  }

  paginated.forEach(product => {
    // Garantir valores válidos
    const preco = parseFloat(product.preco) || 0;
    const nome = product.nome || "Produto sem nome";
    const imagem = product.imagem || "sem-imagem.jpg";
    const avaliacao = product.avaliacao || 0;
    const categoria_produto = product.categoria || "";

    catalog.innerHTML += `
      <div class="col mb-4">
        <div class="card product-card h-100 text-center">
          <img class="card-img-top p-3 img-card" src="../ecoraiz-adm/img/Produtos/${imagem}" alt="${nome}">
          <div class="card-body d-flex flex-column">
            <h6 class="card-title text-start">${nome}</h6>
            <p class="card-subtitle text-muted mb-2 text-start">${categoria_produto}</p>
            <div class="rating mb-2 text-start">
              <i class="bi bi-star-fill text-warning"></i>
              <small class="text-muted">${avaliacao}</small>
            </div>
            <h4 class="fw-bold mb-3 text-start montserrat">R$ ${preco.toFixed(2)}</h4>
            <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
              <a href="detalhesproduto.php?id=${product.id_produto}" class="btn btn-buy flex-grow-1">
                <i class="bi bi-cart-plus"></i> Comprar
              </a>
              <button class="btn btn-outline-secondary btn-favorite">
                <i class="bi bi-heart"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
  });

  renderPagination(totalPages);
}

// Paginação
function renderPagination(totalPages) {
  const pagination = document.getElementById("pagination");
  pagination.innerHTML = "";

  for (let i = 1; i <= totalPages; i++) {
    pagination.innerHTML += `
      <li class="page-item ${i === currentPage ? "active" : ""}">
        <a class="page-link" href="#">${i}</a>
      </li>
    `;
  }

  document.querySelectorAll(".page-link").forEach((link, index) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      currentPage = index + 1;
      displayProducts();
    });
  });
}

// Eventos de filtro
document.getElementById("searchInput").addEventListener("input", loadProducts);
document.getElementById("categoryFilter").addEventListener("change", loadProducts);
document.getElementById("priceFilter").addEventListener("change", loadProducts);
document.getElementById("ratingFilter").addEventListener("change", loadProducts);

// Inicializa
loadProducts();

  </script>
</body>
</html>
