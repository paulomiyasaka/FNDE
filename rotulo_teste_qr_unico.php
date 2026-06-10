<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/header.php';

use FNDE\Services\GerarRotuloAgrupamento;
use FNDE\Services\GerarRotuloTesteUnicoRetratoQRCode;
use FNDE\Services\GetAgrupamento;
$palete = 0;

if(isset($_GET['p']) AND $_GET['p'] > 0 AND $_GET['p'] != ""){
    $palete = $_GET['p'];
}else{
    echo "Verificar informações.";
    exit;
}

$paletesAgrupados = new GetAgrupamento();
$paletesAgrupados->setAgrupamento($palete);
$paletes = $paletesAgrupados->retornarPaletes();
$dadosGerais = $paletesAgrupados->retornarDadosGerais();
//var_dump($dadosGerais);

$gerador = new GerarRotuloTesteUnicoRetratoQRCode();
$gerador->renderizar($dadosGerais[0], $paletes);