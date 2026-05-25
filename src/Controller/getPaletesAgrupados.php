<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\GetAgrupamento;

$id_agrupamento = $_POST['id'] ?? '';

$retorno = ['resultado' => false, 'dadosGerais' => null, 'agrupamento' => null];
    
$GetAgrupamento = new GetAgrupamento();
$GetAgrupamento->setAgrupamento($id_agrupamento);
$agrupamento = $GetAgrupamento->retornarPaletes();
$dadosGerais = $GetAgrupamento->retornarDadosGerais();

//var_dump($dadosGerais);


if ($agrupamento) {
    $retorno = [
        'resultado' => true,
        'dadosGerais' => $dadosGerais,
        'agrupamento' => $agrupamento
    ];
    
}

echo json_encode($retorno);

?>