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
$cod = '';
$nome = '';
$cep = '';
$uf = '';
$cidade = '';
$rua = '';
$bairro = '';


if (isset($_POST['inserir'])){
  $cod = $_POST['cod'];
  $nome = $_POST['nome'];
  $cep = $_POST['cep'];
  $uf = $_POST['uf'];
  $cidade = $_POST['cidade'];
  $rua = $_POST['rua'];
  $bairro = $_POST['bairro'];

  //Inserir os dados
  $s = oci_parse($c, "INSERT INTO AEROPORTO VALUES ('$cod', '$nome', '$cep', '$uf', '$cidade', '$bairro', '$rua' )");
  if (!$s) {
      $m = oci_error($c);
      trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na inserção! Mensagem de erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../aeroporto.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Aeroporto inserido!";
  $_SESSION['msg_type'] = "success";

  header("location: ../aeroporto.php");
  exit();
}

if (isset($_GET['deletar'])){
  $cod = $_GET['deletar'];

  $s = oci_parse($c, "DELETE FROM AEROPORTO WHERE cod = '$cod'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na deleção! Mensagem de erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../aeroporto.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Aeroporto deletado!";
  $_SESSION['msg_type'] = "warning";

  header("location: ../aeroporto.php");
  exit();
}

if (isset($_GET['editar'])){
  
  $cod = $_GET['editar'];

  $s = oci_parse($c, "SELECT * FROM AEROPORTO WHERE cod = '$cod'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro! Mensagem de erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../aeroporto.php");
    exit();
  }

  $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
  if($row != false){
    $update = true;
    $cod = $row['COD'];
    $nome = $row['NOME'];
    $cep = $row['CEP'];
    $uf = $row['UF'];
    $cidade = $row['CIDADE'];
    $rua = $row['RUA'];
    $bairro = $row['BAIRRO'];
  }

}

if (isset($_POST['atualizar'])){
  $cod = $_POST['cod'];
  $nome = $_POST['nome'];
  $cep = $_POST['cep'];
  $uf = $_POST['uf'];
  $cidade = $_POST['cidade'];
  $rua = $_POST['rua'];
  $bairro = $_POST['bairro'];

  //Inserir os dados
  $s = oci_parse($c, "UPDATE AEROPORTO SET nome='$nome', cep='$cep', uf='$uf', 
        cidade='$cidade', rua='$rua', bairro='$bairro' where cod='$cod'");
  if (!$s) {
      $m = oci_error($c);
      trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na atualização! Mensagem de erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../aeroporto.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Aeroporto atualizado!";
  $_SESSION['msg_type'] = "warning";

  header("location: ../aeroporto.php");
  exit();
}

?>