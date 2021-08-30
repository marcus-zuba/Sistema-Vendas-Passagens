<!DOCTYPE html>
<html>
  <head>
    <title>Passagens</title>
    <script src="https://code.jquery.com/jquery-3.3.1.js"
    integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </head>
  <body>

    <?php
      session_start();

      if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?=$_SESSION['msg_type']?>">    
          <?php 
            echo $_SESSION['message'];
            unset($_SESSION['message']);
          ?>
        </div>
    <?php endif ?>

    <div class="container">
    <?php 

      $filtrar = isset($_POST['filtrar']);
      $id = '';

      //Conectar ao banco 
      $str = file_get_contents(__DIR__.'/../../env.json');
      $json = json_decode($str, true);
      $c = oci_connect($json['username'], $json['password'], "bdengcomp_low");
      if (!$c) {
          $m = oci_error();
          $_SESSION['message'] = "Erro! Mensagem de erro: ".$m['message'];
          $_SESSION['msg_type'] = "danger";
          header("location: media_peso_bag.php");
          exit();      
      }

      // Modificar o formato do Timestamp
      $s = oci_parse($c, "alter SESSION set NLS_TIMESTAMP_FORMAT = 'DD-MM-YYYY HH24:MI'");
      if (!$s) {
        $m = oci_error($c);
        $_SESSION['message'] = "Erro! Mensagem de erro: ".$m['message'];
        $_SESSION['msg_type'] = "danger";
        header("location: media_peso_bag.php");
        exit();    
      }

      $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
      if (!$r) {
        $m = oci_error($s);
        $_SESSION['message'] = "Erro! Mensagem de erro: ".$m['message'];
        $_SESSION['msg_type'] = "danger";
        header("location: media_peso_bag.php");
        exit();    
      }

      if (!$filtrar){
        // Fazer o select
        $s = oci_parse($c, "SELECT * from peso_medio_bag");
        if (!$s) {
          $m = oci_error($c);
          $_SESSION['message'] = "Erro! Mensagem de erro: ".$m['message'];
          $_SESSION['msg_type'] = "danger";
          header("location: media_peso_bag.php");
          exit();      
        }
      }else{
        $id = $_POST["id"];
        // Fazer o select
        $s = oci_parse($c, "SELECT CIA_AEREA, AVG(PESO_BAG) MEDIA_PESO_BAG
                            FROM aeroportos_destinos
                            GROUP BY CIA_AEREA
                            HAVING cia_aerea = $id");
        if (!$s) {
          $m = oci_error($c);
          $_SESSION['message'] = "Erro! Mensagem de erro: ".$m['message'];
          $_SESSION['msg_type'] = "danger";
          header("location: media_peso_bag.php");
          exit();      
        }
      }

      $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
      if (!$r) {
        $m = oci_error($s);
        $_SESSION['message'] = "Erro! Mensagem de erro: ".$m['message'];
        $_SESSION['msg_type'] = "danger";
        header("location: media_peso_bag.php");
        exit();    
      }
    ?>

    <!-- Mostrar os dados !-->
    <div class="row justifiy-content-center">
      <table class="table">

        <!-- Cabeçalhos !-->
        <thead>
          <tr>
            <?php 
              $ncols = oci_num_fields($s);              
              for ($i = 1; $i <= $ncols; ++$i) {
                $colname = oci_field_name($s, $i);
                echo "<th>".htmlspecialchars($colname,ENT_QUOTES|ENT_SUBSTITUTE)."</th>\n";                      
              }
            ?>
          </tr>
        </thead>

        <!-- Dados !-->
        <?php
          while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
            echo "<tr>\n";
            foreach ($row as $item) {
              echo "<td>";
              echo $item !== null ? htmlspecialchars($item, ENT_QUOTES|ENT_SUBSTITUTE) : "&nbsp;";
              echo "</td>\n";
            }?>
            <?php
            echo "</tr>\n";
          }
        ?>
      </table>
    </div>
    <div  class="row justify-content-center">
      <form action="media_peso_bag.php" method="POST">
      <div class="form-group">
      <h2>Filtrar CIA Aérea</h2>
      <label>ID CIA Aérea</label>
          <input type="number" name="id" class="form-control"
          value="<?php echo $id; ?>" placeholder="Insira o ID da CIA Aérea">
      </div>
      <div class="form-group">
      <button type="submit" class="btn btn-primary" name="filtrar">Filtrar</button>
      <?php if($filtrar): ?>
          <button type="submit" class="btn btn-info" name="limpar_filtro">Limpar Filtro</button>
        <?php endif; ?>
      </div>
      <div class="form-group">
        <a href="../../index.php" class="btn btn-primary">Retornar</a>
      </div>
      </form>
    </div>
    </div>
  </body>
</html>
