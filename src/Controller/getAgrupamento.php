<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\GetAgrupamento;

$retorno = ['resultado' => false, 'se' => null];
    
$GetAgrupamento = new GetAgrupamento();
$agrupamento = $GetAgrupamento->retornarPaletes();



if ($agrupamento) {
    $retorno = [
        'resultado' => true,
        'agrupamento' => $agrupamento
    ];
    
}

echo json_encode($retorno);

?>