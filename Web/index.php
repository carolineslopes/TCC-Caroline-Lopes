<?php
include('login.php'); // Inclui a rotina de login
if(isset($_SESSION['login_user'])){ // Caso haja uma sessão aberta
header("location: profile.php"); // Redireciona para a página inicial do usuário
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Controle de Acesso</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="Estilo.css" rel="stylesheet" type="text/css">
    </head>
    <body style="background-color:#588c7e">
      <div id="login">
      <h2>Controle de Acesso</h2>
      <form action="" method="post">
      <label>Usuário</label>
      <input id="name" name="username" placeholder="Usuário" type="text">
      <br><br>
      <label>Senha</label>
      <input id="password" name="password" placeholder="********" type="password"><br><br>
      <input name="submit" type="submit" value="Entrar">
      <span><?php echo $error; ?></span>
      </form>
      </div>
      <div id="legenda">
        Sistema de gerenciamento de credenciais desenvolvido como parte do Trabalho de Conclusão de Curso da aluna Caroline Siqueira Lopes sob orientação do professor Leandro Bueno.
      </div>
    </body>
</html>