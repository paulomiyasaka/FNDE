<?php
namespace FNDE\Services;

use Mpdf\Mpdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

class GerarRotuloTesteUnicoRetratoQRCodeSemBorda {
    private $mpdf;
    private $limitePaletes;
    private $pesoTara;

    public function __construct() {
        // Inicializa o mPDF configurado estritamente para Retrato (Portrait)
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10
        ]);
        $this->limitePaletes = 23;
        $this->pesoTara = 17.0;
    }

    /**
     * Gera o QR Code de alta densidade utilizando a biblioteca Endroid\QrCode
     * Retorna a imagem codificada em Base64 para incorporação direta no HTML
     */
    private function gerarQrCodeBase64($stringDados) {
        $qrCode = new QrCode($stringDados);
        $qrCode->setEncoding(new Encoding('UTF-8'));
        
        // Nível de correção Low (L) garante menor densidade de blocos para strings longas,
        // facilitando a leitura de alta velocidade pelo coletor no galpão.
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium);
        
        $qrCode->setSize(800);
        $qrCode->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return $result->getDataUri();
    }

    /**
     * Renderiza o rótulo unificado otimizado para expedição acelerada com divisão aritmética de dízima
     * * @param object $dadosGerais Contém: qrCompilacao (ID Master), siglaCentralizadora, nomeCentralizadora, siglaSe, nomeCentralizadoraOrigem, totalPaletes, pesoTotalAgrupamento
     * @param array $paletes      Lista de objetos vindos do banco de dados (numeroPalete, pesoPrevisto, qrMaster)
     */
    public function renderizar($dadosGerais, array $paletes) {
        // Divide estritamente no limite configurado por página
        
        $paginasDeCarga = array_chunk($paletes, $this->limitePaletes); 
        $totalPaginas = count($paginasDeCarga);

        // --- ENGENHARIA DE DISTRIBUIÇÃO DE PESO E TRATAMENTO DE DÍZIMA ---
        $pesoRealTotal = (float) $dadosGerais->pesoTotalAgrupamento;
        $totalPaletesDoGrupo = count($paletes);
        
        // Evita divisão por zero caso venha vazio por inconsistência
        $pesoMedioUnitario = $totalPaletesDoGrupo > 0 ? ($pesoRealTotal / $totalPaletesDoGrupo) : 0;
        
        $pesoAcumuladoGasto = 0.0;
        
        foreach ($paginasDeCarga as $index => $paletesDaPagina) {
            $paginaAtual = $index + 1;
            $qtdPaletesNaPagina = count($paletesDaPagina);
            
            // Se for a última página, absorve o resto/dízima matemática, senão faz a média proporcional
            if ($paginaAtual === $totalPaginas) {
                $pesoCalculadoDaPagina = $pesoRealTotal - $pesoAcumuladoGasto;
            } else {
                $pesoCalculadoDaPagina = round($pesoMedioUnitario * $qtdPaletesNaPagina, 2);
                $pesoAcumuladoGasto += $pesoCalculadoDaPagina;
            }
            

            // Formata o peso final da página com duas casas decimais padrão para a string
            $pesoPaginaFormatado = number_format($pesoCalculadoDaPagina, 2, '.', '');
            $pesoRealTotalFormatado = number_format($pesoRealTotal, 2, '.', '');
            $pesoLiquido = $pesoRealTotal - $this->pesoTara;
            $pesoLiquidoFormatado = number_format($pesoLiquido, 2, '.', '');
            // Modificação do sufixo do ID para diferenciar as sub-páginas (Opção 4)
            // Ex: Se o master é ID123, vira ID123_A, ID123_B, etc.
            //$sufixoPagina = chr(64 + $paginaAtual); // 1 = A, 2 = B, 3 = C...
            //$idSubLotePagina = 'ID'. sprintf("%010d", $dadosGerais->idAgrupamento) . '_' . $sufixoPagina;
            $idSubLotePagina = 'ID'. sprintf("%010d", $dadosGerais->idAgrupamento);

            // --- 1. MONTAGEM DA STRING ATÔMICA DO QR CODE ---
            // Posição 0: ID do Sub-lote | Posição 1: Peso Real Fracionado da Página
            //$stringCompletaMaster = $idSubLotePagina . '|' . sprintf("%011.3f", $pesoRealTotal) . '|' . sprintf("%011.3f", $pesoPaginaFormatado);
            $stringCompletaMaster = "";
            $pesagem = sprintf("%011.3f", $pesoRealTotal) . sprintf("%011.3f", $this->pesoTara) . sprintf("%011.3f", $pesoLiquido);
            
            date_default_timezone_set('America/Sao_Paulo');
            $codigoDataHora = date('dmYHis');
            //$stringCompletaMaster = $idSubLotePagina . '|' . $pesagem . $codigoDataHora;
            
            //$stringCompletaMaster .= '|'.$dadosGerais->qrCompilacao;            
            $stringCompletaMaster = $dadosGerais->qrCompilacao; //qr code com um palete            

            // Concatena o restante dos paletes (97 caracteres cada)
            foreach ($paletesDaPagina as $palete) {
                //$stringCompletaMaster .= '|' . $palete->qrMaster;
            }
            

            // --- 2. GERAÇÃO DO QR CODE ÚNICO ---
            $linkQrCodeUnificado = $this->gerarQrCodeBase64($stringCompletaMaster);

            // --- 3. SEPARAÇÃO SIMÉTRICA PARA AS DUAS TABELAS VISUAIS ---
            $pontoCorte = ceil($qtdPaletesNaPagina / 2);
            $tabelaEsquerda = array_slice($paletesDaPagina, 0, $pontoCorte);
            $tabelaDireita = array_slice($paletesDaPagina, $pontoCorte);
            
            // --- 4. MONTAGEM ESTRUTURAL DO HTML ---
            $html = $this->obterEstilosCSS();
            $html .= "
            <div class='header-container'>
                <table class='tabela-cabecalho'>
                    <tr>
                        <td colspan='4' class='titulo-cabecalho'>PALETES ENGLOBADOS - FNDE</td>
                    </tr>
                    <tr>
                        <td colspan='3' class='label'>Centralizadora de Destino:</td>
                        <td class='label'>SE Destino:</td>                       
                    </tr>
                    <tr>
                        <td colspan='3' class='saida-bold'>{$dadosGerais->siglaCentralizadora} - {$dadosGerais->nomeCentralizadora}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais->siglaSe}</td>
                    </tr>
                    <tr>
                        <td class='label' style='width: 35%;'>Origem:</td>
                        <td class='label' style='width: 15%;'>Qtde Paletes (Folha/Total):</td>
                        <td class='label' style='width: 35%;'>Peso Kg (Folha/Total):</td>
                        <td class='label' style='width: 15%;'>Página:</td>
                    </tr>
                    <tr>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais->nomeCentralizadoraOrigem}</td>
                        <td class='saida-bold' style='text-align: center;'>{$qtdPaletesNaPagina} / {$dadosGerais->totalPaletes}</td>
                        <td class='saida-bold' style='text-align: center; color: #000;'>".number_format($pesoPaginaFormatado, 2, ',', '.')." / ". number_format($pesoRealTotalFormatado, 2, ',', '.') ." kg</td>
                        <td class='saida-bold' style='text-align: center;'>{$paginaAtual} de {$totalPaginas}</td>
                    </tr>
                </table>
            </div>

            <div class='centro-container'>                
                <div class='wrapper-qr'>
                    <img src='{$linkQrCodeUnificado}' class='qr-unificado-img'>
                </div>
            </div>

            <div class='footer-container'>
            <div class='instrucao-leitura'>Relação de paletes englobados neste rótulo:</div>
                <table style='width: 100%; border: none; border-collapse: collapse; table-layout: fixed;'>
                    <tr>
                        <td style='width: 4%; border: none;'></td>
                        
                        <td style='width: 44%; border: none; vertical-align: top;'>
                            <table class='tabela-paletes'>
                                <thead>
                                    <tr>
                                        <th style='width: 65%;'>Número do Palete</th>
                                        <th style='width: 35%;'>Peso (kg)</th>
                                    </tr>
                                </thead>
                                <tbody>";
                                foreach ($tabelaEsquerda as $p) {
                                    $html .= "<tr>
                                        <td>".substr($p, 0, 11)."</td>
                                        <td style='text-align: center;'>".number_format(substr($p, 11, 11), 2, ',', '.')."</td>
                                    </tr>";
                                }
                                $html .= "</tbody>
                            </table>
                        </td>
                        
                        <td style='width: 4%; border: none;'></td>
                        
                        <td style='width: 44%; border: none; vertical-align: top;'>
                            <table class='tabela-paletes'>
                                <thead>
                                    <tr>
                                        <th style='width: 65%;'>Número do Palete</th>
                                        <th style='width: 35%;'>Peso (kg)</th>
                                    </tr>
                                </thead>
                                <tbody>";
                                foreach ($tabelaDireita as $p) {
                                    $html .= "<tr>
                                        <td>".substr($p, 0, 11)."</td>
                                        <td style='text-align: center;'>".number_format(substr($p, 11, 11), 2, ',', '.')."</td>
                                    </tr>";
                                }
                                // Linha de compensação visual se o número de itens na página for ímpar
                                if (count($tabelaDireita) < count($tabelaEsquerda)) {
                                    $html .= "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
                                }
                                $html .= "</tbody>
                            </table>
                        </td>
                        
                        <td style='width: 4%; border: none;'></td>
                    </tr>
                </table>
            </div>";
            
            // Envia o HTML processado da folha atual para a engine do mPDF
            $this->mpdf->WriteHTML($html);
            
            // Adiciona nova folha se ainda houver blocos de paletes pendentes
            if ($paginaAtual < $totalPaginas) {
                $this->mpdf->AddPage();
            }
        }

        // Envia o PDF compilado direto para o navegador do operador
        $this->mpdf->Output('Rótulo Englogado ID'.$dadosGerais->idAgrupamento.' '.$dadosGerais->siglaSe.' '.$dadosGerais->nomeCentralizadora.'.pdf', 'I');
    }

    /**
     * Engine CSS responsável pelo travamento das margens e cantos arredondados da moldura
     */
    private function obterEstilosCSS() {
        return "
        <style>
            @page { margin: 10mm; }
            body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; }
            
            .header-container { width: 100%; margin-bottom: 5mm; }
            .tabela-cabecalho { width: 100%; border-collapse: collapse; table-layout: fixed; }
            .tabela-cabecalho td { border: 1px solid black; padding: 4px 6px; vertical-align: middle; }
            .tabela-cabecalho th { border: 1px solid red; padding: 4px 6px; vertical-align: top; }
            .titulo-cabecalho { background-color: #EFEFEF; font-weight: bold; text-align: center; font-size: 15pt; padding: 6px !important; text-transform: uppercase; }
            .label { font-size: 10pt; color: #333; font-weight: bold; }
            .saida-bold { font-size: 14pt; font-weight: bold; }

            .centro-container { 
                width: 100%; 
                text-align: center; 
                margin-top: 3mm;
                margin-bottom: 3mm; 
            }
            .instrucao-leitura { 
                font-size: 13pt; 
                font-weight: bold; 
                margin-bottom: 4mm; 
                padding-bottom: 2mm;
                text-align: center; 
                width: 100%;
            }
            
            .wrapper-qr {
                width: 120mm;
                height: 120mm;
                margin: 0 auto;
                padding: 1mm;
                background-color: #FFFFFF;
                display: block;
            }
            .qr-unificado-img { 
                padding-top: 3mm;
                width: 110mm; 
                height: 110mm; 
                display: flex;
                align-items: center;
                margin: 0 auto;
            }

            .footer-container { 
                width: 100%; 
                position: absolute;
                margin-bottom: 10mm;
                left: 0;
            }
            
            .tabela-paletes { 
                width: 100%; 
                border-collapse: collapse; 
                table-layout: fixed;
            }
            .tabela-paletes th { 
                background-color: #EFEFEF; 
                border: 1px solid #000; 
                padding: 5px; 
                font-weight: bold; 
                font-size: 12pt;
                text-align: center;
            }
            .tabela-paletes td { 
                border: 1px solid #000; 
                padding: 4px 6px; 
                font-weight: bold;
                font-size: 11pt;
                line-height: 1.1;
                text-align: center;
            }
        </style>";
    }
}

/*
.wrapper-qr {
                width: 120mm;
                height: 120mm;
                margin: 0 auto;
                border: 6px solid #000000;
                border-radius: 5mm;
                padding: 1mm;
                background-color: #FFFFFF;
                display: block;
            }
*/