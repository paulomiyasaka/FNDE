<?php

namespace FNDE\Services;

use FNDE\Database\FuncoesSQL;
use FNDE\Models\Agrupamento;

class RegistrarAgrupamento
{

	private int $matricula;
    private string $sigla_centralizadora;
    private string $sigla_se;
    private string $status;
	
	public function __construct(int $matricula, string $sigla_centralizadora, string $sigla_se, string $status)
	{
		$this->matricula = $matricula;
        $this->sigla_centralizadora = $sigla_centralizadora;
        $this->sigla_se = $sigla_se;
		$this->status = $status;


	}  

	public function criar()
	{
		$sql = "INSERT INTO tb_agrupamento (matricula, sigla_centralizadora, sigla_se, status) VALUES (:matricula, :sigla_centralizadora, :sigla_se, :status)";
		$dados = array(":matricula" => $this->matricula, ":sigla_centralizadora" => $this->sigla_centralizadora, ":sigla_se" => $this->sigla_se, ":status" => $this->status);
    	$funcoesSQL = new FuncoesSQL();
		$resultado = $funcoesSQL->SQL($sql, $dados);
	        
        //return Agrupamento::fromArray($resultado);
		return $resultado;


	}



}



?>