<?php

namespace FNDE\Models;

class idAgrupamento
{

    public function __construct
    (
        public readonly int $idAgrupamento,
        public readonly string $palete,
        public readonly string $status

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            idAgrupamento: $dados->id_agrupamento,
            palete: $dados->numero_palete,
            status: $dados->status

        );
    }
}


?>