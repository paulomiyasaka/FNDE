<?php

namespace FNDE\Services;

use FNDE\Database\FuncoesSQL;
use FNDE\Models\Agrupamento;

class ListarAgrupamento
{

    //private int $matricula;  
	
	public function __construct()
	{

	}  

	public function listar():?array
	{
		$sql = "SELECT a.id_agrupamento,
		a.matricula, 
		a.sigla_se,
		a.sigla_centralizadora,
		a.status,
		c.nome_centralizadora,
		DATE_FORMAT(a.data_registro, '%d/%m/%Y - %H:%i') AS data_registro 
		FROM tb_agrupamento as a
		INNER JOIN tb_centralizadora as c
		ON a.sigla_centralizadora = c.sigla_centralizadora 
		ORDER BY data_registro DESC";
		$dados = array();
    	$funcoesSQL = new FuncoesSQL();
		$resultado = $funcoesSQL->fetchAllSQL($sql, $dados);
	        
        $listaDTO = array_map(function($itemIndividual) {
            return Agrupamento::fromArray($itemIndividual);
        }, $resultado);

        return $listaDTO;


	}



}



?>