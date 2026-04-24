<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\Login;

$matricula = $_POST['matricula'] ?? '';

$retorno = ['resultado' => false, 'usuario' => null];

if (strlen($matricula) === 8) {
    
    $login = new Login($matricula);
    $usuario = $login->logar();
    
    if($usuario !== NULL) {
        $retorno['resultado'] = TRUE;
        $retorno['usuario'] = $usuario;
    }
}

echo json_encode($retorno);

?>