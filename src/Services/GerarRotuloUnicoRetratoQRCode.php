<?php
namespace FNDE\Services;

use mpdf\mpdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

class GerarRotuloUnicoRetratoQRCode {
    private $mpdf;

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
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::LOW);
        
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
        $paginasDeCarga = array_chunk($paletes, 15); 
        $totalPaginas = count($paginasDeCarga);

        foreach ($paginasDeCarga as $index => $paletesDaPagina) {
            $paginaAtual = $index + 1;
            
            // 1. INICIALIZA A STRING COM OS DADOS GERAIS (21 caracteres)
            $stringCompletaMaster = $dadosGerais['qr_master'];

            // 2. LOOP DE CONCATENAÇÃO COM O DELIMITADOR "|" NA FRENTE DE CADA CÓDIGO DE 97 CARACTERES
            foreach ($paletesDaPagina as $palete) {
                $stringCompletaMaster .= '|' . $palete->qr_97_chars;
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
                        <td colspan='4' class='titulo-cabecalho'>Dados do Agrupamento - FNDE</td>
                    </tr>
                    <tr>
                        <td colspan='3' class='label'>Destino:</td>
                        <td class='label' style='width: 15%;'>SE:</td>
                    </tr>
                    <tr>
                        <td colspan='3' class='saida-bold'>{$dadosGerais['destino']}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais['se']}</td>
                    </tr>
                    <tr>
                        <td class='label' style='width: 25%;'>Sigla Centralizadora:</td>
                        <td class='label' style='width: 25%;'>Qtde de paletes:</td>
                        <td class='label' style='width: 25%;'>Peso Total - kg</td>
                        <td class='label' style='width: 25%;'>Página:</td>
                    </tr>
                    <tr>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais['sigla']}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais['qtd_total']}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais['peso_total']} kg</td>
                        <td class='saida-bold' style='text-align: center;'>{$paginaAtual} de {$totalPaginas}</td>
                    </tr>
                </table>
            </div>

            <div class='centro-container'>
                <div class='instrucao-leitura'>Faça a leitura do QR Code para a postagem e expedição.</div>
                <div class='wrapper-qr'>
                    <img src='{$linkQrCodeUnificado}' class='qr-unificado-img'>
                </div>
            </div>

            <div class='footer-container'>
                <div class='coluna-tabela-esquerda'>
                    <table class='tabela-paletes'>
                        <thead>
                            <tr>
                                <th>IDENTIFICADOR DO PALETE</th>
                                <th>PESO (kg)</th>
                            </tr>
                        </thead>
                        <tbody>";
                        foreach ($tabelaEsquerda as $p) {
                            $html .= "<tr>
                                <td>{$p->id_etiqueta}</td>
                                <td style='text-align: right;'>{$p->peso}</td>
                            </tr>";
                        }
                        $html .= "</tbody>
                    </table>
                </div>

                <div class='coluna-tabela-direita'>
                    <table class='tabela-paletes'>
                        <thead>
                            <tr>
                                <th>IDENTIFICADOR DO PALETE</th>
                                <th>PESO (kg)</th>
                            </tr>
                        </thead>
                        <tbody>";
                        foreach ($tabelaDireita as $p) {
                            $html .= "<tr>
                                <td>{$p->id_etiqueta}</td>
                                <td style='text-align: right;'>{$p->peso}</td>
                            </tr>";
                        }
                        // Mantém o alinhamento de bordas caso a última página tenha número ímpar de itens
                        if (count($tabelaDireita) < count($tabelaEsquerda)) {
                            $html .= "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        }
                        $html .= "</tbody>
                    </table>
                </div>
                <div style='clear: both;'></div>
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
    private function obterEstilosCSS() {
        return "
        <style>
            @page { margin: 10mm; }
            body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; }
            
            /* CSS do Cabeçalho */
            .header-container { width: 100%; margin-bottom: 4mm; }
            .tabela-cabecalho { width: 100%; border-collapse: collapse; table-layout: fixed; }
            .tabela-cabecalho td { border: 1px solid black; padding: 4px 6px; vertical-align: top; }
            .titulo-cabecalho { background-color: #EFEFEF; font-weight: bold; text-align: center; font-size: 13pt; padding: 6px !important; text-transform: uppercase; }
            .label { font-size: 8.5pt; color: #333; font-weight: bold; }
            .saida-bold { font-size: 11pt; font-weight: bold; }

            /* CSS da Área Central */
            .centro-container { 
                width: 100%; 
                text-align: center; 
                margin-top: 2mm;
                margin-bottom: 2mm; 
            }
            .instrucao-leitura { 
                font-size: 14pt; 
                font-weight: bold; 
                margin-bottom: 5mm; 
                text-align: center; 
                width: 100%;
            }
            
            /* Moldura Preta Sólida Espessa que envolve o Código */
            .wrapper-qr {
                width: 130mm;
                height: 130mm;
                margin: 0 auto;
                border: 4px solid #000000;
                padding: 4mm;
                background-color: #FFFFFF;
                display: block;
            }
            .qr-unificado-img { 
                width: 122mm; 
                height: 122mm; 
                display: block;
                margin: 0 auto;
            }

            /* CSS do Rodapé Travado nos 20% Inferiores */
            .footer-container { 
                width: 100%; 
                position: absolute;
                bottom: 0;
                left: 0;
            }
            .coluna-tabela-esquerda { width: 48.5%; float: left; }
            .coluna-tabela-direita { width: 48.5%; float: right; }
            
            .tabela-paletes { width: 100%; border-collapse: collapse; }
            .tabela-paletes th { 
                background-color: #EFEFEF; 
                border: 1px solid #000; 
                padding: 3px; 
                font-weight: bold; 
                font-size: 8pt;
                text-align: center;
            }
            .tabela-paletes td { 
                border: 1px solid #000; 
                padding: 2px 5px; 
                font-weight: bold;
                font-size: 8.5pt;
                line-height: 1.1;
            }
        </style>";
    }
}