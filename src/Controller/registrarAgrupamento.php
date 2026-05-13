<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\RegistrarAgrupamento;

$retorno = ['resultado' => false, 'agrupamento' => null];
    
$agrupamento = new RegistrarAgrupamento();
$criar = $agrupamento->criar();
if($criar){
    $retorno['resultado'] = TRUE;
    $retorno['agrupamento'] = $criar;
}
//var_dump($usuario);
//exit();
echo json_encode($retorno);

?>