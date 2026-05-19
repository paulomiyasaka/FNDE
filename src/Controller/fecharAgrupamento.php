<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\FecharAgrupamento;

$retorno = ['resultado' => false, 'agrupamento' => null];

$id = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? '';


$agrupamento = new FecharAgrupamento($id, $status);
$fechar = $agrupamento->fechar();
if($fechar){
    $retorno['resultado'] = TRUE;
    $retorno['agrupamento'] = $fechar;
}
//var_dump($usuario);
//exit();
echo json_encode($retorno);

?>