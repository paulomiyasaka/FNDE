<?php

namespace FNDE\Services;

use FNDE\Database\FuncoesSQL;
use FNDE\Models\UsuarioLogin;

class Login
{

	private int $matricula;
	
	public function __construct(string $matricula)
	{
		$this->matricula = $matricula;
	}

	public function logar(): ?UsuarioLogin
	{
		$matricula = $this->matricula;

		$funcoesSQL = new funcoesSQL();
		$sql = "SELECT u.matricula, u.nome, u.sigla_se, u.perfil FROM tb_usuario as u WHERE u.matricula = :matricula";

		$dados = array(":matricula" => $matricula);
		$resultado = $funcoesSQL->fetchAllSQL($sql, $dados);
		
        if (empty($resultado)) {
            return null;
        }
        
        $usuario = UsuarioLogin::fromArray($resultado);
        $this->criarSessao($usuario);
        return $usuario;

	}


	private function criarSessao(UsuarioLogin $usuario): void
	{
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Regenera o ID para evitar Session Fixation
        session_regenerate_id(true);
        
        $_SESSION['matricula'] = $usuario->matricula;
        $_SESSION['nome'] = $usuario->nome;
        $_SESSION['se_usuario'] = $usuario->se;
        $_SESSION['perfil_usuario'] = $usuario->perfil;

        $_SESSION['usuario_logado'] = true;
        $_SESSION['hora_login'] = time();

    }

    



}



?>