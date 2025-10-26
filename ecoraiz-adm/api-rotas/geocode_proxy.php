<?php
if (!isset($_GET['endereco'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parâmetro "endereco" é obrigatório.']);
    exit;
}

$endereco = urlencode($_GET['endereco']);
$url = "https://nominatim.openstreetmap.org/search?format=json&q={$endereco}&limit=1";

$opts = [
    "http" => [
        "header" => "User-Agent: ColetaApp/1.0\r\n"
    ]
];
$context = stream_context_create($opts);
$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao buscar coordenadas.']);
    exit;
}

header('Content-Type: application/json');
echo $response;
