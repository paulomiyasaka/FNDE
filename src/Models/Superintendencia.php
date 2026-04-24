<?php

namespace FNDE\Models;

class Superintendencia
{

    public function __construct
    (
        public readonly string $siglaSe,
        public readonly string $nome

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            siglaSe: $dados->sigla_se,
            nome: $dados->nome

        );
    }
}


?>