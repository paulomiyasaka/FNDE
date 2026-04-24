<?php
//error_reporting(0);
ob_clean();
header('Content-Type: application/json');

require '../../vendor/autoload.php';

use FNDE\Utils\UploadPlanilha;
use FNDE\Utils\LerPlanilha;
use FNDE\Utils\SalvarDados;
use FNDE\Database\FuncoesSQL;


//const LISTA_CLIENTES = "clientes/clientes_cadastrados.xlsx";
const UPLOAD_DIR = "../../uploads";
$retorno = [
    'resultado' => false,
    'mensagem' => ''
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    //return 'Método não permitido.';
    //return false;
    $retorno['mensagem'] = "Método não permitido.";
    echo json_encode($retorno);
}
/*
if (!isset($_POST['tabela']) || $_POST['tabela'] == '') {
    //echo 'Informar a tabela do banco de dados.';
    //return false;
    $retorno['mensagem'] = "Informar a tabela do banco de dados.";
    echo json_encode($retorno);

}
*/

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    //echo 'Arquivo não localizado.';
    //return false;
    $retorno['mensagem'] = "Arquivo não localizado.";
    echo json_encode($retorno);

}else{
        
    $nomeArquivo = $_FILES['file']['name'];
    $nomeArquivoTemporario = $_FILES['file']['tmp_name'];

    $registrosSalvos = new SalvarDados($nomeArquivo, $nomeArquivoTemporario, UPLOAD_DIR);
    
    //echo $registrosSalvos;
    if($registrosSalvos == "false"){
        //echo "Erro ao tentar salvar os registros.";
        //return false;
        $retorno['mensagem'] = "Erro ao tentar salvar os registros.";

    }else{
        //echo $registrosSalvos. " registros salvos.";
        //return true;
        $retorno['mensagem'] = $registrosSalvos. " registros salvos.";
        $retorno['resultado'] = true;
        
    }

    echo json_encode($retorno);
    


}//if $_FILES        
    








?>