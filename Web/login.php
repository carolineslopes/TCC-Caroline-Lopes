<?php
session_start(); // Iniciando a sessão
$error = ''; // Variável que armazena a mensagem de erro
if (isset($_POST['submit'])) {
if (empty($_POST['username']) || empty($_POST['password'])) { //Caso um dos campos do login esteja vazio, a mensagem abaixo é exibida
$error = "Usuário ou senha inválido!";
}
else{
// Obtem as informações inseridas no formulário
$username = $_POST['username'];
$password = $_POST['password'];
// Inicia uma nova conexão com o servidor MySQL
$conn = mysqli_connect("localhost", "debian", "temppwd", "Registro");
// Executa a query para buscar se o usuário existe e está autorizado a acessar o sistema
$query = "SELECT usuario, senha from LoginWeb where usuario=? AND senha=? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$stmt->bind_result($username, $password);
$stmt->store_result();
if($stmt->fetch()) 
$_SESSION['login_user'] = $username; // Atribui o nome do usuário a sessão
header("location: profile.php"); // Redireciona para a página inicial do usuário
}
mysqli_close($conn); // Fecha
}
?>
