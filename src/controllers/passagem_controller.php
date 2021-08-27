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
$id_voo = '';
$cpf_passageiro = '';
$poltrona = '';
$peso_bagagem = '';
$quantidade_bagagem = '';
$cpf_pagador = '';


if (isset($_POST['inserir'])){
  $id = $_POST['id'];
  $id_voo = $_POST['id_voo'];
  $cpf_passageiro = $_POST['cpf_passageiro'];
  $poltrona = $_POST['poltrona'];
  $peso_bagagem = $_POST['peso_bagagem'];
  $quantidade_bagagem = $_POST['quantidade_bagagem'];
  $cpf_pagador = $_POST['cpf_pagador'];
    
  //Inserir os dados
  $s = oci_parse($c, "INSERT INTO PASSAGEM VALUES 
    ($id, $id_voo, '$cpf_passageiro', $poltrona, $peso_bagagem,
    $quantidade_bagagem)");
  
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    print_r($m);
    $_SESSION['message'] = "Erro na inserção da passagem!\nMensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../comprar_passagem.php");
    exit();
  }

  $s = oci_parse($c, "SELECT valor_passagem FROM VOO WHERE id = '$id_voo'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../comprar_passagem.php");
    exit();
  }

  $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
  if($row != false){
    $valor_passagem = $row['VALOR_PASSAGEM'];
  }

  $horario_atual = date("d-m-Y H:i",strtotime('-5 hours', time()));

  //Inserir os dados
  $s = oci_parse($c, "INSERT INTO TRANSACAO VALUES 
    ('$id', '$id', '$cpf_pagador', '$valor_passagem', '$horario_atual')");
  
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    print_r($m);
    $_SESSION['message'] = "Erro na inserção da transação!\nMensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../comprar_passagem.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Passagem comprada e transação registrada!";
  $_SESSION['msg_type'] = "success";

  header("location: ../comprar_passagem.php");
  exit();
}

if (isset($_GET['deletar'])){
  $id = $_GET['deletar'];

  $s = oci_parse($c, "DELETE FROM passagem WHERE id = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na deleção! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../passagem.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Passagem e Transação deletada!";
  $_SESSION['msg_type'] = "warning";

  header("location: ../passagem.php");
  exit();
}

if (isset($_GET['editar'])){
  
  $id = $_GET['editar'];

  $s = oci_parse($c, "SELECT * FROM PASSAGEM WHERE id = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../passagem.php");
    exit();
  }

  $update_1 = false;

  $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
  if($row != false){
    $update_1 = true;
    $id = $row['ID'];
    $id_voo = $row['ID_VOO'];
    $cpf_passageiro = $row['CPF_PASSAGEIRO'];
    $poltrona = $row['POLTRONA'];
    $peso_bagagem = $row['PESO_BAGAGEM'];
    $quantidade_bagagem = $row['QUANTIDADE_BAGAGEM'];
  }

  $s = oci_parse($c, "SELECT cpf_pagador FROM TRANSACAO WHERE id_passagem = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../passagem.php");
    exit();
  } 

  $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
  if($row != false){
    if($update_1)
      $update = true;
    $cpf_pagador = $row['CPF_PAGADOR'];
  }

}

if (isset($_POST['atualizar'])){
  $id = $_POST['id'];
  $id_voo = $_POST['id_voo'];
  $cpf_passageiro = $_POST['cpf_passageiro'];
  $poltrona = $_POST['poltrona'];
  $peso_bagagem = $_POST['peso_bagagem'];
  $quantidade_bagagem = $_POST['quantidade_bagagem'];
  $cpf_pagador = $_POST['cpf_pagador'];

  $s = oci_parse($c, "UPDATE PASSAGEM SET id_voo='$id_voo', 
  cpf_passageiro='$cpf_passageiro', poltrona='$poltrona', 
  peso_bagagem='$peso_bagagem', quantidade_bagagem='$quantidade_bagagem' where id = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na atualização da passagem! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../passagem.php");
    exit();
  }

  $s = oci_parse($c, "SELECT valor_passagem FROM VOO WHERE id = '$id_voo'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../passagem.php");
    exit();
  }

  $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
  if($row != false){
    $valor_passagem = $row['VALOR_PASSAGEM'];
  }

  $horario_atual = date("d-m-Y H:i",strtotime('-5 hours', time()));  

  $s = oci_parse($c, "UPDATE TRANSACAO SET cpf_pagador='$cpf_pagador', valor_pago='$valor_passagem', 
  horario='$horario_atual' where id_passagem = '$id'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    print_r($m);
    $_SESSION['message'] = "Erro na atualização da transação! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
//    header("location: ../passagem.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Passagem e transação atualizada!";
  $_SESSION['msg_type'] = "warning";

  header("location: ../passagem.php");
  exit();
}

?>