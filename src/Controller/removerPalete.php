<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\RegistrarPalete;

$retorno = ['resultado' => false, 'remover' => null];

$idAgrupamento = $_POST['idAgrupamento'] ?? '';
$numeroPalete = $_POST['numeroPalete'] ?? '';

$remover = new RegistrarPalete($idAgrupamento, $numeroPalete);

if($registrar->remover()){
    $retorno['resultado'] = TRUE;
    $retorno['remover'] = TRUE;
}



echo json_encode($retorno);

?>