<?php
// Inicia uma nova conexão com o servidor MySQL
$conn = mysqli_connect("localhost", "debian", "temppwd", "Registro");
session_start();// // Iniciando a sessão
// Armazenando a sessão
$user_check = $_SESSION['login_user'];
// Executa a query para buscar as informações do usuário existe e está autorizado a acessar o sistema
$query = "SELECT usuario from LoginWeb where usuario = '$user_check'";
$ses_sql = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($ses_sql);
$login_session = $row['username'];
?>
