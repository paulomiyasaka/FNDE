<?php

namespace FNDE\Models;

class Centralizadora
{

    public function __construct
    (
        public readonly string $nomeCentralizadora,
        public readonly string $siglaSe

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            nomeCentralizadora: $dados->nome_centralizadora,
            siglaSe: $dados->sigla_se            

        );
    }
}


?>