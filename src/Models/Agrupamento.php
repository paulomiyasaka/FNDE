<?php

namespace FNDE\Models;

class Agrupamento
{

    public function __construct
    (
        public readonly int $idAgrupamento,
        public readonly int $matricula,
        public readonly string $siglaCentralizadora,
        public readonly string $siglaSe,
        public readonly string $status

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            idAgrupamento: $dados->id_agrupamento,
            matricula: $dados->matricula,
            siglaCentralizadora: $dados->sigla_centralizadora,
            siglaSe: $dados->sigla_se,
            status: $dados->status

        );
    }
}


?>