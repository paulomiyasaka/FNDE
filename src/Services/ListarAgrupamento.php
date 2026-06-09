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
    DATE_FORMAT(a.data_registro, '%d/%m/%Y') AS data_registro,
    DATE_FORMAT(a.data_registro, '%H:%i') AS hora_registro,
    DATE_FORMAT(a.data_registro, '%Y%m%d%H%i') AS data_hora_registro,
    
    -- Novas métricas solicitadas
    COUNT(pa.numero_palete) AS quantidade_paletes,
    SUM(COALESCE(p.quantidade_encomendas, 0)) AS quantidade_encomendas,
    IF(p.peso_previsto > 0, 
    FORMAT(SUM(COALESCE(p.peso_previsto, 0)), 3, 'pt_BR'),
    '0') AS peso_previsto    
FROM tb_agrupamento as a
LEFT JOIN tb_centralizadora as c
    ON a.sigla_centralizadora = c.sigla_centralizadora 

-- Joins necessários para alcançar os dados dos paletes
LEFT JOIN tb_paletes_agrupados as pa 
    ON a.id_agrupamento = pa.id_agrupamento
LEFT JOIN tb_paletes as p 
    ON pa.numero_palete = p.numero_palete

GROUP BY 
    data_hora_registro

ORDER BY data_hora_registro DESC;";
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