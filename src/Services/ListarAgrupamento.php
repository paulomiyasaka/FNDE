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
		$sql = "SELECT * FROM tb_agrupamento ORDER BY data_registro DESC";
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