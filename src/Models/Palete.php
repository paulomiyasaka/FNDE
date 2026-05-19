<?php

namespace FNDE\Models;

class Palete
{

    public function __construct
    (
        public readonly string $numeroPalete,
        public readonly float $pesoPrevisto,
        public readonly float $pesoMinimo,
        public readonly float $pesoMaximo,
        public readonly string $encomendaInicial,
        public readonly string $encomendaFinal,
        public readonly string $sku,
        public readonly int $quantidadeEncomendas,
        public readonly int $faseUnitizacao,
        public readonly string $siglaCentralizadora,
        public readonly string $siglaSe

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            numeroPalete: $dados->numero_palete,
            pesoPrevisto: $dados->peso_previsto,
            pesoMinimo: $dados->peso_minimo,
            pesoMaximo: $dados->peso_maximo,
            encomendaInicial: $dados->encomenda_inicial,
            encomendaFinal: $dados->encomenda_final,
            sku: $dados->sku,
            quantidadeEncomendas: $dados->quantidade_encomendas,
            faseUnitizacao: $dados->fase_unitizacao,
            siglaCentralizadora: $dados->sigla_centralizadora,
            siglaSe: $dados->sigla_se

        );
    }
}


?>