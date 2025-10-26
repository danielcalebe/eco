<?php
// Arquivo: db.php
// Conexão com o banco de dados MySQL

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "ecoraiz";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("❌ Falha na conexão: " . $conn->connect_error);
}

// Define charset para evitar erros com acentuação
$conn->set_charset("utf8mb4");
?>
