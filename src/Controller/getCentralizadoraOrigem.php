<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Utils\GetCentralizadoraOrigem;

$se = $_POST['se'] ?? '';

$retorno = ['resultado' => false, 'centralizadora' => null];
    
$getCentralizadora = new getCentralizadoraOrigem();
$getCentralizadora->setSE($se);
$centralizadora = $getCentralizadora->retornarCentralizadora();



if ($centralizadora) {
    $retorno = [
        'resultado' => true,
        'centralizadora' => $centralizadora
    ];
    
}

echo json_encode($retorno);

?>