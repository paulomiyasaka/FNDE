<?php

namespace FNDE\Services;

use FNDE\Database\FuncoesSQL;
use FNDE\Models\Agendamento;

class AtualizarStatusAgendamento
{

	private int $id;
    private string $status;
	
	public function __construct(int $id, string $status)
	{
		$this->id = $id;
		$this->status = $status;


	}  

	public function alterarStatus()
	{
		$sql = "UPDATE tb_agendamento SET status = :status WHERE id_agrupamento = :id";
		$dados = array(":status" => $this->status, ":id" => $this->id);
    	$funcoesSQL = new FuncoesSQL();
		$resultado = $funcoesSQL->SQL($sql, $dados);
	        
        //return Agrupamento::fromArray($resultado);
		//return $resultado;
		if($resultado){
			return TRUE;
		}else{
			return FALSE;
		}


	}



}



?>