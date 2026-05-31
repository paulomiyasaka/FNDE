<?php

namespace FNDE\Utils;

use FNDE\Models\Superintendencia;
use FNDE\Database\FuncoesSQL;

class GetSuperintendenciaOrigem
{

    public function retornarSe(): array
    {

        $funcoesSQL = new funcoesSQL();
        $sql = "SELECT s.sigla_se, s.nome 
        FROM tb_se as s 
        INNER JOIN tb_centralizadora_postagem_rt AS rt
        INNER JOIN tb_centralizadora AS c 
        ON rt.sigla_centralizadora = c.sigla_centralizadora AND 
        c.sigla_se = s.sigla_se
        ORDER BY s.sigla_se ASC";

        $dados = array();
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);      
        

        $listaDTO = array_map(function($itemIndividual) {
            return Superintendencia::fromArray($itemIndividual);
        }, $resultado);

        return $listaDTO;


    }



}

        
