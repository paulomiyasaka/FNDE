<?php

namespace FNDE\Utils;

use FNDE\Models\Superintendencia;
use FNDE\Database\FuncoesSQL;

class GetSuperintendencia
{

    public function retornarSe(): array
    {

        $funcoesSQL = new funcoesSQL();
        $sql = "SELECT s.sigla_se, s.nome FROM tb_se as s ORDER BY s.sigla_se ASC";

        $dados = array();
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);      
        

        $listaDTO = array_map(function($itemIndividual) {
            return Superintendencia::fromArray($itemIndividual);
        }, $resultado);

        return $listaDTO;


    }



}

        
