<?php
include 'conexao.php';

// ==========================
// 🔹 1. Inserir dados de teste (se não existirem)
// ==========================
$usuarioTeste = "teste@exemplo.local";
$checkUser = $conn->query("SELECT id_usuario FROM usuario WHERE email = '$usuarioTeste'");
if ($checkUser->num_rows === 0) {
    $conn->query("
        INSERT INTO usuario (nome, email, senha, tipo_usuario, telefone)
        VALUES ('Usuário Teste', '$usuarioTeste', 'senha_teste', 'PF', '(31)99999-0001')
    ");
}
$userRow = $conn->query("SELECT id_usuario FROM usuario WHERE email = '$usuarioTeste'")->fetch_assoc();
$idUsuario = $userRow['id_usuario'];

// ==========================
// 🔹 2. Inserir doações de teste (para o dia atual)
// ==========================
$hoje = date('Y-m-d');

$checkDoacao = $conn->query("SELECT COUNT(*) AS total FROM doacao WHERE data_coleta = '$hoje'");
$row = $checkDoacao->fetch_assoc();


// ==========================
// 🔹 3. Lógica de seleção automática por dia
// ==========================
$diaSemana = date('N');
$cronograma = [
    1 => ['Norte', 'Venda Nova'],
    2 => ['Noroeste', 'Pampulha'],
    3 => ['Oeste', 'Barreiro'],
    4 => ['Leste', 'Nordeste'],
    5 => ['Centro-Sul']
];

if (!isset($cronograma[$diaSemana])) {
    echo "<p>Não há coleta programada para hoje.</p>";
    exit;
}
$regioes = $cronograma[$diaSemana];
$escaped = array_map(fn($r) => $conn->real_escape_string($r), $regioes);


$sql = "SELECT endereco_coleta 
        FROM doacao 
        WHERE regiao IN ('" . implode("','", $escaped) . "') 
        AND status != 'Cancelada'
        AND data_coleta = '$hoje'";
        
$result = $conn->query($sql);
$enderecos = [];
while ($row = $result->fetch_assoc()) {
    if (!empty($row['endereco_coleta'])) $enderecos[] = $row['endereco_coleta'];
}

if (empty($enderecos)) {
    echo "<p>Nenhuma coleta programada para hoje nas regiões: " . implode(', ', $regioes) . ".</p>";
    exit;
}

// Adiciona ponto inicial e final
array_unshift($enderecos, "Praça Sete de Setembro, - Centro, Belo Horizonte - MG");
$enderecos[] = "Praça Sete de Setembro, - Centro, Belo Horizonte - MG";
$enderecosJSON = json_encode($enderecos, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rota de Coletas do Dia</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"/>

<style>
  :root {
    --primary: #1E5E2E;
    --bg: #f4f7f1;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    font-family: "Segoe UI", Roboto, sans-serif;
    background: var(--bg);
    display: flex;
    flex-direction: column;
    height: 100vh;
  }
  header {
    background: var(--primary);
    color: white;
    padding: 12px;
    text-align: center;
    font-size: 1.1rem;
  }
  .info {
    background: #ffffff;
    text-align: center;
    padding: 8px;
    font-size: 0.95rem;
    border-bottom: 1px solid #ddd;
  }
  #map {
    flex-grow: 1;
    width: 100%;
  }
  @media (max-width: 768px) {
    header { font-size: 1rem; padding: 10px; }
    .info { font-size: 0.9rem; padding: 6px; }
  }

  /* PRELOADER */
  #preloader {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(255,255,255,0.9);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    transition: opacity 0.4s ease;
  }
  .loader {
    border: 5px solid #ddd;
    border-top: 5px solid var(--primary);
    border-radius: 50%;
    width: 55px;
    height: 55px;
    animation: spin 1s linear infinite;
  }
  @keyframes spin { 100% { transform: rotate(360deg); } }
  #preloader p {
    margin-top: 15px;
    font-size: 1rem;
    color: #333;
  }
</style>
</head>
<body>
  <div style="display:flex;justify-content:center;align-items:center;">
    <a href="../painel-adm.php"><img src="logo.png" width="50" alt=""></a>
  </div>
<header>Rota de Coletas de Hoje<br><small><?= htmlspecialchars(implode(', ', $regioes)) ?></small></header>
<div class="info">Total de paradas: <strong id="totalStops">0</strong></div>

<!-- PRELOADER -->
<div id="preloader">
  <div class="loader"></div>
  <p>Carregando rota...</p>
</div>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
const enderecos = <?= $enderecosJSON ?>;
document.getElementById('totalStops').innerText = enderecos.length;

const map = L.map('map').setView([-19.9167, -43.9345], 11);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Função de geocodificação via proxy
async function geocode(endereco) {
  try {
    const response = await fetch(`geocode_proxy.php?endereco=${encodeURIComponent(endereco)}`);
    const data = await response.json();
    return data.length > 0 ? [parseFloat(data[0].lat), parseFloat(data[0].lon)] : null;
  } catch (e) {
    console.error("Erro no geocode:", e);
    return null;
  }
}

async function gerarRota() {
  const preloader = document.getElementById('preloader');
  preloader.style.display = 'flex';

  const waypoints = [];
  const foundAddresses = [];

  for (let i = 0; i < enderecos.length; i++) {
    const e = enderecos[i];
    if (i > 0) await new Promise(r => setTimeout(r, 200)); 
    const ponto = await geocode(e);
    if (ponto) {
      waypoints.push(ponto);
      foundAddresses.push(e);
    }
  }

  if (waypoints.length < 2) {
    preloader.remove();
    alert("Não há endereços suficientes para traçar a rota.");
    return;
  }

  document.getElementById('totalStops').innerText = foundAddresses.length;

  const routing = L.Routing.control({
    waypoints: waypoints.map(p => L.latLng(p[0], p[1])),
    router: L.Routing.osrmv1({ serviceUrl: 'https://router.project-osrm.org/route/v1' }),
    routeWhileDragging: false,
    showAlternatives: false,
    collapsible: true,
    lineOptions: { styles: [{ opacity: 0.8, weight: 6 }] },
    createMarker: function(i, wp) {
      const label = foundAddresses[i] || `Parada ${i + 1}`;
      return L.marker(wp.latLng).bindPopup(`<b>Parada ${i + 1}</b><br>${label}`);
    }
  }).addTo(map);

  routing.on('routesfound', () => {
    preloader.style.opacity = '0';
    setTimeout(() => preloader.remove(), 400);
  });
}

gerarRota();
</script>
</body>
</html>
