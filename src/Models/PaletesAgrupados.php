<?php

namespace FNDE\Models;

class PaletesAgrupados
{

    public function __construct
    (
        public readonly int $idAgrupamento,
        public readonly string $numeroPalete

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            idAgrupamento: $dados->id_agrupamento,
            numeroPalete: $dados->numero_palete

        );
    }
}


?>