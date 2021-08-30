<!DOCTYPE html>
<html>
  <head>
    <title>BDNAD</title>
    <script src="https://code.jquery.com/jquery-3.3.1.js"
    integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="src/style.css">
  </head>
  <body>
    <div class="col justify-content-center">
      <div class="form-group" id="titulo">
        <h1>Banco de Dados de Navegação Aeronáutica Doméstica</h1>
      </div>
      <div class="form-group">
        <a href="src/comprar_passagem.php" class="btn btn-primary menu-option" >Comprar passagem</a>
      </div>
      <div class="form-group">
        <a href="src/cliente.php" class="btn btn-primary menu-option" >Clientes Cadastrados</a>
      </div>
      <div class="form-group" >
        <a href="src/aeroporto.php"  class="btn btn-primary menu-option" >Aeroportos</a>
      </div>
      <div class="form-group">
        <a href="src/companhia_aerea.php" class="btn btn-primary menu-option" >Companhias Aereas</a>
      </div>
      <div class="form-group">
        <a href="src/voo.php" class="btn btn-primary menu-option" >Voos</a>
      </div>
      <div class="form-group">
        <a href="src/passagem.php" class="btn btn-primary menu-option" >Passagens e Transações</a>
      </div>
      <div class="form-group" id="titulo">
        <h2>Relatórios</h2>
      </div>
      <div class="form-group">
        <a href="src/relatorios/pessoas_voo.php" class="btn btn-primary menu-option" >Nomes dos Passageiros por Voo</a>
      </div>
      <div class="form-group">
        <a href="src/relatorios/aeroportos_destinos.php" class="btn btn-primary menu-option" >Relação de Todos os Aeroportos e Destinos</a>
      </div>
      <div class="form-group">
        <a href="src/relatorios/media_peso_bag.php" class="btn btn-primary menu-option" >Média dos Pesos Máximos de Bagagem por Companhia Aérea</a>
      </div>
      <div class="form-group">
        <a href="src/relatorios/preco_medio_cia_aerea.php" class="btn btn-primary menu-option" >Preço médio da Passagem por Companhia Aérea</a>
      </div>
    </div>
  </body>
</html>
