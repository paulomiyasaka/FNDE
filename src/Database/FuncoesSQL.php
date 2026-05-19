<?php
namespace FNDE\Database;

use FNDE\Database\FuncoesSQL;
use \PDO;

class FuncoesSQL extends conecta{


	final public function SQL(string $sql, array $dados){
		
		//$query = conecta::executarSQL($sql, $dados);
		$query = $this->executarSQL($sql, $dados);
    
		// 2. Verifica se a execução retornou o objeto PDOStatement com sucesso
		if ($query instanceof \PDOStatement) {
			
			// 3. Usa o método já existente na sua classe para pegar o ID
			$id = $this->lastidSQL();
			
			// Se o ID for maior que zero, retorna ele. 
			// Caso contrário (como em UPDATE/DELETE), retorna true indicando sucesso.
			return ($id > 0) ? $id : true;
			
		} else {
			// Se deu erro ou retornou string de exceção
			return false;
		}

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