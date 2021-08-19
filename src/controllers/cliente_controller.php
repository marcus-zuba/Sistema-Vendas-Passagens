<?php
$str = file_get_contents('../../env.json');

$json = json_decode($str, true);

$c = oci_connect($json['username'], $json['password'], "bdengcomp_low");

if (!$c) {
    $m = oci_error();
    trigger_error("Não pôde conectar com o banco de dados: ". $m["message"], E_USER_ERROR);
}
if (isset($_POST['inserir'])){
  $cpf = $_POST['cpf'];
  $nome = $_POST['nome'];
  $data_nascimento = date("d-m-Y", strtotime($_POST['data_nascimento']));
  $email = $_POST['email'];
  $telefone = $_POST['telefone'];
  $endereco = $_POST['endereco'];

  // Modificar o formato da DATA para aceitar
  $s = oci_parse($c, "ALTER SESSION SET NLS_DATE_FORMAT = 'DD-MM-YYYY'");
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
  }

  $r = oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead
  if (!$r) {
    $m = oci_error($r);
    trigger_error("Não pôde executar a sentença: ". $m["message"], E_USER_ERROR);
  }

  //Inserir os dados
  $s = oci_parse($c, "INSERT INTO CLIENTE VALUES ('$cpf', '$nome', '$data_nascimento', '$email', '$telefone', '$endereco')");
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

  print "Cliente (".$cpf.", ".$nome.", ". $data_nascimento.", 
        ".$email. ", ".$telefone. ", ".$endereco. ") inserido.";

}
