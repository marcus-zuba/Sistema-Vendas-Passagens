<?php

print <<<_HTML_
        <H3> Preencha os dados no novo Cliente </H3>
        <FORM method="post" action="insercao_cliente.php">
        CPF: <input type="text" name="cliente_cpf">
        <BR/>
        Nome: <input type="text" name="cliente_nome">
        <BR/>
        Data de Nascimento: <input type="date" name="cliente_data_nascimento">
        <BR/>
        Email: <input type="email" name="cliente_email">
        <BR/>
        Telefone: <input type="tel" name="cliente_telefone">
        <BR/>
        Endereço: <input type="text" name="cliente_endereco">
        <BR/>
        <INPUT type="submit" value="Inserir Cliente">
        </FORM>
      _HTML_;
