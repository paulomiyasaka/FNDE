<?php

namespace FNDE\Utils;

use FNDE\Models\Centralizadora;
use FNDE\Database\FuncoesSQL;

class GetCentralizadora
{

    public function retornarCentralizadora(): array
    {

        $funcoesSQL = new funcoesSQL();
        $sql = "SELECT s.nome_centralizadora, s.sigla_se FROM tb_centralizadora as s ORDER BY s.nome_centralizadora ASC";

        $dados = array();
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);      

        $listaDTO = array_map(function($itemIndividual) {
            return Centralizadora::fromArray($itemIndividual);
        }, $resultado);

        return $listaDTO;


    }



}

        
