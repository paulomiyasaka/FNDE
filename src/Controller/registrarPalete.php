<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\RegistrarPalete;

$retorno = ['resultado' => false, 'agrupamento' => null];

$idAgrupamento = $_POST['idAgrupamento'] ?? '';
$numeroPalete = $_POST['numeroPalete'] ?? '';
$pesoLiquido = $_POST['pesoLiquido'] ?? '';
$pesoMinimoEstimado = $_POST['pesoMinimoEstimado'] ?? '';
$pesoMaximoEstimado = $_POST['pesoMaximoEstimado'] ?? '';
$encomendaInicial = $_POST['encomendaInicial'] ?? '';
$encomendaFinal = $_POST['encomendaFinal'] ?? '';
$codigoSKU = $_POST['codigoSKU'] ?? '';
$quantidadeEncomendas = $_POST['quantidadeEncomendas'] ?? '';
$faseUnitizacao = $_POST['faseUnitizacao'] ?? '';
$siglaCentralizadora = $_POST['siglaCentralizadora'] ?? '';
$se = $_POST['se'] ?? '';

$registrar = new RegistrarPalete($idAgrupamento, $numeroPalete);
$existePalete = $registrar->consultar();

if(!$existePalete){

    $registrado = $registrar->registrar($numeroPalete, $pesoLiquido, $pesoMinimoEstimado, $pesoMaximoEstimado, $encomendaInicial, $encomendaFinal, $codigoSKU, $quantidadeEncomendas, $faseUnitizacao, $siglaCentralizadora, $se);
    if($registrado){
        //$retorno['resultado'] = TRUE;
        $retorno['palete'] = $registrado;
    }

}

$agrupar = $registrar->agrupar();
if($agrupar){
    $retorno['resultado'] = TRUE;
    $retorno['agrupamento'] = TRUE;
}



echo json_encode($retorno);

?>