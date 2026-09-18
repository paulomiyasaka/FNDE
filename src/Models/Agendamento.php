<?php

namespace FNDE\Models;

class Agendamento
{

    public function __construct
    (
        public readonly int $idAgendamento,
        public readonly string $sku,
        public readonly string $dataSolicitacao,
        public readonly string $dataPrevistaEntrega,
        public readonly string $dataEntregaRealizada,
        public readonly string $arquivoEmailSolicitacao,
        public readonly float $pesoPrevisto,
        public readonly float $pesoEntregue,
        public readonly int $quantidadePaletePrevisto,
        public readonly int $quantidadePaleteEntregue,
        public readonly string $protocoloSei,
        public readonly string $numeroNotaFiscal,
        public readonly string $arquivoNotaFiscal,
        public readonly string $observacaoAgendamento,
        public readonly string $observacaoEntrega,
        public readonly string $status

    ) {}

    public static function fromArray(object $dados): self {
        
        return new self(
            idAgendamento: $dados->id_agendamento,
            sku: $dados->sku,
            dataSolicitacao: $dados->data_solicitacao,
            dataPrevistaEntrega: $dados->data_prevista_entrega,
            dataEntregaRealizada: $dados->data_entrega_realizada,
            arquivoEmailSolicitacao: $dados->arquivo_email_solicitacao,
            pesoPrevisto: $dados->peso_previsto,
            pesoEntregue: $dados->peso_entregue,
            quantidadePaletePrevisto: $dados->quantidade_paletes_previsto,
            quantidadePaleteEntregue: $dados->quantidade_paletes_entregue,
            protocoloSei: $dados->protocolo_sei,
            numeroNotaFiscal: $dados->numero_nota_fiscal,
            arquivoNotaFiscal: $dados->arquivo_nota_fiscal,
            observacaoAgendamento: $dados->observacao_agendamento,
            observacaoEntrega: $dados->observacao_entrega,
            status: $dados->status


        );
    }
}


?>