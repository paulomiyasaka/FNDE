<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\RegistrarAgrupamento;

$retorno = ['resultado' => false, 'agrupamento' => null];

$matricula = $_POST['matricula'] ?? '';
$nome_centralizadora = $_POST['centralizadora'] ?? '';
$sigla_se = $_POST['se'] ?? '';
$status = $_POST['status'] ?? '';


$agrupamento = new RegistrarAgrupamento($matricula, $nome_centralizadora, $sigla_se, $status);
$criar = $agrupamento->criar();
if($criar){
    $retorno['resultado'] = TRUE;
    $retorno['agrupamento'] = $criar;
}
//var_dump($usuario);
//exit();
echo json_encode($retorno);

?>