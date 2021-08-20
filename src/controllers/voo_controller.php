<?php
$str = file_get_contents(__DIR__.'/../../env.json');
$json = json_decode($str, true);

$c = oci_connect($json['username'], $json['password'], "bdengcomp_low");
if (!$c) {
    $m = oci_error();
    trigger_error("Não pôde conectar com o banco de dados: ". $m["message"], E_USER_ERROR);
}

// Modificar o formato do Timestamp
$s = oci_parse($c, "alter SESSION set NLS_TIMESTAMP_FORMAT = 'DD-MM-YYYY HH24:MI'");
if (!$s) {
  $m = oci_error($c);
  trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
}

$r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
if (!$r) {
  $m = oci_error($s);
  $_SESSION['message'] = "Erro! Mensagem de Erro: ".$m['message'];
  $_SESSION['msg_type'] = "danger";
  header("location: ../voo.php");
  exit();
}

session_start();

$update = false;
$id = '';
$id_companhia_aerea = '';
$cod_aeroporto_origem = '';
$cod_aeroporto_destino = '';
$valor_passagem = '';
$horario_saida = '';
$horario_chegada = '';
$poltronas_total = '';
$poltronas_disponiveis = '';
$peso_bagagem_max = '';
$quantidade_bagagem_max = '';


if (isset($_POST['inserir'])){
  $id = $_POST['id'];
  $id_companhia_aerea = $_POST['id_companhia_aerea'];
  $cod_aeroporto_origem = $_POST['cod_aeroporto_origem'];
  $cod_aeroporto_destino = $_POST['cod_aeroporto_destino'];
  $valor_passagem = $_POST['valor_passagem'];
  $horario_saida = date("d-m-Y H:i", strtotime($_POST['horario_saida']));
  $horario_chegada = date("d-m-Y H:i", strtotime($_POST['horario_chegada']));
  $poltronas_total = $_POST['poltronas_total'];
  $poltronas_disponiveis = $_POST['poltronas_disponiveis'];
  $peso_bagagem_max = $_POST['peso_bagagem_max'];
  $quantidade_bagagem_max = $_POST['quantidade_bagagem_max'];
  
  //Inserir os dados
  $s = oci_parse($c, "INSERT INTO voo VALUES 
    ('$id', '$id_companhia_aerea', '$cod_aeroporto_origem', '$cod_aeroporto_destino', '$valor_passagem',
    '$horario_saida', '$horario_chegada', '$poltronas_total', '$poltronas_disponiveis', 
    '$peso_bagagem_max', '$quantidade_bagagem_max')");
  
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }


  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na inserção!\nMensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../voo.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Voo inserido!";
  $_SESSION['msg_type'] = "success";

//  header("location: ../voo.php");
  exit();
}

if (isset($_GET['deletar'])){
  $id = $_GET['deletar'];

  $s = oci_parse($c, "DELETE FROM voo WHERE id = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na deleção! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../voo.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Voo deletado!";
  $_SESSION['msg_type'] = "warning";

  header("location: ../voo.php");
  exit();
}

if (isset($_GET['editar'])){
  
  $id = $_GET['editar'];

  $s = oci_parse($c, "SELECT * FROM voo WHERE id = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }


  $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../voo.php");
    exit();
  }

  $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
  if($row != false){
    $update = true;
    $id = $row['ID'];
    $id_companhia_aerea = $row['ID_COMPANHIA_AEREA'];
    $cod_aeroporto_origem = $row['COD_AEROPORTO_ORIGEM'];
    $cod_aeroporto_destino = $row['COD_AEROPORTO_DESTINO'];
    $valor_passagem = $row['VALOR_PASSAGEM'];
    $horario_saida = str_replace(' ','T',date("Y-m-d H:i", strtotime($row['HORARIO_SAIDA'])));
    $horario_chegada = str_replace(' ','T',date("Y-m-d H:i", strtotime($row['HORARIO_CHEGADA'])));
    $poltronas_total = $row['POLTRONAS_TOTAL'];
    $poltronas_disponiveis = $row['POLTRONAS_DISPONIVEIS'];
    $peso_bagagem_max = $row['PESO_BAGAGEM_MAX'];
    $quantidade_bagagem_max = $row['QUANTIDADE_BAGAGEM_MAX'];  
  }
}

if (isset($_POST['atualizar'])){
  print_r($_POST);
  $id = $_POST['id'];
  $id_companhia_aerea = $_POST['id_companhia_aerea'];
  $cod_aeroporto_origem = $_POST['cod_aeroporto_origem'];
  $cod_aeroporto_destino = $_POST['cod_aeroporto_destino'];
  $valor_passagem = $_POST['valor_passagem'];
  $horario_saida = date("d-m-Y H:i", strtotime($_POST['horario_saida']));
  $horario_chegada = date("d-m-Y H:i", strtotime($_POST['horario_chegada']));
  $poltronas_total = $_POST['poltronas_total'];
  $poltronas_disponiveis = $_POST['poltronas_disponiveis'];
  $peso_bagagem_max = $_POST['peso_bagagem_max'];
  $quantidade_bagagem_max = $_POST['quantidade_bagagem_max'];

  $s = oci_parse($c, "UPDATE voo SET id_companhia_aerea='$id_companhia_aerea', 
  cod_aeroporto_origem='$cod_aeroporto_origem', cod_aeroporto_destino='$cod_aeroporto_destino', 
  valor_passagem='$valor_passagem', horario_saida='$horario_saida', horario_chegada='$horario_chegada', 
  poltronas_total='$poltronas_total', poltronas_disponiveis='$poltronas_disponiveis', 
  peso_bagagem_max='$peso_bagagem_max', quantidade_bagagem_max='$quantidade_bagagem_max'
  where id = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na atualização! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../voo.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Voo atualizado!";
  $_SESSION['msg_type'] = "warning";

//  header("location: ../voo.php");
//  exit();
}

?>