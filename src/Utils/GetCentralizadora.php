<?php

namespace FNDE\Utils;

use FNDE\Models\Centralizadora;
use FNDE\Database\FuncoesSQL;

class GetCentralizadora
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
        $sql = "SELECT s.nome_centralizadora, s.sigla_se FROM tb_centralizadora as s WHERE s.sigla_se = :se ORDER BY s.nome_centralizadora ASC";

        $dados = array(':se' => $se);
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);      

        $listaDTO = array_map(function($itemIndividual) {
            return Centralizadora::fromArray($itemIndividual);
        }, $resultado);

        return $listaDTO;


    }



}

        
