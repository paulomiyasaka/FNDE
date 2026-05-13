<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\Agrupamento;

$se = $_POST['se'] ?? '';
$centralizadora = $_POST['centralizadora'] ?? '';

$retorno = ['resultado' => false, 'centralizadora' => null];
    
$getCentralizadora = new getCentralizadora();
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