<?php
include('session.php');
if(!isset($_SESSION['login_user'])){
 header("location: index.php"); // Redirecting To Home Page
}
if(isset($_GET['id']) && isset($_GET['usu']) && isset($_GET['pwd']) && isset($_GET['tmp'])){
  $id = $_GET['id'];
  $usu = $_GET['usu'];
  $pwd = $_GET['pwd'];
  $aux = $_GET['tmp'];
  $tmp = substr($aux, 0, 10);
  $tmp2 = substr($aux, -1, 10);
  
  $titulo = "Editar Usuário Web";
  $botao = "Atualizar";
}else{
  $titulo = "Novo Usuário Web";
  $botao = "Cadastrar";
}
?>

<!DOCTYPE html>
<html>
  
  <head>
    <title><?php echo $titulo; ?></title
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="Estilo.css" rel="stylesheet" type="text/css">
  </head>
  
  <body>
    
    <div id="top_bar">
      <b id="welcome">Bem vindo: <i><?php echo $login_session; ?></i></b>
      <b id="logout"><a href="logout.php">Sair</a></b>
    </div>
    <br>
    <h3 style=text-align:center><?php echo $titulo; ?></h3><br>
    <div id="login">
    <form action="cadastroweb_sql.php" method="POST">
      
          <label>Tag ID:</label>
          <input type="text" name="tag_id" value=<?php echo $id; ?>><br><br>
        
          <!--<label>Email:</label>-->
          <!--<input type="email" name="email"><br><br>-->
        
          <label>Usuário:</label>
          <input type="text" name="username" value=<?php echo $usu; ?>><br><br>
        
          <label>Senha:</label>
          <input type="text" name="password"  value=<?php echo $pwd; ?>><br><br>
          
          <!--<label>Confirmar senha:</label>-->
          <!--<input type="password" name="password2"><br><br>-->
        
          <label for="periodo">Inicio Vigência:</label>
          <input type="datetime-local" id="periodo" name="periodo"  value=<?php echo $tmp; ?>><br><br>
          
          <label for="periodo2">Fim Vigência:</label>
          <input type="datetime-local" id="periodo2" name="periodo2"  value=<?php echo $t; ?>><br><br>
        
          <input type="submit" value=<?php echo $botao; ?>>
        
    </form>
    </div>
  </body>
</html>
