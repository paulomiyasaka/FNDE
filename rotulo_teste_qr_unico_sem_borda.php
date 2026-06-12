<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/header.php';

use FNDE\Services\GerarRotuloAgrupamento;
use FNDE\Services\GerarRotuloTesteUnicoRetratoQRCodeSemBorda;
use FNDE\Services\GetAgrupamento;
$palete = 0;

if(isset($_GET['p']) AND $_GET['p'] > 0 AND $_GET['p'] != ""){
    $palete = strtoupper($_GET['p']);
}else{
    echo "Verificar informações.";
    exit;
}

$codigoPalete = substr($palete, 0, 11);
$pesoLiquido = substr($palete, 11, 11);
$pesoMinimo = substr($palete, 22, 11);
$pesoMaximo = substr($palete, 33, 11);
$encomendaInicial = substr($palete, 44, 13);
$encomendaFinal = substr($palete, 57, 13);
$sku = substr($palete, 71, 15);
$qtdeEncomendas = substr($palete, 85, 4);
$faseUnitizacao = substr($palete, 89, 2);
$siglaCentralizadora = substr($palete, 91, 3);
$siglaSe = substr($palete, 94, 3);
if(strlen($siglaSe) < 3)
{
    $palete .= " "; 
}

$dadosGerais = new stdClass();
$dadosGerais->codigoPalete = $codigoPalete;
$dadosGerais->pesoTotalAgrupamento = $pesoLiquido;
$dadosGerais->idAgrupamento = $sku;
$dadosGerais->qrCompilacao = $palete;
$dadosGerais->siglaSe = $siglaSe;
$dadosGerais->siglaCentralizadora = $siglaCentralizadora;
$dadosGerais->nomeCentralizadora = $siglaCentralizadora;
$dadosGerais->nomeCentralizadoraOrigem = "CLI CAJAMAR RT";
$dadosGerais->totalPaletes = 1;


$paletesAgrupados = new GetAgrupamento();
$paletesAgrupados->setAgrupamento($palete);
$paletes = $paletesAgrupados->retornarPaletes();
//$dadosGerais = $paletesAgrupados->retornarDadosGerais();
//var_dump($dadosGerais);
$paletes[] = &$palete;

$gerador = new GerarRotuloTesteUnicoRetratoQRCodeSemBorda();
//var_dump($dadosGerais);
$gerador->renderizar($dadosGerais, $paletes);