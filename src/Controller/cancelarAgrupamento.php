<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\CancelarAgrupamento;

$retorno = ['resultado' => false, 'agrupamento' => null];

$id = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? '';


$agrupamento = new CancelarAgrupamento($id, $status);
$cancelar = $agrupamento->cancelar();
if($cancelar){
    $retorno['resultado'] = TRUE;
    $retorno['agrupamento'] = $cancelar;
}
//var_dump($usuario);
//exit();
echo json_encode($retorno);

?>