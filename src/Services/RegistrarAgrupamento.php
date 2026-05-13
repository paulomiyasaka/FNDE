<?php

namespace FNDE\Services;

use FNDE\Database\FuncoesSQL;
use FNDE\Models\Agrupamento;

class RegistrarAgrupamento
{

	private int $matricula;
    private string $nome_centralizadora;
    private string $sigla_se;
    private string $status;
	
	public function __construct(int $matricula, string $nome_centralizadora, string $sigla_se, string $status)
	{
		$this->matricula = $matricula;
        $this->nome_centralizadora = $nome_centralizadora;
        $this->sigla_se = $sigla_se;
        $this->status = $status;

	}

	public function criar(): Agrupamento
	{
		$matricula = $this->matricula;
        $nome_centralizadora = $this->nome_centralizadora;
        $sigla_se = $this->sigla_se;
        $status = $this->status;

		$funcoesSQL = new funcoesSQL();
		$sql = "INSERT INTO tb_agrupamento AS a (a.matricula, a.nome_centralizadora, a.sigla_se, a.status) VALUES (:matricula, :nome_centralizadora, :sigla_se, :status)";
		$dados = array(":matricula" => $matricula, ":nome_centralizadora" => $nome_centralizadora, ":sigla_se" => $sigla_se, ":status" => $status);
    	$resultado = $funcoesSQL->fetchAllSQL($sql, $dados);
	        
        return Agrupamento::fromArray($resultado);

	}  



}



?>