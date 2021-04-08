<?php
$tag_id = $_POST['tag_id'];
$username = $_POST['username'];
$password = $_POST['password'];
// $password2 = $_POST['password2'];
$periodo = $_POST['periodo'];
$periodo2 = $_POST['periodo2'];
//$email = $_POST['email'];

if (!empty($tag_id) || !empty($username) || !empty($password) || !empty($periodo) || !empty($periodo2)) {
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
     $SELECT = "SELECT usuario From LoginWeb Where usuario = ? Limit 1";
     $INSERT = "INSERT Into LoginWeb (tag_id, usuario, senha, inicio_vigencia, fim_vigencia, data_adicao) values(?, ?, ?, ?, ?, ?)";
     //Prepare statement
     $stmt = $conn->prepare($SELECT);
     $stmt->bind_param("s", $username);
     $stmt->execute();
     $stmt->bind_result($username);
     $stmt->store_result();
     $rnum = $stmt->num_rows;
     if ($rnum==0) {
      $stmt->close();
      $stmt = $conn->prepare($INSERT);
      $stmt->bind_param("ssssss", $tag_id, $username, $password, $periodo, $periodo2, $data_adicao);
      $stmt->execute();
      echo "Novo usuário adicionado com sucesso!";
     } else {
      echo "Esse usuário já existe!";
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
<b id="novocadastroweb"><a href="cadastroweb.php">Novo Cadastro</a></b>
<body> 
</html>
