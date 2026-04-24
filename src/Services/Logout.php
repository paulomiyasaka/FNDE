<?php

namespace FNDE\Services;

class Logout
{

	
	public static function logout1(): void
    {
    	if (session_status() === PHP_SESSION_NONE) {
        	session_start();
    	}
    		session_regenerate_id(true);
	        session_unset();
	        session_destroy();	        
	        header("Location: ./login.php");
	        exit();
    	        

    }

    public static function logout(?string $motivo = null): void 
	{
	    if (session_status() === PHP_SESSION_NONE) {
	        session_start();
	    }

	    session_unset();
	    session_destroy();

	    // Constrói a URL de redirecionamento
	    $url = "./login.php";
	    if ($motivo) {
	        // urlencode garante que espaços ou caracteres especiais não quebrem a URL
	        $url .= "?motivo=" . urlencode($motivo);
	    }

	    header("Location: " . $url);
	    exit();
	}



    public static function verificarInatividade(int $minutos = 3): void
	{
	    if (session_status() === PHP_SESSION_NONE) session_start();

	    if (isset($_SESSION['hora_login'])) {
	        $tempoInativo = time() - $_SESSION['hora_login'];
	        $limite = $minutos * 60;

	        if ($tempoInativo > $limite) {
	            // Se estourou o tempo, desloga
	            self::logout();
	        }
	    }
	    
	    // Se ainda está ativo, atualiza o timestamp para "renovar" o tempo
	    $_SESSION['hora_login'] = time();
}


}



?>