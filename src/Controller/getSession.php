<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require '../../vendor/autoload.php';

use FNDE\Utils\GetSession;

$retorno = ['resultado' => false, 'sessao' => null];
    
$sessao = new GetSession();
$usuario = $sessao->retornarSessao();
/*
if($usuario['usuarioLogado']) {
    $retorno['resultado'] = TRUE;
    $retorno['sessao'] = $usuario;
}
*/
if ($usuario) {
    $retorno = [
        'resultado' => true,
        'sessao' => [
            'matricula' => $usuario->matricula,
            'nome'      => $usuario->nome,
            'se'        => $usuario->se,
            'perfil'    => $usuario->perfil,
            'usuario'   => $usuario->usuarioLogado,
            'hora'      => $usuario->horaLogin
        ]
    ];
    
}

//var_dump($usuario);
//exit();
echo json_encode($retorno);

?>