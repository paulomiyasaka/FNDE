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
		$sql = "INSERT INTO tb_agrupamento (matricula, nome_centralizadora, sigla_se, status) VALUES (:matricula, :nome_centralizadora, :sigla_se, :status)";
		$dados = array(":matricula" => $matricula, ":nome_centralizadora" => $nome_centralizadora, ":sigla_se" => $sigla_se, ":status" => $status);
    	$funcoesSQL = new FuncoesSQL();
		$resultado = $funcoesSQL->SQL($sql, $dados);
	        
        return Agrupamento::fromArray($resultado);

	}  



}



?>