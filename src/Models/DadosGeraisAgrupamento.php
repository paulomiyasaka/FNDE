<?php

namespace FNDE\Models;

class DadosGeraisAgrupamento
{

    public function __construct
    (
        public readonly int $idAgrupamento,
        public readonly string $statusAgrupamento,
        public readonly string $dataCriacaoAgrupamento,
        public readonly string $nomeCentralizadora,
        public readonly string $siglaCentralizadora,
        public readonly string $siglaSe,
        public readonly int $matricula,
        public readonly int $totalPaletes,
        public readonly float $pesoTotalAgrupamento,
        public readonly string $qrCompilacao,
        public readonly string $nomeCentralizadoraOrigem,
        public readonly string $siglaCentralizadoraOrigem,
        public readonly string $siglaSeOrigem,

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            idAgrupamento: $dados->id_agrupamento,
            statusAgrupamento: $dados->status_agrupamento,
            dataCriacaoAgrupamento: $dados->data_criacao_agrupamento,
            nomeCentralizadora: $dados->nome_centralizadora_destino,
            siglaCentralizadora: $dados->sigla_centralizadora_destino,
            siglaSe: $dados->sigla_se_destino,
            matricula: $dados->matricula,
            totalPaletes: $dados->total_paletes,
            pesoTotalAgrupamento: $dados->peso_total_agrupamento,
            qrCompilacao: $dados->qr_master,
            siglaSeOrigem: $dados->sigla_se_origem,
            siglaCentralizadoraOrigem: $dados->sigla_centralizadora_origem,
            nomeCentralizadoraOrigem: $dados->nome_centralizadora_origem
        );
    }
}


?>