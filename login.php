<!doctype html>
<html lang="pt-BR">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão FNDE</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="view/css/signin.css" rel="stylesheet">
    
  </head>

<?php
include 'header.php';
include 'vendor/autoload.php';

use FNDE\Utils\Validacoes;

Validacoes::interromperReenvioFormulario();



?>

<body>
<?php
include 'view/formLogin.php';

?>


  </body>
</html>

<?php
include 'scripts.html';
?>

<script type="module" src="view/js/logar.js"></script>