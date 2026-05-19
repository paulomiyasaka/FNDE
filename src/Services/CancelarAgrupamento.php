<?php

namespace FNDE\Services;

use FNDE\Database\FuncoesSQL;
use FNDE\Models\Agrupamento;

class CancelarAgrupamento
{

	private int $id;
    private string $status;
	
	public function __construct(int $id, string $status)
	{
		$this->id = $id;
		$this->status = $status;


	}  

	public function cancelar()
	{
		$sql = "UPDATE tb_agrupamento SET status = :status WHERE id_agrupamento = :id";
		$dados = array(":status" => $this->status, ":id" => $this->id);
    	$funcoesSQL = new FuncoesSQL();
		$resultado = $funcoesSQL->SQL($sql, $dados);
	        
        //return Agrupamento::fromArray($resultado);
		return $resultado;


	}



}



?>