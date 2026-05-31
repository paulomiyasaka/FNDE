<?php

namespace FNDE\Utils;

use FNDE\Models\Centralizadora;
use FNDE\Database\FuncoesSQL;

class GetCentralizadoraOrigem
{

    protected ?string $se = '';

    public function setSE($se){
        $this->se = $se;
    }

    public function getSE(){
        return $this->se;
    }

    public function retornarCentralizadora(): array
    {

        $se = $this->getSE();

        $funcoesSQL = new funcoesSQL();
        $sql = "SELECT rt.sigla_centralizadora, 
        c.nome_centralizadora, 
        c.sigla_se 
        FROM tb_centralizadora_postagem_rt as rt 
        INNER JOIN tb_centralizadora AS c 
        INNER JOIN tb_se AS s
        ON rt.sigla_centralizadora = c.sigla_centralizadora AND
        s.sigla_se = c.sigla_se 
        WHERE s.sigla_se = :se 
        ORDER BY rt.sigla_centralizadora ASC";

        $dados = array(':se' => $se);
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);      

        $listaDTO = array_map(function($itemIndividual) {
            return Centralizadora::fromArray($itemIndividual);
        }, $resultado);

        return $listaDTO;


    }



}

        
