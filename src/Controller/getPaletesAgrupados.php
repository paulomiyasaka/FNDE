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

$idAgrupamento = 0;

if (!empty($agrupamento)) {
    $idAgrupamento = $agrupamento[0]->idAgrupamento;
    $GetAgrupamento->setAgrupamento($idAgrupamento);
    $dadosPaletes = $GetAgrupamento->retornarPaletes();

    $retorno = [
        'resultado' => true,
        'paletes' => $dadosPaletes,
        'agrupamento' => $agrupamento
    ];
    
  

}

//var_dump($dadosPaletes);

echo json_encode($retorno);

?>