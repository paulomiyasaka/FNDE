<?php

namespace FNDE\Models;

class Agrupamento
{

    public function __construct
    (
        public readonly string $matricula,
        public readonly string $nome_centralizadora,
        public readonly string $sigla_se,
        public readonly string $status

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            matricula: $dados->matricula,
            nomeCentralizadora: $dados->nome_centralizadora,
            siglaSe: $dados->sigla_se,
            status: $dados->status

        );
    }
}


?>