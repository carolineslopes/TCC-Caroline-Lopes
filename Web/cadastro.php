<?php
   include('session.php');
   // Caso não haja uma sessão ativa, redireciona para a página de login
   if(!isset($_SESSION['login_user'])){
    header("location: index.php"); 
   }
   if(isset($_GET['id'])){
     $var = $_GET['id']; //
   }
   ?>
<!DOCTYPE html>
<html>
   <head>
      <title>Cadastro de Novos Usuários</title>
      <link href="Estilo.css" rel="stylesheet" type="text/css">
   </head>
   <body style="background-color:#588c7e">
      <div id="top_bar" style="background-color:white; color: black;">
         <b id="welcome">Bem vindo: <i><?php echo $login_session; ?></i></b>
         <b id="logout"><a href="logout.php">Sair</a></b>
      </div>
      <div id= "login" style="width: 50vw; margin-top:2vw;">
         <br><br><b style="text-align:center">Cadastrar Novo Usuário </b></b><br><br>
         <form action="cadastro_sql.php" method="POST">
            <label>Nome Completo:</label><br>
            <input type="text" name="name">
            <br><br>
            <label>Email:</label><br>
            <input type="email" name="email">
            <br><br>
            <label>Matrícula:</label><br>
            <input type="text" name="matricula">
            <br><br>
            <label>Tag ID:</label><br>
            <input type="text" name="tag_id" value=<?php echo $var; ?>>
            <br><br>
            <label>Coordenadoria:</label><br>
            <input type="text" name="coord">
            <br><br>
            <label>Perfil:</label><br>
            <select name="perfil">
               <!-- required -->
               <option selected hidden value="">  </option>
               <option value="aluno">Aluno</option>
               <option value="professor">Professor</option>
               <option value="coordenador">Coordenador</option>
               <option value="convidado">Convidado</option>
            </select>
            <br><br>
            <label>Nível:</label><br>
            <select name="nivel">
               <option selected hidden value="">  </option>
               <option value=1>1</option>
               <option value=2>2</option>
               <option value=3>3</option>
            </select>
            <br><br>
            <label>Autorização de Acesso:</label>
            <table style="width: 40vw; margin-top: 1em;">
               <tr style="padding-top: .1em; height: 2em;">
                  <td>Ambiente</td>
                  <td>Inicio</td>
                  <td>Final</td>
               </tr>
               <tr style="padding-top: .1em; height: 2em;>
                  <td><input type="checkbox" id="sala1" name="sala1" value="M204">
                     <label for="sala1"> Bloco M - Sala M204</label>
                  </td>
                  <td><input type="date" id="inicio1" value=<?php echo date('Y-m-d'); ?>></td>
                  <td><input type="date" id="fim1"></td>
               </tr>
               <tr>
                  <td><input type="checkbox" id="sala2" name="sala2" value="M205">
                     <label for="sala2"> Bloco M - Sala M205</label>
                  </td>
                  <td><input type="date" id="inicio2" value=<?php echo date('Y-m-d'); ?>></td>
                  <td><input type="date" id="fim2"></td>
               </tr>
               <tr>
                  <td><input type="checkbox" id="sala3" name="sala3" value="M206">
                     <label for="sala3"> Bloco M - Sala M206</label>
                  </td>
                  <td><input type="date" id="inicio3" value=<?php echo date('Y-m-d'); ?>></td>
                  <td><input type="date" id="fim3"></td>
               </tr>
            </table>
            <br><br>
            <table style="width: 45vw;">
               <tr>
                  <td><button type="reset" value="Reset">Reset</button></td>
                  <td><button id="cancel"><c href="profile.php">Cancelar</c></button></td> 
                  <td><input type="submit" style="width: 90%;" value="Cadastrar"></td>
               </tr>
            </table>
         </form>
      </div>
   </body>
</html>
