<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\ListarAgrupamento;

$retorno = ['resultado' => false, 'agrupamento' => null];

//$matricula = $_POST['matricula'] ?? '';

$agrupamento = new ListarAgrupamento();
$listar = $agrupamento->listar();
if($listar){
    $retorno['resultado'] = TRUE;
    $retorno['agrupamento'] = $listar;
}
//var_dump($usuario);
//exit();
echo json_encode($retorno);

?>