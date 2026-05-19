<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\RegistrarPalete;

$retorno = ['resultado' => false, 'agrupamento' => null];

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

$registrar = new RegistrarPalete($numeroPalete, $pesoLiquido, $pesoMinimoEstimado, $pesoMaximoEstimado, $encomendaInicial, $encomendaFinal, $codigoSKU, $quantidadeEncomendas, $faseUnitizacao, $siglaCentralizadora, $se);
$registrado = $registrar->registrar();
if($registrado){
    $retorno['resultado'] = TRUE;
    $retorno['agrupamento'] = $registrado;
}
//var_dump($usuario);
//exit();
echo json_encode($retorno);

?>