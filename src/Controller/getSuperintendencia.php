<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Utils\GetSuperintendencia;

$retorno = ['resultado' => false, 'se' => null];
    
$getSe = new GetSuperintendencia();
$se = $getSe->retornarSe();



if ($se) {
    $retorno = [
        'resultado' => true,
        'se' => $se
    ];
    
}

echo json_encode($retorno);

?>