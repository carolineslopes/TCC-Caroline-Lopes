
<?php
  include('session.php');
  if(!isset($_SESSION['login_user'])){
    header("location: index.php"); // Redireciona para página de login
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Página Inicial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="Estilo.css" >
    <link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.min.css" >
    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.min.css">
    <script language="javascript" type="text/javascript" src="/jquery/jquery.js"></script>
    <script src="/DataTables/Buttons-1.6.5/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="/DataTables/datatables.min.js"></script>
    <script>
    $(document).ready(function() {
      
    // Adiciona filtro para cada coluna
    $('#myTable1 thead tr').clone(true).appendTo( '#myTable1 thead' );
    $('#myTable1 thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Buscar" />' );
        //$(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );
    var table1 = $('#myTable1').DataTable( {
      "pageLength": 20,
        "language": {
            "order": [[ 3, "desc" ]],
            "search": "Busca Geral:",
            "paginate": {"previous": "Anterior", "next": "Próxima"},
            "lengthMenu": "Exibindo _MENU_ registros por página",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Página _PAGE_ de _PAGES_",
            "infoEmpty": "Não há registros a serem exibidos",
            "infoFiltered": "(filtrado do total de _MAX_ registros)" },
        //dom: 'Bfrtip',
        dom: "<'row'<'col-md-6'B><'col-md-6'f>><'row'<'col-md-12't>><'row'<'col-md-6'i><'col-md-6'p>>",
        lengthChange: false,
        buttons: [ 
          {extend: 'csv', className: 'btn-success', exportOptions: {columns: ':visible'}},
          {extend: 'excel', className: 'btn-success', exportOptions: {columns: ':visible'}},
          {extend: 'pdf', className: 'btn-success', exportOptions: {columns: ':visible'}},
          {extend: 'print', className: 'btn-success', exportOptions: {columns: ':visible'}, text:'Imprimir'},
          {extend: 'colvis', className: 'btn-success', text:'Colunas Vísiveis'}],
          columnDefs: [ {
            targets: 0,
            visible: false
        } ],
        orderCellsTop: true,
        fixedHeader: true
    } );
    
    // Adiciona filtro para cada coluna
    $('#myTable2 thead tr').clone(true).appendTo( '#myTable2 thead' );
    $('#myTable2 thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Buscar" />' );
        //$(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );
    var table2 = $('#myTable2').DataTable( {
      "pageLength": 20,
        "language": {
            "order": [[ 3, "desc" ]],
            "search": "Busca Geral:",
            "paginate": {"previous": "Anterior", "next": "Próxima"},
            "lengthMenu": "Exibindo _MENU_ registros por página",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Página _PAGE_ de _PAGES_",
            "infoEmpty": "Não há registros a serem exibidos",
            "infoFiltered": "(filtrado do total de _MAX_ registros)" },
        //dom: 'Bfrtip',
        dom: "<'row'<'col-md-6'B><'col-md-6'f>><'row'<'col-md-12't>><'row'<'col-md-6'i><'col-md-6'p>>",
        lengthChange: false,
        buttons: [ 
          {extend: 'csv', className: 'btn-success', exportOptions: {columns: ':visible'}},
          {extend: 'excel', className: 'btn-success', exportOptions: {columns: ':visible'}},
          {extend: 'pdf', className: 'btn-success', exportOptions: {columns: ':visible'}},
          {extend: 'print', className: 'btn-success', exportOptions: {columns: ':visible'}, text:'Imprimir'},
          {extend: 'colvis', className: 'btn-success', text:'Colunas Vísiveis'}],
          columnDefs: [ {
            targets: 0,
            visible: false
        } ],
        orderCellsTop: true,
        fixedHeader: true
    } );

    
    } );
    </script>
    <style>
   .btn-success {
      width: 100px;
      padding: 5px;
      font-size: 12px;
   }
   
   thead input {
        width: 60%;
        height:20px;
        color:black;
    }
</style>
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
      <button class="tablinks" onclick="openTab(event, 'Ambientes')">Ambientes</button>
    </div>
    
    <h2>Sistema de Gerenciamento de Controle de Acesso</h2>
<!---------------------------------------------------------------------------------------->  
    <div id="Acessos" class="tabcontent">
      <button2 id="cadastro"><a href="cadastro.php">Cadastrar Novo Usuário</a></button2>            
      <button2 id="atualizar"><a href="profile.php">Atualizar</a></button2>  
      <br><br>
      
      <table id="myTable1" class="display nowrap">
        <thead>
          <tr style="height:40px">
            <th>ID</th>
            <th>Nome</th>
            <th>Horário</th>
            <th>Área</th>
            <th>Sala</th>
            <th>Acesso</th>
            <th>Porta</th>
            <th>Cadastrar?</th>
          </tr>
        </thead>
        <tbody>
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
          </tbody>
          <tfoot>
        </tfoot>
      </table>
    </div>
    
<!---------------------------------------------------------------------------------------->
    <div id="Usuarios" class="tabcontent">
      <button2 id="cadastro"><a href="cadastro.php">Cadastrar Novo Usuário</a></button2>            
      <button2 id="relatorio"><a href="relatorios.php">Relatórios</a></button2>
      <button2 id="atualizar"><a href="profile.php">Atualizar</a></button2>  
      <br><br>
      
      <table id="myTable2" class="display" >
        <thead>
        <tr style="height:40px">
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
        </thead>
        <tbody>
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
          </tbody>
          <tfoot>
        </tfoot>
      </table>
    </div>

<!---------------------------------------------------------------------------------------->
    <div id="Sistema" class="tabcontent">
      <button2 id="cadastroweb"><a href="cadastroweb.php">Novo Usuário Web</a></button2>
      <br><br>
      <table id="myTable3" class="display nowrap">
        <thead>
          <tr style="height:40px">
          <th>ID</th>
          <th>Usuário</th>
          <th>Senha</th>
          <th>Vigência</th>
          <th>Editar</th>
        </tr>
        </thead>
        <tbody>
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
          </tbody>
          <tfoot>
        </tfoot>
      </table>
    </div>
    
    <div id="Ambientes" class="tabcontent">
      
    </div>
    
    <script>
      function openTab(evt, tabName) {
        
        var i, tabcontent, tablinks;
      
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
          tabcontent[i].style.display = "none";
        }
      
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
          tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
      }

    document.getElementById("defaultOpen").click();
    
    </script>
  </body>
</html>
