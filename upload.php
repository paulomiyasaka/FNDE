<!doctype html>
<html lang="pt-BR">


    
<?php
include 'header.php';
include 'vendor/autoload.php';

use FNDE\Utils\Validacoes;

$validacoes = new Validacoes();
//$validacoes->validar();
$validacoes->interromperReenvioFormulario();
$validacoes->verificarUsuarioLogado();
$usuario['perfil'] = $_SESSION['perfil_usuario'];
$usuario['nome'] = $_SESSION['nome'];
$usuario['matricula'] = $_SESSION['matricula'];
$usuario['se'] = $_SESSION['se_usuario'];
include 'menuTop.php';
?>

<body>
<?php
include 'view/conteudoUpload.php';
include 'view/modalResposta.php';
//include 'scripts.html';
include 'footer.php';
?>


</body>
</html>

<?php
include 'scripts.html';
?>

<script type="module" src="view/js/upload.js"></script>