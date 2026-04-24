<?php

namespace FNDE\Services;

require '../../vendor/autoload.php';

use FNDE\Database\FuncoesSQL;
use FNDE\Models\OrigemAR;

class ConsultarOrigem{

	protected int $codigoCaixa;

	public function __construct(int $codigoCaixa)
	{

		$this->codigoCaixa = $codigoCaixa;
		//return $this->consultar();
	}

	public function consultar(): ?OrigemAR
	{

		$codigoCaixa = $this->codigoCaixa;
		$funcoesSQL = new funcoesSQL();
		$sql = "SELECT 
		ar.numero_caixa,
		ar.sigla_cliente, 
		ori.mcu_origem, 
		ori.unidade, 
		ori.matricula_gerente, 
		u.nome as nome_gerente, 
		ori.sigla_se, 
		ori.cnpj,
		ori.logradouro,
		ori.numero,
		ori.complemento,
		ori.bairro,
		ori.cidade,
		ori.uf,
		ori.cep 
		FROM tb_endereco_origem as ori 
		JOIN tb_armazenamento_ar as ar 
		JOIN tb_usuario as u 
		ON ori.sigla_se = u.sigla_se 
		AND u.matricula = ori.matricula_gerente 
		AND ar.sigla_se_armazenamento = u.sigla_se 
		WHERE ar.numero_caixa = :numero_caixa";

		$dados = array(":numero_caixa" => $codigoCaixa);
		$resultado = $funcoesSQL->fetchAllSQL($sql, $dados);
        if (empty($resultado)) {
            return null;
        }

        return OrigemAR::fromArray($resultado);
		

	}




}



?>