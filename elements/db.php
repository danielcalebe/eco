<?php
// db.php - Conexão mysqli com MySQL do Aiven

$host = 'ecoraiz-danielcalebe719-2b82.f.aivencloud.com';
$port = 25538; // porta do Aiven
$user = 'avnadmin';
$pass = 'AVNS_sexjgLJAxB2JrKZ_eQH';
$db   = 'defaultdb';

// Inicializa e conecta
$mysqli = mysqli_init();
$mysqli->real_connect($host, $user, $pass, $db, $port);

// Checa se houve erro na conexão
if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

// Define charset UTF-8
$mysqli->set_charset("utf8");

// Criar segunda variável $conn apontando para a mesma conexão
$conn = $mysqli;


