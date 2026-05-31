<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/header.php';

use FNDE\Services\GerarRotuloAgrupamento;
use FNDE\Services\GerarRotuloUnicoRetratoQRCode;
use FNDE\Services\GetAgrupamento;
$id_agrupamento = 0;

if(isset($_GET['id']) AND $_GET['id'] > 0 AND $_GET['id'] != ""){
    $id_agrupamento = $_GET['id'];
}else{
    echo "Verificar informações.";
    exit;
}

$paletesAgrupados = new GetAgrupamento();
$paletesAgrupados->setAgrupamento($id_agrupamento);
$paletes = $paletesAgrupados->retornarPaletes();
$dadosGerais = $paletesAgrupados->retornarDadosGerais();
//var_dump($dadosGerais);

$gerador = new GerarRotuloUnicoRetratoQRCode();
$gerador->renderizar($dadosGerais[0], $paletes);