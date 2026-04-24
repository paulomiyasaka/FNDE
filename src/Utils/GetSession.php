<?php

namespace FNDE\Utils;

use FNDE\Models\UsuarioSessao;

class GetSession
{

    public function retornarSessao(): ?UsuarioSessao
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['usuario_logado'])) {
            return null; 
        }

        $dados = [
            'matricula'      => (int) $_SESSION['matricula'],
            'nome'           => $_SESSION['nome'],
            'se'     => $_SESSION['se_usuario'],
            'perfil' => $_SESSION['perfil_usuario'],
            'usuarioLogado' => $_SESSION['usuario_logado'],
            'horaLogin'     => $_SESSION['hora_login']
        ];

        return UsuarioSessao::fromArray($dados);


    }



}

        
