<?php
namespace FNDE\Database;

use FNDE\Database\FuncoesSQL;
use \PDO;

class FuncoesSQL extends conecta{


	final public function SQL(string $sql, array $dados){
		
		$query = conecta::executarSQL($sql, $dados);
		//$resultado = $query->fetch(PDO::FETCH_OBJ);
		
		//$row = $query->rowCount();
		//if($row > 0){
		if($query){
			$row = $query->rowCount();
			if($row > 0){				
				return TRUE;
			}else{
				return FALSE;
			}
			
		}else{
			return FALSE;
		}
		//return $query->rowCount();	
	}

	final public function fetchAllSQL(string $sql, array $dados){
		$query = conecta::executarSQL($sql, $dados);
		return $query->fetchAll(PDO::FETCH_OBJ);			
	}

	final public function fetchSQL(string $sql, array $dados){
		$query = conecta::executarSQL($sql, $dados);
		return $query->fetch(PDO::FETCH_OBJ);			
	}

	final public function executarScriptSQL(string $nomeArquivo, string $caminho){
		$query = conecta::execScriptSQL($nomeArquivo, $caminho);
		if($query){
			return true;
		}else{
			return false;
		}
					
	}

	
	

}


?>