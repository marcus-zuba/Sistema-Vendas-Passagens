<?php
$str = file_get_contents(__DIR__.'/../../env.json');
$json = json_decode($str, true);

$c = oci_connect($json['username'], $json['password'], "bdengcomp_low");
if (!$c) {
    $m = oci_error();
    trigger_error("Não pôde conectar com o banco de dados: ". $m["message"], E_USER_ERROR);
}

session_start();

$update = false;
$id = '';
$nome = '';
$sigla = '';


if (isset($_POST['inserir'])){
  $id = $_POST['id'];
  $nome = $_POST['nome'];
  $sigla = $_POST['sigla'];

  //Inserir os dados
  $s = oci_parse($c, "INSERT INTO companhia_aerea VALUES ('$id', '$nome', '$sigla')");
  if (!$s) {
      $m = oci_error($c);
      trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($r);
    trigger_error("Não pôde executar a sentença: ". $m["message"], E_USER_ERROR);
  }

  oci_commit($c);

  $_SESSION['message'] = "Companhia aérea inserida!";
  $_SESSION['msg_type'] = "success";

  header("location: ../companhia_aerea.php");
  exit();
}

if (isset($_GET['deletar'])){
  $id = $_GET['deletar'];

  $s = oci_parse($c, "DELETE FROM companhia_aerea WHERE id = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($r);
    trigger_error("Não pôde executar a sentença: ". $m["message"], E_USER_ERROR);
  }

  oci_commit($c);

  $_SESSION['message'] = "Companhia aérea deletada!";
  $_SESSION['msg_type'] = "danger";

  header("location: ../companhia_aerea.php");
  exit();
}

if (isset($_GET['editar'])){
  
  $id = $_GET['editar'];

  $s = oci_parse($c, "SELECT * FROM companhia_aerea WHERE id = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($r);
    trigger_error("Não pôde executar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
  if($row != false){
    $update = true;
    $id = $row['ID'];
    $nome = $row['NOME'];
    $sigla = $row['SIGLA'];
  }

}

if (isset($_POST['atualizar'])){
  $id = $_POST['id'];
  $nome = $_POST['nome'];
  $sigla = $_POST['sigla'];

  //Inserir os dados
  $s = oci_parse($c, "UPDATE companhia_aerea SET id='$id', nome='$nome', sigla='$sigla' where id = $id");
  if (!$s) {
      $m = oci_error($c);
      trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($r);
    trigger_error("Não pôde executar a sentença: ". $m["message"], E_USER_ERROR);
  }

  oci_commit($c);

  $_SESSION['message'] = "Companhia aérea atualizada!";
  $_SESSION['msg_type'] = "warning";

  header("location: ../companhia_aerea.php");
  exit();
}

?>