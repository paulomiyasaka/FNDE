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
	private string $sigla_centralizadora_origem;
    private string $sigla_se_origem;
	
	public function __construct(int $matricula, string $sigla_se_origem, string $sigla_centralizadora_origem, string $sigla_se, string $sigla_centralizadora, string $status)
	{
		$this->matricula = $matricula;
        $this->sigla_centralizadora = $sigla_centralizadora;
        $this->sigla_se = $sigla_se;
		$this->status = $status;
		$this->sigla_centralizadora_origem = $sigla_centralizadora_origem;
        $this->sigla_se_origem = $sigla_se_origem;


	}  

	public function criar()
	{
		$sql = "INSERT INTO tb_agrupamento (matricula, sigla_se_origem, sigla_centralizadora_origem, sigla_se, sigla_centralizadora, status) VALUES (:matricula, :sigla_se_origem, :sigla_centralizadora_origem, :sigla_se, :sigla_centralizadora, :status)";
		$dados = array(":matricula" => $this->matricula,  ":sigla_se_origem" => $this->sigla_se_origem, ":sigla_centralizadora_origem" => $this->sigla_centralizadora_origem,  ":sigla_se" => $this->sigla_se, ":sigla_centralizadora" => $this->sigla_centralizadora, ":status" => $this->status);
    	$funcoesSQL = new FuncoesSQL();
		$resultado = $funcoesSQL->SQL($sql, $dados);
	        
        //return Agrupamento::fromArray($resultado);
		return $resultado;


	}



}



?>