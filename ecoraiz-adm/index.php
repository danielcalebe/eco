<?php
session_start();

// Verifica se o administrador está logado
if (!isset($_SESSION['admin'])) {
  header("Location: login-adm.php");
  exit;
}else {
      header("Location: painel-adm.php");

}
?>