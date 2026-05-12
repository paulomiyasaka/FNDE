<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Utils\GetCentralizadora;

$retorno = ['resultado' => false, 'centralizadora' => null];
    
$getCentralizadora = new getCentralizadora();
$centralizadora = $getCentralizadora->retornarCentralizadora();



if ($centralizadora) {
    $retorno = [
        'resultado' => true,
        'centralizadora' => $centralizadora
    ];
    
}

echo json_encode($retorno);

?>