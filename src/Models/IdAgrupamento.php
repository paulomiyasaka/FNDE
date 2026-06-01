<?php

namespace FNDE\Models;

class idAgrupamento
{

    public function __construct
    (
        public readonly int $idAgrupamento,
        public readonly string $palete,
        public readonly string $status,
        public readonly string $siglaSe,
        public readonly string $siglaCentralizadora,
        public readonly string $nomeCentralizadora,
        public readonly string $siglaSeOrigem,
        public readonly string $siglaCentralizadoraOrigem,
        public readonly string $nomeCentralizadoraOrigem

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            idAgrupamento: $dados->id_agrupamento,
            palete: $dados->numero_palete,
            status: $dados->status,
            siglaSe: $dados->sigla_se,
            siglaCentralizadora: $dados->sigla_centralizadora,
            nomeCentralizadora: $dados->nome_centralizadora,
            siglaSeOrigem: $dados->sigla_se_origem,
            siglaCentralizadoraOrigem: $dados->sigla_centralizadora_origem,
            nomeCentralizadoraOrigem: $dados->nome_centralizadora_origem

        );
    }
}


?>