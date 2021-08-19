<?php
$str = file_get_contents('../env.json');

$json = json_decode($str, true);

$c = oci_connect($json['username'], $json['password'], "bdengcomp_low");

if (!$c) {
    $m = oci_error();
    trigger_error("Não pôde conectar com o banco de dados: ". $m["message"], E_USER_ERROR);
}


$stmtCreate = array(
    "BEGIN
       EXECUTE IMMEDIATE 
          'CREATE TABLE cliente (
            cpf              VARCHAR2(11) NOT NULL,
            nome             VARCHAR2(50) NOT NULL,
            data_nascimento  DATE NOT NULL,
            email            VARCHAR2(50) NOT NULL,
            telefone         VARCHAR2(14) NOT NULL,
            endereco         VARCHAR2(100) NOT NULL
          )';
       EXCEPTION
         WHEN OTHERS THEN
           IF SQLCODE NOT IN (-00955) THEN
             RAISE;
           END IF;
     END;"//, //Erro 955 - object already exist

//    "ALTER TABLE cliente ADD CONSTRAINT cliente_pk PRIMARY KEY ( cpf );",

//    "ALTER TABLE cliente ADD CONSTRAINT cliente_email_un UNIQUE ( email );"
);

foreach ($stmtCreate as $stmt) {
  $s = oci_parse($c, $stmt);
  if (!$s) {
    $m = oci_error($c);
    trigger_error("Could not parse statement: ". $m["message"], E_USER_ERROR);
  }
  $r = oci_execute($s);
  if (!$r) {
    $m = oci_error($s);
    trigger_error("Could not execute statement: ". $m["message"], E_USER_ERROR);
  }
}

$s = oci_parse($c, "INSERT INTO CLIENTE VALUES (:1, :2, :3, :4, :5, :6)");
if (!$s) {
    $m = oci_error($c);
    trigger_error("Não pôde compilar a sentença: ". $m["message"], E_USER_ERROR);
}

oci_bind_by_name($s, ":1", $_POST['cliente_cpf']);
oci_bind_by_name($s, ":2", $_POST['cliente_nome']);
$data_nascimento = date("d-m-Y", strtotime($_POST['cliente_data_nascimento']));
$data = "TO_DATE(19-08-2021)";
oci_bind_by_name($s, ":3", $data_nascimento);
oci_bind_by_name($s, ":4", $_POST['cliente_email']);
oci_bind_by_name($s, ":5", $_POST['cliente_telefone']);
oci_bind_by_name($s, ":6", $_POST['cliente_endereco']);
oci_execute($s, OCI_NO_AUTO_COMMIT); // for PHP <= 5.3.1 use OCI_DEFAULT instead

if (!$r) {
    $m = oci_error($s);
    trigger_error("Não pôde executar a sentença: ". $m["message"], E_USER_ERROR);
}

oci_commit($c);

print "Empregado (".$_POST['cliente_cpf'].", ".$_POST['cliente_nome'].", ". $data_nascimento.", 
        ".$_POST['cliente_email']. ", ".$_POST['cliente_telefone']. ", ".$_POST['cliente_endereco']. ") inserido.";

