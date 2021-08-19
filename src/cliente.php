<!DOCTYPE html>
<html>
  <head>
    <title>Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </head>
  <body>
    <?php require_once 'controllers/cliente_controller.php'; ?>

    <?php ?>

    <div  class="row justify-content-center">
      <form action="controllers/cliente_controller.php" method="POST">
      <div class="form-group">
        <label>CPF</label>
        <input type="text" name="cpf" class="form-control" placeholder="Insira o CPF">
      </div>
      <div class="form-group">
        <label>Nome</label>
        <input type="text" name="nome" class="form-control" placeholder="Insira o Nome">
      </div>
      <div class="form-group">
        <label>Data de Nascimento</label>
        <input type="date" name="data_nascimento" class="form-control" placeholder="Insira a data de nascimento">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="text" name="email" class="form-control" placeholder="Insira o Email">
      </div>
      <div class="form-group">
        <label>Telefone</label>
        <input type="text" name="telefone" class="form-control" placeholder="Insira o Telefone">
      </div>
      <div class="form-group">
        <label>Endereço</label>
        <input type="text" name="endereco" class="form-control" placeholder="Insira o Endereço">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary" name="inserir">Inserir</button>
      </div>

    </form>
    </div>
  </body>
</html>
