<?php

namespace FNDE\Services;

use FNDE\Models\PaletesAgrupados;
use FNDE\Database\FuncoesSQL;

class GetAgrupamento
{

    protected ?string $agrupamento = '';

    public function setAgrupamento($agrupamento){
        $this->agrupamento = $agrupamento;
    }

    public function getAgrupamento(){
        return $this->agrupamento;
    }

    public function retornarPaletes(): array
    {

        $id_agrupamento = $this->getAgrupamento();

        $funcoesSQL = new funcoesSQL();
        $sql = "SELECT id_agrupamento, numero_palete FROM tb_paletes_agrupados WHERE id_agrupamento = :id_agrupamento";

        $dados = array(':id_agrupamento' => $id_agrupamento);
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);      

        $listaDTO = array_map(function($itemIndividual) {
            return PaletesAgrupados::fromArray($itemIndividual);
        }, $resultado);

        return $listaDTO;


    }


    public function dadosAgrupamento()
    {
        $agrupamento = $this->getAgrupamento();
    /*
        $funcoesSQL = new funcoesSQL();
        $sql = "SELECT id_agrupamento, numero_palete FROM tb_paletes_agrupados WHERE id_agrupamento = :id_agrupamento";

        $dados = array(':id_agrupamento' => $id_agrupamento);
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);      

        $listaDTO = array_map(function($itemIndividual) {
            return PaletesAgrupados::fromArray($itemIndividual);
        }, $resultado);
    */
        return $listaDTO;

    }



}

        
