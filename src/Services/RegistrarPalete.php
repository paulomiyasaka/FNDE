<?php

namespace FNDE\Services;

use FNDE\Database\FuncoesSQL;
use FNDE\Models\Palete;

class RegistrarPalete
{

	private string $numero_palete;
    private int $idAgrupamento;
    //private float $peso_previsto;
    //private float $peso_minimo;
    //private float $peso_maximo;
    //private string $encomenda_inicial;
    //private string $encomenda_final;
    //private string $sku;
    //private int $quantidade_encomendas;
    //private int $fase_unitizacao;
    //private string $sigla_centralizadora;
    //private string $sigla_se;
	
	public function __construct(int $idAgrupamento, string $numero_palete)
	{

        $this->idAgrupamento = $idAgrupamento;
        $this->numero_palete = $numero_palete;     
        

	}  

    public function consultar(){

        $sql = "SELECT COUNT(*) AS qtd_palete FROM tb_paletes WHERE numero_palete = :numero_palete";
        $dados = array(":numero_palete" => $this->numero_palete);
        $funcoesSQL = new FuncoesSQL();  
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);
        return $resultado;

    }

    public function remover(){

        $sql = "DELETE FROM tb_paletes_agrupados WHERE numero_palete = :numero_palete AND id_agrupamento = :id_agrupamento";
        $dados = array(":id_agrupamento" => $this->id_agrupamento, ":numero_palete" => $this->numero_palete);
        $funcoesSQL = new FuncoesSQL();  
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);
        return $resultado;

    }

    public function agrupar(){

        $sql = "INSERT INTO tb_paletes_agrupados (id_agrupamento, numero_palete) VALUES (:id_agrupamento, :numero_palete) ";
        $dados = array(":id_agrupamento" => $this->idAgrupamento, ":numero_palete" => $this->numero_palete);
        $funcoesSQL = new FuncoesSQL();  
        $resultado = $funcoesSQL->SQL($sql, $dados);
        if($resultado){
            return TRUE;
        }else{
            return FALSE;
        }


    }



	public function registrar(string $numero_palete, float $peso_previsto, float $peso_minimo, float $peso_maximo, string $encomenda_inicial, string $encomenda_final, string $sku, int $quantidade_encomendas, int $fase_unitizacao, string $sigla_centralizadora, string $sigla_se)
	{
        
		$sql = "INSERT INTO tb_paletes (numero_palete, peso_previsto, peso_minimo, peso_maximo, encomenda_inicial, encomenda_final, sku, quantidade_encomendas, fase_unitizacao, sigla_centralizadora, sigla_se) VALUES (:numero_palete, :peso_previsto, :peso_minimo, :peso_maximo, :encomenda_inicial, :encomenda_final, :sku, :quantidade_encomendas, :fase_unitizacao, :sigla_centralizadora, :sigla_se)";
		$dados = array(":numero_palete" => $numero_palete, ":peso_previsto" => $peso_previsto, ":peso_minimo" => $peso_minimo, ":peso_maximo" => $peso_maximo, ":encomenda_inicial" => $encomenda_inicial, ":encomenda_final" => $encomenda_final, ":sku" => $sku, ":quantidade_encomendas" => $quantidade_encomendas, ":fase_unitizacao" => $fase_unitizacao, ":sigla_centralizadora" => $sigla_centralizadora, ":sigla_se" => $sigla_se);
    	        
        $funcoesSQL = new FuncoesSQL();
		$resultado = $funcoesSQL->SQL($sql, $dados);
	        
        //return Agrupamento::fromArray($resultado);
		return $resultado;


	}



}



?>