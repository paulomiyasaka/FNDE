<?php

namespace FNDE\Services;

use FNDE\Models\PaletesAgrupados;
use FNDE\Models\DadosGeraisAgrupamento;
use FNDE\Models\IdAgrupamento;
use FNDE\Database\FuncoesSQL;

class GetAgrupamento
{

    protected ?string $agrupamento = '';
    protected ?string $palete = '';

    public function setAgrupamento($agrupamento){
        $this->agrupamento = $agrupamento;
    }

    public function getAgrupamento(){
        return $this->agrupamento;
    }

    public function setPalete($palete){
        $this->palete = $palete;
    }

    public function getPalete(){
        return $this->palete;
    }

    public function retornarPaletes(): array
    {

        $id_agrupamento = $this->getAgrupamento();

        $funcoesSQL = new funcoesSQL();
        //$sql = "SELECT id_agrupamento, numero_palete FROM tb_paletes_agrupados WHERE id_agrupamento = :id_agrupamento";
        $sql = "SELECT 
    pa.id_agrupamento,
    p.numero_palete,
    p.peso_previsto,
    CONCAT(
        RPAD(COALESCE(p.numero_palete, ''), 11, ' '),
        RPAD(COALESCE(CAST(p.peso_previsto AS CHAR), ''), 11, '0'),
        RPAD(COALESCE(CAST(p.peso_minimo AS CHAR), ''), 11, '0'),
        RPAD(COALESCE(CAST(p.peso_maximo AS CHAR), ''), 11, '0'),
        RPAD(COALESCE(p.encomenda_inicial, ''), 13, ' '),
        RPAD(COALESCE(p.encomenda_final, ''), 13, ' '),
        RPAD(COALESCE(p.sku, ''), 15, ' '),
        LPAD(COALESCE(CAST(p.quantidade_encomendas AS CHAR), ''), 4, '0'),
        LPAD(COALESCE(CAST(p.fase_unitizacao AS CHAR), ''), 2, '0'),
        RPAD(COALESCE(p.sigla_centralizadora, ''), 3, ' '),
        RPAD(COALESCE(p.sigla_se, ''), 3, ' ')
    ) AS qr_97_chars
FROM tb_paletes p
INNER JOIN tb_centralizadora c       ON p.sigla_centralizadora = c.sigla_centralizadora
INNER JOIN tb_se s                   ON p.sigla_se = s.sigla_se
INNER JOIN tb_paletes_agrupados pa   ON p.numero_palete = pa.numero_palete
-- Novo JOIN para poder validar o status do agrupamento
INNER JOIN tb_agrupamento a          ON pa.id_agrupamento = a.id_agrupamento
WHERE pa.id_agrupamento = :id_agrupamento
  AND a.status = :status;";

        $dados = array(':id_agrupamento' => $id_agrupamento, ':status' => 'FECHADO');
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados);      

        $listaDTO = array_map(function($itemIndividual) {
            return PaletesAgrupados::fromArray($itemIndividual);
        }, $resultado);

        return $listaDTO;


    }


    public function retornarDadosGerais():array
    {
        $id_agrupamento = $this->getAgrupamento();
    
        $funcoesSQL = new funcoesSQL();
        /*$sql = "SELECT 
    a.id_agrupamento,
    a.status AS status_agrupamento,
    a.data_registro AS data_criacao_agrupamento,
    c.sigla_centralizadora,
    c.nome_centralizadora,
    s.sigla_se,
    s.nome AS nome_se,
    a.matricula,
    a.sigla_se_origem,
    a.sigla_centralizadora_origem,     
    c.nome_centralizadora AS nome_centralizadora_origem,
    COUNT(pa.numero_palete) AS total_paletes,
    SUM(COALESCE(p.peso_previsto, 0)) AS peso_total_agrupamento,
    CONCAT(
        LPAD(FORMAT(SUM(COALESCE(p.peso_previsto, 0)), 3, 'en_US'), 11, '0'),
        LPAD(CAST(COUNT(pa.numero_palete) AS CHAR), 4, '0'),
        RPAD(COALESCE(c.sigla_centralizadora, ''), 3, ' '),
        RPAD(COALESCE(s.sigla_se, ''), 3, ' ')
    ) AS qr_master -- <--- CONFIRME ESTE NOME
FROM tb_agrupamento a
INNER JOIN tb_centralizadora c       ON a.sigla_centralizadora = c.sigla_centralizadora AND a.sigla_centralizadora_origem = c.sigla_centralizadora 
INNER JOIN tb_se s                   ON a.sigla_se = s.sigla_se
INNER JOIN tb_paletes_agrupados pa   ON a.id_agrupamento = pa.id_agrupamento
INNER JOIN tb_paletes p              ON pa.numero_palete = p.numero_palete
WHERE a.id_agrupamento = :id_agrupamento
  AND a.status = :status
GROUP BY 
    a.id_agrupamento, a.status, a.data_registro, 
    c.sigla_centralizadora, c.nome_centralizadora, 
    s.sigla_se, s.nome, a.matricula;";
    */
        $sql = "SELECT 
    a.id_agrupamento,
    a.status AS status_agrupamento,
    a.data_registro AS data_criacao_agrupamento,
    
    -- Dados de Destino (usando o alias c_dest e s_dest)
    c_dest.sigla_centralizadora AS sigla_centralizadora_destino,
    c_dest.nome_centralizadora AS nome_centralizadora_destino,
    s_dest.sigla_se AS sigla_se_destino,
    s_dest.nome AS nome_se_destino,
    
    a.matricula,
    
    -- Dados de Origem (usando o alias c_orig)
    a.sigla_se_origem,
    a.sigla_centralizadora_origem,     
    c_orig.nome_centralizadora AS nome_centralizadora_origem,
    
    -- Agregações
    COUNT(pa.numero_palete) AS total_paletes,
    SUM(COALESCE(p.peso_previsto, 0)) AS peso_total_agrupamento,
    
    -- Montagem do QR Master (utilizando os dados de destino conforme seu padrão)
    CONCAT(
        LPAD(FORMAT(SUM(COALESCE(p.peso_previsto, 0)), 3, 'en_US'), 11, '0'),
        LPAD(CAST(COUNT(pa.numero_palete) AS CHAR), 4, '0'),
        RPAD(COALESCE(c_dest.sigla_centralizadora, ''), 3, ' '),
        RPAD(COALESCE(s_dest.sigla_se, ''), 3, ' ')
    ) AS qr_master
    
FROM tb_agrupamento a
-- 1. JOIN para a Centralizadora de DESTINO
INNER JOIN tb_centralizadora c_dest 
    ON a.sigla_centralizadora = c_dest.sigla_centralizadora
    
-- 2. JOIN para a SE de DESTINO
INNER JOIN tb_se s_dest 
    ON a.sigla_se = s_dest.sigla_se

-- 3. JOIN para a Centralizadora de ORIGEM (Aqui está a correção!)
INNER JOIN tb_centralizadora c_orig 
    ON a.sigla_centralizadora_origem = c_orig.sigla_centralizadora

-- Demais JOINS da estrutura de paletes
INNER JOIN tb_paletes_agrupados pa 
    ON a.id_agrupamento = pa.id_agrupamento
INNER JOIN tb_paletes p 
    ON pa.numero_palete = p.numero_palete

WHERE a.id_agrupamento = :id_agrupamento
  AND a.status = :status

GROUP BY 
    a.id_agrupamento, 
    a.status, 
    a.data_registro, 
    c_dest.sigla_centralizadora, 
    c_dest.nome_centralizadora, 
    s_dest.sigla_se, 
    s_dest.nome, 
    a.matricula,
    a.sigla_se_origem,
    a.sigla_centralizadora_origem,
    c_orig.nome_centralizadora;";
        $dados = array(':id_agrupamento' => $id_agrupamento, ':status' => "FECHADO");
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados); 

        $listaDTO = array_map(function($itemIndividual) {
            return DadosGeraisAgrupamento::fromArray($itemIndividual);
        }, $resultado);
    
        return $listaDTO;

    }


    public function buscarAgrupamento():array
    {
        $palete = $this->getPalete();
    
        $funcoesSQL = new funcoesSQL();
        $sql = "SELECT 
        pa.id_agrupamento,
        pa.numero_palete,
        a.sigla_se,
        c.nome_centralizadora,
        c.sigla_centralizadora,
        a.status,
        a.sigla_se_origem,
        a.sigla_centralizadora_origem,     
        c.nome_centralizadora AS nome_centralizadora_origem
        FROM tb_paletes_agrupados pa 
        INNER JOIN tb_agrupamento a 
        INNER JOIN tb_centralizadora c
        ON pa.id_agrupamento = a.id_agrupamento 
        AND c.sigla_se = a.sigla_se
        WHERE pa.numero_palete = :palete 
        ORDER BY pa.id_agrupamento DESC 
        LIMIT 1";
        $dados = array(':palete' => $palete);
        $resultado = $funcoesSQL->fetchAllSQL($sql, $dados); 

        $listaDTO = array_map(function($itemIndividual) {
            return IdAgrupamento::fromArray($itemIndividual);
        }, $resultado);
    
        return $listaDTO;

    }



}

        
