<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use FNDE\Services\GerarRotuloAgrupamento2;
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

//var_dump($paletes);


/*
for ($i = 1; $i <= 15; $i++) {
    $palete = new \stdClass();
    // ID do Palete curto para caber perfeitamente no topo interno da moldura
    $palete->id_etiqueta = "PALETE " . str_pad($i, 2, "0", STR_PAD_LEFT);
    $palete->peso = number_format(rand(15, 38) + (rand(0, 99)/100), 2, ',', '') . " kg";
    
    // Dados de 97 caracteres industriais para o QR Code
    $palete->qr_97_chars = "FNDE_REG_N_ " . str_pad($i, 3, "0", STR_PAD_LEFT) . "_" . str_repeat("Q", 79);
    
    $paletes[] = $palete;
}
*/


$dadosGerais = [
    'destino'     => 'FNDE_BENFICA_AGRUPAR',
    'se'          => 'RJ',
    'sigla'       => 'BFC',
    'qtd_total'   => '15',
    'peso_total'  => '394,12',
    'qr_master'   => '00000394120015BFCRJXX' // 21 caracteres
];

$gerador = new GerarRotuloAgrupamento2();
$gerador->renderizar($dadosGerais, $paletes);