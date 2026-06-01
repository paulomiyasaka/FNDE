<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\RegistrarAgrupamento;

$retorno = ['resultado' => false, 'agrupamento' => null];

$matricula = $_POST['matricula'] ?? '';
$sigla_centralizadora = $_POST['centralizadora'] ?? '';
$sigla_se = $_POST['se'] ?? '';
$status = $_POST['status'] ?? '';
$sigla_centralizadora_origem = $_POST['centralizadora_origem'] ?? '';
$sigla_se_origem = $_POST['se_origem'] ?? '';

$agrupamento = new RegistrarAgrupamento($matricula, $sigla_se_origem, $sigla_centralizadora_origem, $sigla_se, $sigla_centralizadora, $status);
$criar = $agrupamento->criar();
if($criar){
    $retorno['resultado'] = TRUE;
    $retorno['agrupamento'] = $criar;
}
//var_dump($usuario);
//exit();
echo json_encode($retorno);

?>