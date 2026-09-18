<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Services\AtualizarStatusAgendamento;

$retorno = ['resultado' => false, 'agendamento' => null];

$id = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? '';


$agendamento = new AtualizarStatusAgendamento($id, $status);
$cancelar = $agendamento->cancelar();
if($cancelar){
    $retorno['resultado'] = TRUE;
    $retorno['agendamento'] = $cancelar;
}
//var_dump($usuario);
//exit();
echo json_encode($retorno);

?>