<?php
$host = 'ecoraiz-danielcalebe719-2b82.f.aivencloud.com';
$port = 25538; // porta do Aiven
$user = 'avnadmin';
$pass = 'AVNS_sexjgLJAxB2JrKZ_eQH';
$db   = 'defaultdb';

$mysqli = mysqli_init();
$mysqli->real_connect($host, $user, $pass, $db, $port);

if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

echo "Conexão MySQL Aiven com mysqli bem-sucedida!";
