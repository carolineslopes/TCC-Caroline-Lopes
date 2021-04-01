<?php
  include('session.php');
  if(!isset($_SESSION['login_user'])){
    header("location: index.php"); // Redirecting To Home Page
  }
?>


<!DOCTYPE html>
<html>
  
  <head>
    <title>Página Inicial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="Estilo.css" >
    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <script>
    $(document).ready(function() {
      $('#myTable').DataTable();
    });
    </script>
    
  </head>
  
  <body>
    <div id="top_bar">
      <b id="welcome">Bem vindo: <i><?php echo $login_session; ?></i></b>
      <a id="logout" href="logout.php">Sair</a>
    </div>
    
    <div class="tab">
      <button class="tablinks" onclick="openTab(event, 'Acessos')" id="defaultOpen">Acessos</button>
      <button class="tablinks" onclick="openTab(event, 'Usuarios')">Usuários</button>
      <button class="tablinks" onclick="openTab(event, 'Sistema')">Web</button>
    </div>
    
    <h2>Sistema de Gerenciamento de Controle de Acesso</h2>
  
    <div id="Acessos" class="tabcontent">
      <button2 id="cadastro"><a href="cadastro.php">Cadastrar Novo Usuário</a></button2>            
      <button2 id="atualizar"><a href="profile.php">Atualizar</a></button2>  
      <br><br>
      <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Procurar ID">
      <br><br>
      <table id="myTable">
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Horário</th>
          <th>Área</th>
          <th>Sala</th>
          <th>Acesso</th>
          <th>Porta</th>
          <th>Cadastrar?</th>
        </tr>
        <?php
          $conn = mysqli_connect("localhost", "debian", "temppwd", "Registro");
          if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
          }
          $sql = "SELECT hora, RegistroLog.tag_id AS registro_tag, Cadastro.nome, area, sala, acesso, entrada, Cadastro.tag_id AS cadastro_tag FROM RegistroLog LEFT JOIN Cadastro ON RegistroLog.tag_id = Cadastro.tag_id ORDER BY num DESC;";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                if ($row["acesso"] == "0" || $row["acesso"] == "1"){
                  $texto = "LIBERADO";
                }elseif ($row["acesso"] == "2" || $row["acesso"] == "3"){
                  $texto = "NEGADO";
                }
                
                if ($row["entrada"] == 1){
                  $porta = "Aberta";
                }elseif ($row["entrada"] == 0){
                  $porta = "Fechada";
                }
                
                  echo "<tr><td>" . $row["registro_tag"] . 
                  "</td><td>" . $row["nome"] .
                  "</td><td>" . $row["hora"] .
                  "</td><td>" . $row["area"]. 
                  "</td><td>" . $row["sala"] . "</td>";

                  echo $texto == "LIBERADO" ? '</td><td style="color:green"> LIBERADO' : '</td><td style="color:red"> NEGADO';
                  
                  echo "</td><td>" . $porta . "</td>"; //$row["entrada"]. "</td>";
                  echo "<td>";
                  echo $row['cadastro_tag'] == NULL ? '<a href=cadastro.php?id='. $row["registro_tag"] .'>Cadastro</a>' : ' ';
                  echo "</td></tr>";
              }
              echo "</table>";
          } else { echo "0 results"; }
          $conn->close();
          ?>
      </table>
    </div>
    
    <div id="Usuarios" class="tabcontent">
      <button2 id="cadastro"><a href="cadastro.php">Cadastrar Novo Usuário</a></button2>            
      <button2 id="relatorio"><a href="relatorios.php">Relatórios</a></button2>
      <button2 id="atualizar"><a href="profile.php">Atualizar</a></button2>  
      <br><br>
      <table>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Matrícula</th>
          <th>Coordenadoria</th>
          <th>Perfil</th>
          <th>Nivel</th>
          <th>Acesso</th>
          <th>Vigência</th>
          <th>Editar/Excluir</th>
        </tr>
        <?php
          $conn = mysqli_connect("localhost", "debian", "temppwd", "Registro");
          if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
          }
          $sql = "SELECT Cadastro.tag_id AS cadastro_tag, nome, email, matricula, coordenadoria, perfil, nivel, Acesso.tag_id AS acesso_tag, 
                  GROUP_CONCAT(Acesso.area, ' ', Acesso.sala SEPARATOR '<br>') AS local,
                  GROUP_CONCAT(SUBSTRING(Acesso.inicio_vigencia,1,10), ' / ', SUBSTRING(Acesso.fim_vigencia,1,10) SEPARATOR '<br>') AS tempo
                  FROM Cadastro 
                  LEFT JOIN Acesso 
                  ON Acesso.tag_id = Cadastro.tag_id 
                  GROUP BY Cadastro.tag_id;";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo "<tr><td>" . $row["cadastro_tag"] . 
                  "</td><td>" . $row["nome"] .
                  "</td><td>" . $row["email"] .
                  "</td><td>" . $row["matricula"]. 
                  "</td><td>" . $row["coordenadoria"] . 
                  "</td><td>" . $row["perfil"] . 
                  "</td><td>" . $row["nivel"]. "</td>";
                  echo "<td>";
                  echo $row['acesso_tag'] == NULL ? ' ' : $row["local"] ;
                  echo "</td><td>";
                  echo $row['acesso_tag'] == NULL ? ' ' : $row["tempo"] ;
                  echo "</td></tr>";
              }
              echo "</table>";
          } else { echo "0 results"; }
          $conn->close();
          ?>
      </table>
    </div>
    
    <div id="Sistema" class="tabcontent">
      <button2 id="cadastroweb"><a href="cadastroweb.php">Novo Usuário Web</a></button2>
      <br><br>
      <table>
        <tr>
          <th>ID</th>
          <th>Usuário</th>
          <th>Senha</th>
          <th>Vigência</th>
          <th>Editar</th>
        </tr>
        <?php
          $conn = mysqli_connect("localhost", "debian", "temppwd", "Registro");
          if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
          }
          $sql = "SELECT tag_id, usuario, senha, 
                  GROUP_CONCAT(SUBSTRING(inicio_vigencia,1,10), ' / ', SUBSTRING(fim_vigencia,1,10) SEPARATOR '<br>') AS tempo
                  FROM LoginWeb;";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo "<tr><td>" . $row["tag_id"] . 
                  "</td><td>" . $row["usuario"] .
                  "</td><td>" . $row["senha"] .
                  "</td><td>" . $row["tempo"] . "</td>";
                  echo "<td><a href=cadastroweb.php?id=" . $row["tag_id"] . 
                  "&usu=". $row["usuario"] . 
                  "&pwd=". $row["senha"] . 
                  "&tmp=". $row["tempo"] . ">Editar</a></td></tr>";
              }
              echo "</table>";
          } else { echo "0 results"; }
          $conn->close();
          ?>
      </table>
    </div>
    
    <script>
      function myFunction() {
        // Declare variables
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
      
        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }
        }
      }
      function openTab(evt, cityName) {
        // Declare all variables
        var i, tabcontent, tablinks;
      
        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
          tabcontent[i].style.display = "none";
        }
      
        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
          tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
      
        // Show the current tab, and add an "active" class to the link that opened the tab
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
      }
    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();
    </script>
  </body>
</html>
