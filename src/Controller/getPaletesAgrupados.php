<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\GetAgrupamento;

$palete = $_POST['palete'] ?? '';

$retorno = ['resultado' => false, 'dadosGerais' => null, 'agrupamento' => null];
    
$GetAgrupamento = new GetAgrupamento();
$GetAgrupamento->setPalete($palete);
$agrupamento = $GetAgrupamento->buscarAgrupamento();

$idAgrupamento = $agrupamento[0]->idAgrupamento;
$GetAgrupamento->setAgrupamento($idAgrupamento);
$dadosPaletes = $GetAgrupamento->retornarPaletes();


//var_dump($dadosPaletes);


if ($agrupamento) {
    $retorno = [
        'resultado' => true,
        'paletes' => $dadosPaletes,
        'agrupamento' => $agrupamento
    ];
    
}

echo json_encode($retorno);

?>