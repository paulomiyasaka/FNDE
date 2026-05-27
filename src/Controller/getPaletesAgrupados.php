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
//var_dump($agrupamento);
//if (!empty($agrupamento)) {
if ($agrupamento) {
    $idAgrupamento = $agrupamento[0]->idAgrupamento;
    $GetAgrupamento->setAgrupamento($idAgrupamento);
    $dadosPaletes = $GetAgrupamento->retornarPaletes();

    $retorno = [
        'resultado' => true,
        'agrupamento' => $dadosPaletes,
        'dadosGerais' => $agrupamento
    ];
    
  

}

//var_dump($dadosPaletes);

echo json_encode($retorno);

?>