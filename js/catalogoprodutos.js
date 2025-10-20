const products = [
  {
    title: "Adubo de Milho 10kg Fastpower",
    category: "fertilizantes",
    price: 80,
    rating: 5,
    img: "../img/produto1.png",
    comments: 10
  },
  {
    title: "Produto 2",
    category: "sementes",
    price: 50,
    rating: 4.5,
    img: "../img/produto1.png",
    comments: 8
  },
  {
    title: "Produto 3",
    category: "horta",
    price: 120,
    rating: 4.8,
    img: "../img/produto1.png",
    comments: 12
  },
  {
    title: "Adubo de Milho 10kg Fastpower",
    category: "fertilizantes",
    price: 80,
    rating: 5,
    img: "../img/produto1.png",
    comments: 10
  },
  {
    title: "Produto 2",
    category: "sementes",
    price: 50,
    rating: 4.5,
    img: "../img/produto1.png",
    comments: 8
  },
  {
    title: "Produto 3",
    category: "horta",
    price: 120,
    rating: 4.8,
    img: "../img/produto1.png",
    comments: 12
  },
  {
    title: "Adubo de Milho 10kg Fastpower",
    category: "fertilizantes",
    price: 80,
    rating: 5,
    img: "../img/produto1.png",
    comments: 10
  },
  {
    title: "Produto 2",
    category: "sementes",
    price: 50,
    rating: 4.5,
    img: "../img/produto1.png",
    comments: 8
  },
  {
    title: "Produto 3",
    category: "horta",
    price: 120,
    rating: 4.8,
    img: "../img/produto1.png",
    comments: 12
  },
  {
    title: "Adubo de Milho 10kg Fastpower",
    category: "fertilizantes",
    price: 80,
    rating: 5,
    img: "../img/produto1.png",
    comments: 10
  },
  {
    title: "Produto 2",
    category: "sementes",
    price: 50,
    rating: 4.5,
    img: "../img/produto1.png",
    comments: 8
  },
  {
    title: "Produto 3",
    category: "horta",
    price: 120,
    rating: 4.8,
    img: "../img/produto1.png",
    comments: 12
  },
  {
    title: "Adubo de Milho 10kg Fastpower",
    category: "fertilizantes",
    price: 80,
    rating: 5,
    img: "../img/produto1.png",
    comments: 10
  },
  {
    title: "Produto 2",
    category: "sementes",
    price: 50,
    rating: 4.5,
    img: "../img/produto1.png",
    comments: 8
  },
  {
    title: "Produto 3",
    category: "horta",
    price: 120,
    rating: 4.8,
    img: "../img/produto1.png",
    comments: 12
  },
  {
    title: "Adubo de Milho 10kg Fastpower",
    category: "fertilizantes",
    price: 80,
    rating: 5,
    img: "../img/produto1.png",
    comments: 10
  },
  {
    title: "Produto 2",
    category: "sementes",
    price: 50,
    rating: 4.5,
    img: "../img/produto1.png",
    comments: 8
  },
  {
    title: "Produto 3",
    category: "horta",
    price: 120,
    rating: 4.8,
    img: "../img/produto1.png",
    comments: 12
  },
  // adicione todos os produtos aqui (ou carregue via JSON)
];

let currentPage = 1;
const productsPerPage = 10;

function displayProducts() {
  const search = document.getElementById("searchInput").value.toLowerCase();
  const category = document.getElementById("categoryFilter").value;
  const price = document.getElementById("priceFilter").value;
  const rating = document.getElementById("ratingFilter").value;

  let filtered = products.filter(product => {
    let matchSearch = product.title.toLowerCase().includes(search);
    let matchCategory = category === "" || product.category === category;
    let matchPrice = true;
    if (price) {
      const [min, max] = price.split("-").map(Number);
      matchPrice = product.price >= min && product.price <= max;
    }
    let matchRating = rating === "" || product.rating >= Number(rating);
    return matchSearch && matchCategory && matchPrice && matchRating;
  });

  const totalPages = Math.ceil(filtered.length / productsPerPage);
  if(currentPage > totalPages) currentPage = 1;

  const start = (currentPage - 1) * productsPerPage;
  const end = start + productsPerPage;
  const paginatedProducts = filtered.slice(start, end);

  const catalog = document.getElementById("catalogo").querySelector(".row");
  catalog.innerHTML = "";

  paginatedProducts.forEach(product => {
    catalog.innerHTML += `
      <div class="col mb-4">
        <div class="card product-card h-100 text-center">
          <img class="card-img-top p-3 img-card" src="${product.img}" alt="${product.title}">
          <div class="card-body d-flex flex-column">
            <h6 class="card-title text-start">${product.title}</h6>
            <p class="card-subtitle text-muted mb-2 text-start category-text">${product.category}</p>
            <div class="rating mb-2 text-start">
              <i class="bi bi-star-fill text-warning"></i>
              <small class="text-muted">${product.rating}</small>
              <small class="text-muted">(${product.comments} Comentários)</small>
            </div>
            <h4 class="fw-bold mb-3 text-start montserrat">R$ ${product.price.toFixed(2)}</h4>
            <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
              <a href="detalhesproduto.php" class="btn btn-buy flex-grow-1"><i class="bi bi-cart-plus"></i> Comprar</a>
              <button class="btn btn-outline-secondary btn-favorite"><i class="bi bi-heart"></i></button>
            </div>
          </div>
        </div>
      </div>
    `;
  });

  renderPagination(totalPages);
}

function renderPagination(totalPages) {
  const pagination = document.getElementById("pagination");
  pagination.innerHTML = "";

  for(let i=1; i<=totalPages; i++) {
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

// Adiciona eventos aos filtros
document.getElementById("searchInput").addEventListener("input", () => { currentPage = 1; displayProducts(); });
document.getElementById("categoryFilter").addEventListener("change", () => { currentPage = 1; displayProducts(); });
document.getElementById("priceFilter").addEventListener("change", () => { currentPage = 1; displayProducts(); });
document.getElementById("ratingFilter").addEventListener("change", () => { currentPage = 1; displayProducts(); });

// Inicializa
displayProducts();
