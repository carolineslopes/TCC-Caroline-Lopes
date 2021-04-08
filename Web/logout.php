<?php
session_start(); // Inicia a sessão
unset($_SESSION['login_user']); // Libera a variável que armazena o usuário atual
if(!isset($_SESSION["login_user"])){
header("location: index.php"); // Redireciona para a página de login
}
?>
