<?php

namespace FNDE\Utils;


class Funcoes{

	//retorna a string somente com números, remove os demais caracteres
	public function somenteNumeros($numero):int
	{
		$numero = preg_replace( '/[^0-9]/', '', $numero );
		$numero = trim($numero);
		if($numero != "" && $numero != NULL && $numero != false){
			return (int)$numero;	
		}else{
			return 0;
		}
	    
	}

	public function converterUTF8($string){

		return utf8_encode($string);


	}


	//verificar se o valor recebido ($_REQUEST) é válido
	//valor deve ser diferente de NULL, vazio e deve existir.
	public function verificarVariavel($valor){

		if(isset($valor) AND ($valor != "") AND ($valor != NULL)){

			return true;

		}else{
			
			return false;
		}

	}


	//EXIBIR ERROS NA TELA
	public function mostrarErros(){
		ini_set('display_errors',1);
		ini_set('display_startup_erros',1);
		error_reporting(E_ALL);
	}

	public function accessControl(){
		header("Content-type: application/json");
		header("Access-Control-Allow-Origin: *");		
		header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
	}

	public function charset(){
		header("Content-Type: text/html; charset=UTF-8",true);
	}
	
	//criptografa senha
	public function criptografar($senha){
		$valor = md5($senha);
		return $valor;
	}

	//valida e-mail
	public function validarEmail($email){
		if($email = filter_var($email, FILTER_VALIDATE_EMAIL)){
			return $email;
		}else{
			return false;
		}

	}


	//converter string em data
	public function converterData($_date = null) {
		$format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
		if ($_date != null && preg_match($format, $_date, $partes)) {
			return $partes[3].'-'.$partes[2].'-'.$partes[1];
		}
		return false;
	}

	//converter string em data
	public function converterDataPadrao($_date = null) {
		$format = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/';
		if ($_date != null && preg_match($format, $_date, $partes)) {
			return $partes[3].'/'.$partes[2].'/'.$partes[1];
		}
		return false;
	}
		

	//pegar dia da semana
	public function diaSemana($dia_semana){
		$dia = array(0 => 'Domingo',
				1 => 'Segunda-feira',
				2 => 'Terça-feira',
				3 => 'Quarta-feira',
				4 => 'Quinta-feira',
				5 => 'Sexta-feira',
				6 => 'Sábado'
			);

		return $dia[$dia_semana];

	}


	public function validarPOST($variavel){

		if(isset($_POST[$variavel]) && $_POST[$variavel] != NULL && $_POST[$variavel] != ""){

			return true;

		}else{

			return false;

		}

	}


	public function maiuscula($texto):string
	{

		return strtoupper($texto);

	}

	public function minuscula($texto):string
	{

		return strtolower($texto);

	}

	public function removerAcentuacao($string):string
	{

    	return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ç)/","/(Ç)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U c C n N"),$string);
	}


	public function validarCNPJ($cnpj){
    // Remove caracteres não numéricos
    $cnpj = preg_replace('/[^0-9]/is', '', $cnpj);

    // Verifica se tem 14 dígitos
    if (strlen($cnpj) != 14) {
        return false;
    }

    // Validação do primeiro dígito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += (int)$cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
        return false;
    }

    // Validação do segundo dígito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += (int)$cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    	$resto = $soma % 11;
    	return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
	}



	public function validarCPF($cpf) {
    // Verifica se o número foi informado
    if(empty($cpf)) {
        //return "O CPF é obrigatório";
        return false;
    }
 
    // Elimina possível máscara
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
     
    // Verifica se o número de dígitos informados é igual a 11
    if (strlen($cpf) != 11) {
        //return "O CPF deve conter 11 dígitos";
        return false;
    }
    // Verifica se nenhuma das sequências inválidas abaixo
    // foi digitada. Caso afirmativo, retorna falso
    else if ($cpf == '00000000000' || 
        $cpf == '11111111111' || 
        $cpf == '22222222222' || 
        $cpf == '33333333333' || 
        $cpf == '44444444444' || 
        $cpf == '55555555555' || 
        $cpf == '66666666666' || 
        $cpf == '77777777777' || 
        $cpf == '88888888888' || 
        $cpf == '99999999999') {
        //return "CPF inválido";
        return false;
     // Calcula os dígitos verificadores para verificar se o
     // CPF é válido
     } else {   
         
        for ($t = 9; $t < 11; $t++) {
             
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                //return "CPF inválido";
                return false;
            }
        }
 
        //return "CPF válido";
        return true;
    }
}

	



}//class funções

?>