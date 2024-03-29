<!DOCTYPE html>
<html>
  <head>
    <title>Companhias Aéreas</title>
    <script src="https://code.jquery.com/jquery-3.3.1.js"
    integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </head>
  <body>
    <?php require_once (__DIR__.'/controllers/companhia_aerea_controller.php'); ?>

    <?php
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
      //Conectar ao banco 
      $str = file_get_contents(__DIR__.'/../env.json');
      $json = json_decode($str, true);
      $c = oci_connect($json['username'], $json['password'], "bdengcomp_low");
      if (!$c) {
          $m = oci_error();
          trigger_error("Não pôde conectar com o banco de dados: ". $m["message"], E_USER_ERROR);
      }

      // Fazer o select
      $s = oci_parse($c, "SELECT * from companhia_aerea");
    
      $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
      if (!$r) {
        $m = oci_error($r);
        trigger_error("Não pôde executar a sentença: ". $m["message"], E_USER_ERROR);
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
            <th colspan="2">Ação</th>
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
            <td> 
              <a href="companhia_aerea.php?editar=<?php echo $row['ID'];?>" class="btn btn-info">Editar</a>
              <a href="controllers/companhia_aerea_controller.php?deletar=<?php echo $row['ID'];?>" class="btn btn-danger">Deletar</a>
            </td>
            <?php
            echo "</tr>\n";
          }
        ?>
      </table>
    </div>

    <div  class="row justify-content-center">
      <form action="controllers/companhia_aerea_controller.php" method="POST">
      <div class="form-group">
        <label>ID</label>
        <?php if($update == true): ?>
          <input type="number" name="id" class="form-control"
          value="<?php echo $id; ?>" placeholder="Insira o ID" readonly="readonly">
        <?php else: ?>
          <input type="number" name="id" class="form-control"
          value="<?php echo $id; ?>" placeholder="Insira o ID">
        <?php endif ?>
      </div>
      <div class="form-group">
        <label>Nome</label>
        <input type="text" name="nome" maxlength="50" class="form-control" 
         value="<?php echo $nome; ?>"placeholder="Insira o Nome">
      </div>
      <div class="form-group">
        <label>Sigla</label>
        <input type="text" name="sigla" maxlength="10" class="form-control" 
         value="<?php echo $sigla; ?>"placeholder="Insira a Sigla">
      </div>
      <div class="form-group">
        <?php if($update == true): ?>
          <button type="submit" class="btn btn-info" name="atualizar">Atualizar</button>
        <?php else: ?>
        <button type="submit" class="btn btn-primary" name="inserir">Inserir</button>
        <?php endif; ?>
      </div>
      <div class="form-group">
        <a href="../index.php" class="btn btn-primary">Retornar</a>
      </div>
      </form>
    </div>
    </div>
  </body>
</html>
