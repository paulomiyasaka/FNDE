<?php
namespace FNDE\Services;

use Mpdf\Mpdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;

class GerarRotuloUnicoRetratoQRCode {
    private $mpdf;
    private $limitePaletes;

    public function __construct() {
        // Inicializa o mPDF configurado estritamente para Retrato (Portrait)
        $this->mpdf = new mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10
        ]);
        $this->limitePaletes = 23;
    }

    /**
     * Gera o QR Code de alta densidade utilizando a biblioteca Endroid\QrCode
     * Retorna a imagem codificada em Base64 para incorporação direta no HTML
     */
    private function gerarQrCodeBase64($stringDados) {
        // Inicializa o builder do QR Code com a string unificada
        $qrCode = new QrCode($stringDados);
        $qrCode->setEncoding(new Encoding('UTF-8'));
        
        // Nível de correção Low (L) garante menor densidade de blocos para strings longas,
        // facilitando a leitura de alta velocidade pelo coletor no galpão.
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::Low);
        
        
        // Define o tamanho bruto da matriz (pixels)
        $qrCode->setSize(500);
        $qrCode->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return $result->getDataUri(); // Já retorna no formato 'data:image/png;base64,...'
    }

    /**
     * Renderiza o rótulo unificado otimizado para expedição acelerada
     * * @param array $dadosGerais Contém chaves: destino, se, sigla, qtd_total, peso_total, qr_master (21 chars)
     * @param array $paletes     Lista de objetos vindos do banco de dados (id_etiqueta, peso, qr_97_chars)
     */
    public function renderizar($dadosGerais, array $paletes) {
        // Divide estritamente de 15 em 15 paletes por página por segurança operacional
        $paginasDeCarga = array_chunk($paletes, $this->limitePaletes); 
        $totalPaginas = count($paginasDeCarga);

        foreach ($paginasDeCarga as $index => $paletesDaPagina) {
            $paginaAtual = $index + 1;
            
            // 1. INICIALIZA A STRING COM OS DADOS GERAIS (21 caracteres)
            $stringCompletaMaster = $dadosGerais->qrCompilacao;

            // 2. LOOP DE CONCATENAÇÃO COM O DELIMITADOR "|" NA FRENTE DE CADA CÓDIGO DE 97 CARACTERES
            foreach ($paletesDaPagina as $palete) {
                $stringCompletaMaster .= '|' . $palete->qrMaster;
            }

            // 3. GERAÇÃO DO CÓDIGO FONTE MÃE VIA ENDROID QR CODE
            $linkQrCodeUnificado = $this->gerarQrCodeBase64($stringCompletaMaster);

            // 4. SEPARAÇÃO BALANCEADA PARA AS DUAS TABELAS DO RODAPÉ (Máx 20% da folha)
            $pontoCorte = ceil(count($paletesDaPagina) / 2);
            $tabelaEsquerda = array_slice($paletesDaPagina, 0, $pontoCorte);
            $tabelaDireita = array_slice($paletesDaPagina, $pontoCorte);

            // 5. MONTAGEM ESTRUTURAL DO HTML
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
                        <td class='label' style='width: 40%;'>Origem:</td>
                        <td class='label' style='width: 20%;'>Qtde de paletes:</td>
                        <td class='label' style='width: 25%;'>Peso Total - kg</td>
                        <td class='label' style='width: 20%;'>Página:</td>
                    </tr>
                    <tr>
                        <td class='saida-bold' style='text-align: center;'>CLI CAJAMAR</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais->totalPaletes}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais->pesoTotalAgrupamento} kg</td>
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
            <div class='instrucao-leitura'>Relação de paletes englobados:</div>
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
                                        <td>{$p->numeroPalete}</td>
                                        <td>{$p->pesoPrevisto}</td>
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
                                        <td>{$p->numeroPalete}</td>
                                        <td>{$p->pesoPrevisto}</td>
                                    </tr>";
                                }
                                // Linha de compensação visual se o número for ímpar
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

            // Envia o HTML processado para a engine do mPDF
            $this->mpdf->WriteHTML($html);
            
            // Adiciona nova folha se ainda houver blocos de paletes pendentes
            if ($paginaAtual < $totalPaginas) {
                $this->mpdf->AddPage();
            }
        }

        // Output padrão forçando exibição no navegador (Inline)
        $this->mpdf->Output('Rotulo_Unico_Expedicao_QRCode.pdf', 'I');
    }

    /**
     * Engine CSS responsável pela compressão milimétrica e posicionamento absoluto do rodapé
     */
    /**
     * Engine CSS atualizada - Proteção total contra quebra e estouro de margem
     */
    private function obterEstilosCSS() {
        return "
        <style>
            @page { margin: 10mm; }
            body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; }
            
            /* CSS do Cabeçalho - Dados do Agrupamento FNDE */
            .header-container { width: 100%; margin-bottom: 5mm; }
            .tabela-cabecalho { width: 100%; border-collapse: collapse; table-layout: fixed; }
            .tabela-cabecalho td { border: 1px solid black; padding: 4px 6px; vertical-align: top; }
            .titulo-cabecalho { background-color: #EFEFEF; font-weight: bold; text-align: center; font-size: 15pt; padding: 6px !important; text-transform: uppercase; }
            .label { font-size: 10pt; color: #333; font-weight: bold; }
            .saida-bold { font-size: 14pt; font-weight: bold; }

            /* CSS da Área Central */
            .centro-container { 
                width: 100%; 
                text-align: center; 
                margin-top: 3mm;
                margin-bottom: 3mm; 
            }
            .instrucao-leitura { 
                font-size: 14pt; 
                font-weight: bold; 
                margin-bottom: 6mm; 
                padding-bottom: 3mm;
                text-align: center; 
                width: 100%;
            }
            
            .wrapper-qr {
                width: 130mm;
                height: 130mm;
                margin: 0 auto;
                border: 8px solid #000000;
                border-radius: 20mm;
                padding: 5mm;
                background-color: #FFFFFF;
                display: block;
            }
            .qr-unificado-img { 
                width: 125mm; 
                height: 125mm; 
                display: block;
                margin: 0 auto;
            }

            /* --- AJUSTE DEFINITIVO PARA O RODAPÉ --- */
            .footer-container { 
                width: 100%; 
                position: absolute;
                margin-bottom: 10mm;
                left: 0;
            }
            
            /* Tabela interna de dados dos paletes */
            .tabela-paletes { 
                width: 100%; 
                border-collapse: collapse; 
                table-layout: fixed; /* Não deixa a tabela esticar além do seu container de célula */
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
                font-size: 12pt;
                line-height: 1.1;
                text-align: center;
            }
        </style>";
    }
}