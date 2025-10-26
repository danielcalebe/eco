<?php
$host = getenv('ecoraiz-danielcalebe719-2b82.f.aivencloud.com');      // host do Aiven
$user = getenv('avnadmin');      // usuário do Aiven
$pass = getenv('AVNS_sexjgLJAxB2JrKZ_eQH');      // senha
$db   = getenv('defaultdb');      // nome do banco
$port = getenv('25538');// porta padrão 3306

$mysqli = mysqli_init();

// Se precisar de SSL (recomendado pelo Aiven)
$ssl_ca   = getenv('DB_SSL_CA');
$ssl_cert = getenv('DB_SSL_CERT');
$ssl_key  = getenv('DB_SSL_KEY');

if ($ssl_ca && $ssl_cert && $ssl_key) {
    $mysqli->ssl_set($ssl_key, $ssl_cert, $ssl_ca, null, null);
}

$mysqli->real_connect($host, $user, $pass, $db, $port);

if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

echo "Conexão MySQL Aiven com mysqli bem-sucedida!";
