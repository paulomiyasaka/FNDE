<?php
namespace FNDE\Utils;

use Mpdf\Mpdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class GerarRotuloAgrupamento {
    private $mpdf;

    public function __construct() {
        // Configura o mPDF com as fontes padrão
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10
        ]);
    }

    private function gerarQrCode($dados, $width = 300) {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($dados)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size($width)
            ->margin(0)
            ->build();
        return $result->getDataUri();
    }

    public function renderizar($dadosGerais, array $paletes) {
        $chunks = array_chunk($paletes, 15); // Limite de 15 por página
        $totalPaginas = count($chunks);

        foreach ($chunks as $index => $paginaPaletes) {
            $paginaAtual = $index + 1;
            
            // Gerar QR Code Master (Canto superior direito - 21 chars)
            $qrMasterLink = $this->gerarQrCode($dadosGerais['qr_master']);

            $html = $this->getCSS();
            $html .= "
            <div class='header-container'>
                <div class='titulo'>PALETES ENGLOBADOS</div>
                <table class='tabela-cabecalho'>
                    <tr>
                        <td colspan='2' class='label'>Destino:</td>
                        <td class='label'>SE:</td>
                        <td rowspan='3' style='width: 35mm; text-align: center; vertical-align: middle;'>
                            <img src='{$qrMasterLink}' style='width: 30mm; height: 30mm;'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' class='saida-bold'>{$dadosGerais['destino']}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais['se']}</td>
                    </tr>
                    <tr>
                        <td class='label'>Sigla Centralizadora:</td>
                        <td class='label'>Qtde de paletes:</td>
                        <td class='label'>Peso Total - kg</td>
                    </tr>
                    <tr>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais['sigla']}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais['qtd_total']}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais['peso_total']}</td>
                        <td class='label'>Página: <span class='saida'>{$paginaAtual} de {$totalPaginas}</span></td>
                    </tr>
                </table>
            </div>

            <div class='grid-paletes'>";

            foreach ($paginaPaletes as $palete) {
                $qrPaleteImg = $this->gerarQrCode($palete->qr_97_chars);
                $html .= "
                <div class='moldura-palete'>
                    <div class='palete-id'>{$palete->id_etiqueta}</div>
                    <img src='{$qrPaleteImg}' class='qr-palete'>
                    <div class='palete-peso'>Peso (kg):<span class='saida-bold'>{$palete->peso}</span></div>
                </div>";
            }

            $html .= "</div>";

            $this->mpdf->WriteHTML($html);
            
            if ($paginaAtual < $totalPaginas) {
                $this->mpdf->AddPage();
            }
        }

        $this->mpdf->Output('Relatorio_Agrupamento.pdf', 'I');
    }

    private function getCSS() {
        return "
        <style>
            .header-container { width: 100%; margin-bottom: 5mm; }
            .titulo { font-family: 'Times New Roman'; font-size: 36pt; font-weight: bold; text-align: center; width: 100%; margin-bottom: 2mm; }
            
            .tabela-cabecalho { width: 100%; border-collapse: collapse; }
            .tabela-cabecalho td { border: 1px solid black; padding: 2px; }
            
            .label { font-family: 'Times New Roman'; font-size: 20pt; font-weight: bold; }
            .saida { font-family: 'Arial'; font-size: 14pt; font-weight: normal; }
            .saida-bold { font-family: 'Arial'; font-size: 14pt; font-weight: bold; }

            .grid-paletes { width: 100%; margin-top: 5mm; }
            .moldura-palete { 
                display: inline-block; 
                width: 55mm; 
                height: 70mm; 
                border: 1px solid #333; 
                margin: 1mm; 
                text-align: center;
                padding-top: 2mm;
            }
            .qr-palete { width: 52mm; height: 50mm; }
            .palete-id { font-family: 'Arial'; font-size: 14pt; font-weight: bold; margin-bottom: 1mm; }
            .palete-peso { font-family: 'Arial'; font-size: 12pt; margin-top: 1mm; }
        </style>";
    }
}