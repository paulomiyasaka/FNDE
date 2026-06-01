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
		$sql = "SELECT 
    a.id_agrupamento,
    a.matricula, 
    a.sigla_se,
    a.sigla_centralizadora,
    a.status,
    c.nome_centralizadora,
    DATE_FORMAT(a.data_registro, '%d/%m/%Y - %H:%i') AS data_registro,
    
    -- Novas métricas solicitadas
    COUNT(pa.numero_palete) AS quantidade_paletes,
    SUM(COALESCE(p.quantidade_encomendas, 0)) AS quantidade_encomendas
    
FROM tb_agrupamento as a
INNER JOIN tb_centralizadora as c
    ON a.sigla_centralizadora = c.sigla_centralizadora 

-- Joins necessários para alcançar os dados dos paletes
INNER JOIN tb_paletes_agrupados as pa 
    ON a.id_agrupamento = pa.id_agrupamento
INNER JOIN tb_paletes as p 
    ON pa.numero_palete = p.numero_palete

GROUP BY 
    a.id_agrupamento,
    a.matricula, 
    a.sigla_se,
    a.sigla_centralizadora,
    a.status,
    c.nome_centralizadora,
    a.data_registro -- Usamos a coluna original no GROUP BY

ORDER BY a.data_registro DESC;";
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