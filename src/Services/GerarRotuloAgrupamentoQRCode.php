<?php
namespace FNDE\Services;

use Mpdf\Mpdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class GerarRotuloAgrupamentoQRCode {
    private $mpdf;

    public function __construct() {
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
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
        $chunks = array_chunk($paletes, 15); 
        $totalPaginas = count($chunks);

        foreach ($chunks as $index => $paginaPaletes) {
            $paginaAtual = $index + 1;
            
            // Gerar QR Code Master (Canto superior direito - 21 chars)
            $qrMasterLink = $this->gerarQrCode($dadosGerais->qrCompilacao, 200);

            $html = $this->getCSS();
            $html .= "
            <div class='header-container'>
                
                <table class='tabela-cabecalho'>
                    <tr>
                        <td colspan='4' class='label'>Destino:</td>
                        <td class='label' style='width: 15%;'>SE:</td>
                        <td rowspan='6' class='celula-qr-master'>
                            <img src='{$qrMasterLink}' class='qr-master-img'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='4' class='saida-bold'>{$dadosGerais->nomeCentralizadora}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais->siglaSe}</td>
                    </tr>
                    <tr>
                        <td class='label' style='width: 25%;'>Sigla Centralizadora:</td>
                        <td class='label' style='width: 20%;'>Qtde de paletes:</td>
                        <td class='label' style='width: 20%;'>Peso Total - kg</td>
                        <td class='label' style='width: 20%;'>Local de Postagem:</td>
                        <td class='label' style='width: 15%;'>Página:</td>
                    </tr>
                    <tr>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais->siglaCentralizadora}</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais->totalPaletes}</td>
                        <td class='saida-bold' style='text-align: center;'>".number_format($dadosGerais->pesoTotalAgrupamento, 2, ',', '.')."</td>
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais->nomeCentralizadoraOrigem}</td>
                        <td class='saida-bold' style='text-align: center;'>{$paginaAtual} de {$totalPaginas}</td>
                    </tr>
                </table>
            </div>

            <table class='tabela-grid-paletes'>";
            
            $colunas = 5;
            $totalItensPagina = count($paginaPaletes);
            
            for ($i = 0; $i < $totalItensPagina; $i++) {
                if ($i % $colunas == 0) {
                    $html .= "<tr>";
                }
                
                $palete = $paginaPaletes[$i];
                $qrPaleteImg = $this->gerarQrCode($palete->qrMaster, 250);
                
                $html .= "
                <td class='celula-palete'>
                    <div class='moldura-palete'>
                        <div class='palete-id'>{$palete->numeroPalete}</div>
                        <div class='container-qr'>                            
                            <img src='{$qrPaleteImg}' class='qr-palete'>                            
                        </div>
                        <div class='palete-peso'>Peso (kg): <span class='saida-bold'>".number_format($palete->pesoPrevisto, 2, ',', '.')."</span></div>
                    </div>
                </td>";
                
                if (($i + 1) % $colunas == 0 || ($i + 1) == $totalItensPagina) {
                    if (($i + 1) == $totalItensPagina && ($i + 1) % $colunas != 0) {
                        $resto = $colunas - (($i + 1) % $colunas);
                        for ($j = 0; $j < $resto; $j++) {
                            $html .= "<td class='celula-palete' style='border: none;'></td>";
                        }
                    }
                    $html .= "</tr>";
                }
            }

            $html .= "</table>";

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
            .header-container { width: 100%; margin-bottom: 4mm; }
            .titulo { font-family: 'Times New Roman'; font-size: 36pt; font-weight: bold; text-align: center; width: 100%; margin-bottom: 2mm; }
            
            .tabela-cabecalho { width: 100%; border-collapse: collapse; table-layout: fixed; }
            .tabela-cabecalho td { border: 1px solid black; padding: 4px; vertical-align: top; }
            
            .label { font-family: 'Times New Roman'; font-size: 14pt; font-weight: bold; }
            .saida { font-family: 'Arial'; font-size: 20pt; font-weight: normal; }
            .saida-bold { font-family: 'Arial'; font-size: 14pt; font-weight: bold; }

            /* Fixação do QR Code Master no Canto Direito */
            .celula-qr-master { width: 35mm; text-align: center; vertical-align: middle !important; padding: 2px !important; }
            .qr-master-img { width: 30mm; height: 30mm; display: block; margin: 0 auto; }

            /* Estrutura da Grade de Paletes */
            .tabela-grid-paletes { width: 100%; border-collapse: separate; border-spacing: 2mm; table-layout: fixed; }
            .celula-palete { width: 20%; border: 1px solid; text-align: center; vertical-align: top; padding: 0; }
            
            /* A MOLDURA: Todo o conteúdo fica rigorosamente preso aqui dentro */
            .moldura-palete { 
                width: 53mm; 
                height: 68mm; 
                text-align: center;
                margin: 0 auto;
                padding: 2mm 0;
                box-sizing: border-box; /* Garante que padding não aumente o tamanho da caixa */
                background-color: #FFF;
            }
            
            .palete-id { font-family: 'Arial'; font-size: 14pt; font-weight: bold; width: 100%; }
            
            .container-qr { width: 52mm; height: 50mm; margin: 0 auto; overflow: hidden; }
            .qr-palete { width: 30mm; height: 30mm; display: block; margin: 0 auto; }
            
            .palete-peso { font-family: 'Arial'; font-size: 10pt; width: 100%; line-height: 1.2; }
        </style>";
    }
}