<?php
header('Content-Type: application/json');
include './db.php'; // ajuste o caminho se necessário

// ======================
// Parâmetros de filtro
// ======================
$search = isset($_GET['search']) ? "%".$_GET['search']."%" : "%%";
$avaliacao = isset($_GET['avaliacao']) ? floatval($_GET['avaliacao']) : 0;
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$preco = isset($_GET['preco']) ? $_GET['preco'] : '';

// ======================
// Construção da query
// ======================
$sql = "SELECT 
            p.id_produto, 
            p.nome_produto AS nome, 
            p.preco, 
            p.qtd_estoque, 
            p.avaliacao, 
                        p.categoria, 

            i.caminho_imagem AS imagem
        FROM produto p
        LEFT JOIN imagem_produto i ON p.id_produto = i.id_produto
        WHERE p.nome_produto LIKE ?";

$params = [$search];
$types = "s";

// Filtro por avaliação
if ($avaliacao > 0) {
    $sql .= " AND p.avaliacao >= ?";
    $params[] = $avaliacao;
    $types .= "d";
}

// Filtro por categoria, se existir
if(!empty($categoria)) {
    $sql .= " AND p.categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

// Filtro por preço
if(!empty($preco)) {
    $range = explode('-', $preco);
    if(count($range) == 2) {
        $sql .= " AND p.preco BETWEEN ? AND ?";
        $params[] = floatval($range[0]);
        $params[] = floatval($range[1]);
        $types .= "dd";
    }
}

$sql .= " ORDER BY p.nome_produto ASC";

// ======================
// Preparar e executar
// ======================
$stmt = $conn->prepare($sql);
if(!$stmt){
    echo json_encode(['error' => 'Erro na preparação da query: '.$conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// ======================
// Montar array final sem duplicidade
// ======================
$produtos = [];
$adicionados = [];

while ($row = $result->fetch_assoc()) {
    // Se já adicionamos este produto, pule
    if (in_array($row['id_produto'], $adicionados)) continue;

    // Se não tiver imagem, define padrão
    if (empty($row['imagem'])) {
        $row['imagem'] = 'sem-imagem.jpg';
    }

    $produtos[] = $row;
    $adicionados[] = $row['id_produto'];
}

// ======================
// Retorna JSON
// ======================
echo json_encode($produtos);
