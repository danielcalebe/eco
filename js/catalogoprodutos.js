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
      console.log("Dados recebidos:", data);
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
      const categoria_produto = product.categoria || 0;


    catalog.innerHTML += `
      <div class="col mb-4">
        <div class="card product-card h-100 text-center">
          <img class="card-img-top p-3 img-card" src="../img/produtos/${imagem}" alt="${nome}">
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
