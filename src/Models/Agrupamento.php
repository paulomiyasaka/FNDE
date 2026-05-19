<?php

namespace FNDE\Models;

class Agrupamento
{

    public function __construct
    (
        public readonly int $id_agrupamento,
        public readonly int $matricula,
        public readonly string $nome_centralizadora,
        public readonly string $sigla_se,
        public readonly string $status

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            idAgrupamento: $dados->id_agrupamento,
            matricula: $dados->matricula,
            nomeCentralizadora: $dados->nome_centralizadora,
            siglaSe: $dados->sigla_se,
            status: $dados->status

        );
    }
}


?>