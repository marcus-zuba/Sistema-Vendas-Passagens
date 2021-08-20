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
$cpf = '';
$nome = '';
$data_nascimento = '';
$email = '';
$telefone = '';
$endereco = '';


if (isset($_POST['inserir'])){
  $cpf = $_POST['cpf'];
  $nome = $_POST['nome'];
  $data_nascimento = date("d-m-Y", strtotime($_POST['data_nascimento']));
  $email = $_POST['email'];
  $telefone = $_POST['telefone'];
  $endereco = $_POST['endereco'];

  // Modificar o formato da DATA
  $s = oci_parse($c, "ALTER SESSION SET NLS_DATE_FORMAT = 'DD-MM-YYYY'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na inserção! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../cliente.php");
    exit();
  }

  //Inserir os dados
  $s = oci_parse($c, "INSERT INTO CLIENTE VALUES ('$cpf', '$nome', '$data_nascimento', '$email', '$telefone', '$endereco')");
  if (!$s) {
      $m = oci_error($c);
      trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na inserção! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../cliente.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Cliente inserido!";
  $_SESSION['msg_type'] = "success";

  header("location: ../cliente.php");
  exit();
}

if (isset($_GET['deletar'])){
  $cpf = $_GET['deletar'];

  $s = oci_parse($c, "DELETE FROM CLIENTE WHERE CPF = $cpf");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na deleção! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../cliente.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Cliente deletado!";
  $_SESSION['msg_type'] = "danger";

  header("location: ../cliente.php");
  exit();
}

if (isset($_GET['editar'])){

  // Modificar o formato da DATA
  $s = oci_parse($c, "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../cliente.php");
    exit();
  }
  
  $cpf = $_GET['editar'];

  $s = oci_parse($c, "SELECT * FROM CLIENTE WHERE CPF = $cpf");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../cliente.php");
    exit();
  }

  $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
  if($row != false){
    $update = true;
    $cpf = $row['CPF'];
    $nome = $row['NOME'];
    $data_nascimento = $row['DATA_NASCIMENTO'];
    $email = $row['EMAIL'];
    $telefone = $row['TELEFONE'];
    $endereco = $row['ENDERECO'];
  }

}

if (isset($_POST['atualizar'])){
  $cpf = $_POST['cpf'];
  $nome = $_POST['nome'];
  $data_nascimento = date("d-m-Y", strtotime($_POST['data_nascimento']));
  $email = $_POST['email'];
  $telefone = $_POST['telefone'];
  $endereco = $_POST['endereco'];

  // Modificar o formato da DATA
  $s = oci_parse($c, "ALTER SESSION SET NLS_DATE_FORMAT = 'DD-MM-YYYY'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na atualização! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../cliente.php");
    exit();
  }

  //Inserir os dados
  $s = oci_parse($c, "UPDATE CLIENTE SET nome='$nome', data_nascimento='$data_nascimento', 
            email='$email', telefone='$telefone', endereco='$endereco' where cpf='$cpf'");
  if (!$s) {
      $m = oci_error($c);
      trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($s);
    $_SESSION['message'] = "Erro na atualização! Mensagem de Erro: ".$m['message'];
    $_SESSION['msg_type'] = "danger";
    header("location: ../cliente.php");
    exit();
  }

  oci_commit($c);

  $_SESSION['message'] = "Cliente atualizado!";
  $_SESSION['msg_type'] = "warning";

  header("location: ../cliente.php");
  exit();
}

?>