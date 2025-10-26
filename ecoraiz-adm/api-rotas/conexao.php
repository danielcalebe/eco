<?php
$host = 'localhost';      // Servidor do banco (padrão no Laragon)
$usuario = 'root';        // Usuário padrão do MySQL no Laragon
$senha = '';              // Senha (em branco por padrão no Laragon)
$banco = 'ecoraiz';       // Nome do banco de dados

// Cria a conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}else{
}

// Caso queira verificação de sucesso
// echo "Conexão bem-sucedida!";
?>
