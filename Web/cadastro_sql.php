<?php
  $tag_id = $_POST['tag_id'];
  $matricula = $_POST['matricula'];
  $name = $_POST['name'];
  $coord = $_POST['coord'];
  $perfil = $_POST['perfil'];
  $nivel = (is_numeric($_POST['nivel']) ? (int)$_POST['a'] : 0);
  $email = $_POST['email'];
  //Acesso:
  $sala1 = $_POST['sala1'];
  $sala2 = $_POST['sala2'];
  $sala3 = $_POST['sala3'];
  $inicio1 = $_POST['inicio1'];
  $inicio2 = $_POST['inicio2'];
  $inicio3 = $_POST['inicio3'];
  $fim1 = $_POST['fim1'];
  $fim2 = $_POST['fim2'];
  $fim3 = $_POST['fim3'];
  if (!empty($tag_id) || !empty($matricula) || !empty($name) || !empty($coord) || !empty($perfil) || !empty($nivel) || !empty($email)) {
      $host = "localhost";
      $dbUsername = "debian";
      $dbPassword = "temppwd";
      $dbname = "Registro";
      //create connection
      $conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);
      if (mysqli_connect_error()) {
       die('Connect Error('. mysqli_connect_errno().')'. mysqli_connect_error());
      } else {
       $data_adicao = date('d-m-Y h:i:s');
       $SELECT = "SELECT tag_id From Cadastro Where tag_id = ? Limit 1";
       $INSERT = "INSERT Into Cadastro (tag_id, matricula, nome, coordenadoria, perfil, nivel, email, data_adicao) values(?, ?, ?, ?, ?, ?, ?, ?)";
       $ACESSO = "INSERT Into Acesso (tag_id, area, sala, inicio_vigencia, fim_vigencia, data_adicao) values(?, ?, ?, ?, ?, ?)";
       //Prepare statement
       $stmt = $conn->prepare($SELECT);
       $stmt->bind_param("s", $tag_id);
       $stmt->execute();
       $stmt->bind_result($tag_id);
       $stmt->store_result();
       $rnum = $stmt->num_rows;
       if ($rnum==0) {
        $stmt->close();
        $stmt = $conn->prepare($INSERT);
        $stmt->bind_param("sssssiss", $tag_id, $matricula, $name, $coord, $perfil, $nivel, $email, $data_adicao);
        $stmt->execute();
        if ($sala1=="M204") {
        $stmt->close();
        $stmt = $conn->prepare($ACESSO);
        $stmt->bind_param("ssssss", $tag_id, "BlocoM", "M204", $inicio1, $fim1, $data_adicao);
        $stmt->execute();
       }
       if ($sala2=="M205") {
        $stmt->close();
        $stmt = $conn->prepare($ACESSO);
        $stmt->bind_param("ssssss", $tag_id, "BlocoM", "M205", $inicio2, $fim2, $data_adicao);
        $stmt->execute();
       }
       if ($sala2=="M206") {
        $stmt->close();
        $stmt = $conn->prepare($ACESSO);
        $stmt->bind_param("ssssss", $tag_id, "BlocoM", "M206", $inicio2, $fim2, $data_adicao);
        $stmt->execute();
       }
        echo "Novo cadastro feito com sucesso!";
       } else {
        echo "Alguém já usa essa tag RFID!";
       }
       $stmt->close();
       $conn->close();
      }
  } else {
   echo "Todos os campos devem ser preenchidos";
   die();
  }
  ?>

<!DOCTYPE hmtl>
<html>
  <body>
    <br><br>
    <b id="inicio"><a href="profile.php">Home</a></b>
    <br><br>
    <b id="novocadastro"><a href="cadastro.php">Novo Cadastro</a></b>
    <body> 
</html>
