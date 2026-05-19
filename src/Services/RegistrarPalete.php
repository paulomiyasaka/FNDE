<?php

namespace FNDE\Services;

use FNDE\Database\FuncoesSQL;
use FNDE\Models\Palete;

class RegistrarPalete
{

	    private string $numero_palete;
        private float $peso_previsto;
        private float $peso_minimo;
        private float $peso_maximo;
        private string $encomenda_inicial;
        private string $encomenda_final;
        private string $sku;
        private int $quantidade_encomendas;
        private int $fase_unitizacao;
        private string $sigla_centralizadora;
        private string $sigla_se;
	
	public function __construct(string $numero_palete, float $peso_previsto, float $peso_minimo, float $peso_maximo, string $encomenda_inicial, string $encomenda_final, string $sku, int $quantidade_encomendas, int $fase_unitizacao, string $sigla_centralizadora, string $sigla_se)
	{

        $this->numero_palete = $numero_palete;
        $this->peso_previsto = $peso_previsto;
        $this->peso_minimo = $peso_minimo;
        $this->peso_maximo = $peso_maximo;
        $this->encomenda_inicial = $encomenda_inicial;
        $this->encomenda_final = $encomenda_final;
        $this->sku = $sku;
        $this->quantidade_encomendas = $quantidade_encomendas;
        $this->fase_unitizacao = $fase_unitizacao;
        $this->sigla_centralizadora = $sigla_centralizadora;
        $this->sigla_se = $sigla_se;

        

	}  

	public function registrar()
	{
		$sql = "INSERT INTO tb_paletes (numero_palete, peso_previsto, peso_minimo, peso_maximo, encomenda_inicial, encomenda_final, sku, quantidade_encomendas, fase_unitizacao, sigla_centralizadora, sigla_se) VALUES (:numero_palete, :peso_previsto, :peso_minimo, :peso_maximo, :encomenda_inicial, :encomenda_final, :sku, :quantidade_encomendas, :fase_unitizacao, :sigla_centralizadora, :sigla_se)";
		$dados = array(":numero_palete" => $this->numero_palete, ":peso_previsto" => $this->peso_previsto, ":peso_minimo" => $this->peso_minimo, ":peso_maximo" => $this->peso_maximo, ":encomenda_inicial" => $this->encomenda_inicial, ":encomenda_final" => $this->encomenda_final, ":sku" => $this->sku, ":quantidade_encomendas" => $this->quantidade_encomendas, ":fase_unitizacao" => $this->fase_unitizacao, ":sigla_centralizadora" => $this->sigla_centralizadora, ":sigla_se" => $this->sigla_se);
    	$funcoesSQL = new FuncoesSQL();
		$resultado = $funcoesSQL->SQL($sql, $dados);
	        
        //return Agrupamento::fromArray($resultado);
		return $resultado;


	}



}



?>