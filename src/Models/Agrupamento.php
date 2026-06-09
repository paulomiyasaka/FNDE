<?php

namespace FNDE\Models;

class Agrupamento
{

    public function __construct
    (
        public readonly int $idAgrupamento,
        public readonly int $matricula,
        public readonly string $siglaCentralizadora,
        public readonly string $nomeCentralizadora,
        public readonly string $siglaSe,
        public readonly string $status,
        public readonly string $dataRegistro,
        public readonly string $horaRegistro,
        public readonly string $dataHoraRegistro,
        public readonly int $quantidadePaletes,
        public readonly int $quantidadeEncomendas,
        public readonly string $pesoEstimado

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            idAgrupamento: $dados->id_agrupamento,
            matricula: $dados->matricula,
            siglaCentralizadora: $dados->sigla_centralizadora,
            nomeCentralizadora: $dados->nome_centralizadora,
            siglaSe: $dados->sigla_se,
            status: $dados->status,
            dataRegistro: $dados->data_registro,
            horaRegistro: $dados->hora_registro,
            dataHoraRegistro: $dados->data_hora_registro,
            quantidadePaletes: $dados->quantidade_paletes,
            quantidadeEncomendas: $dados->quantidade_encomendas,
            pesoEstimado: $dados->peso_previsto

        );
    }
}


?>