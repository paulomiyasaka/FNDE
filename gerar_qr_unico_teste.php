<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/header.php';

use FNDE\Services\GerarRotuloUnicoRetratoQRCode;
$palete = 0;

if(isset($_GET['palete']) AND $_GET['palete'] != NULL AND $_GET['palete'] != ""){
    $palete = $_GET['palete'];
}else{
    echo "Verificar informações.";
    exit;
}

$gerador = new GerarRotuloUnicoRetratoQRCode();
$gerador->renderizarQRCodePalete($palete);