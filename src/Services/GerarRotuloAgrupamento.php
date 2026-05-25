<?php
namespace FNDE\Services;

use Mpdf\Mpdf;
// Removemos os "use" da biblioteca Endroid QrCode e trazemos a classe do TCPDF
use TCPDF2DBarcode;

class GerarRotuloAgrupamento {
    private $mpdf;

    public function __construct() {
        // Mantém a configuração do mPDF em modo PAISAGEM (A4-L)
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10
        ]);
    }

    /**
     * Gera o código DataMatrix real e entrega em formato de imagem inline (Base64)
     * Parâmetros internos do getBarcodePngData: (largura do bloco, altura do bloco)
     */
    private function gerarDataMatrixBase64($dados) {
        // Instancia o gerador nativo do TCPDF para DataMatrix
        $barcodeObj = new TCPDF2DBarcode($dados, 'DATAMATRIX');
        
        // Cria a imagem PNG pura na memória
        $pngData = $barcodeObj->getBarcodePngData(4, 4);
        
        // Retorna a string pronta para ser lida pela tag <img src="...">
        return 'data:image/png;base64,' . base64_encode($pngData);
    }

    public function renderizar($dadosGerais, array $paletes) {
        $chunks = array_chunk($paletes, 15); // Limite de 15 por página
        $totalPaginas = count($chunks);

        foreach ($chunks as $index => $paginaPaletes) {
            $paginaAtual = $index + 1;
            
            // Gerar DataMatrix Master (Canto superior direito - 21 chars)
            $dmMasterLink = $this->gerarDataMatrixBase64($dadosGerais->qrCompilacao);

            $html = $this->getCSS();
            $html .= "
            <div class='header-container'>
                <!--<div class='titulo'>PALETES ENGLOBADOS</div>-->
                <table class='tabela-cabecalho'>
                    <tr>
                        <td colspan='4' class='label'>Destino:</td>
                        <td class='label' style='width: 15%;'>SE:</td>
                        <td rowspan='6' class='celula-qr-master'>
                            <img src='{$dmMasterLink}' class='qr-master-img'>
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
                        <td class='saida-bold' style='text-align: center;'>{$dadosGerais->pesoTotalAgrupamento}</td>
                        <td class='saida-bold' style='text-align: center;'>CLI CAJAMAR</td>
                        <td class='saida-bold' style='text-align: center;'><span class='saida'>{$paginaAtual} de {$totalPaginas}</span></td>
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
                // Gera o DataMatrix individual com 97 caracteres
                $dmPaleteImg = $this->gerarDataMatrixBase64($palete->qrMaster);
                
                $html .= "
                <td class='celula-palete'>
                    <div class='moldura-palete'>
                        <div class='palete-id'>{$palete->numeroPalete}</div>
                        <div class='container-qr'>
                            <img src='{$dmPaleteImg}' class='qr-palete'>
                        </div>
                        <div class='palete-peso'>Peso (kg): <span class='saida-bold'>{$palete->pesoPrevisto}</span></div>
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

        $this->mpdf->Output('Relatorio_Agrupamento_DataMatrix.pdf', 'I');
    }

    private function getCSS() {
        return "
        <style>
            .header-container { width: 100%; margin-bottom: 4mm; }
            .titulo { font-family: 'Times New Roman'; font-size: 36pt; font-weight: bold; text-align: center; width: 100%; margin-bottom: 2mm; }
            
            .tabela-cabecalho { width: 100%; border-collapse: collapse; table-layout: fixed; }
            .tabela-cabecalho td { border: 1px solid black; padding: 4px; vertical-align: top; }
            
            .label { font-family: 'Times New Roman'; font-size: 20pt; font-weight: bold; }
            .saida { font-family: 'Arial'; font-size: 14pt; font-weight: normal; }
            .saida-bold { font-family: 'Arial'; font-size: 14pt; font-weight: bold; }

            /* Dimensões estritas do DataMatrix Master no topo direito (30x30mm) */
            .celula-qr-master { width: 35mm; text-align: center; vertical-align: middle !important; padding: 2px !important; }
            .qr-master-img { width: 30mm; height: 30mm; display: block; margin: 0 auto; }

            .tabela-grid-paletes { width: 100%; border-collapse: separate; border-spacing: 2mm; table-layout: fixed; }
            .celula-palete { width: 20%; border: 1px solid; text-align: center; vertical-align: top; padding: 0; }

            /* Moldura Estrita com dados internos alinhados */
            .moldura-palete { 
                width: 53mm; 
                height: 68mm; 
                text-align: center;
                margin: 0 auto;
                padding: 2mm 0;
                box-sizing: border-box;
                background-color: #FFF;
            }
            
            .palete-id { font-family: 'Arial'; font-size: 14pt; font-weight: bold; margin-bottom: 1mm; width: 100%;}
            
            /* Força os DataMatrix dos paletes a terem 52mm de largura por 50mm de altura */
            .container-qr { width: 52mm; height: 50mm; margin: 0 auto; overflow: hidden; padding: 5mm}
            .qr-palete { width: 30mm; height: 30mm; display: block; margin: 0 auto;}
            
            .palete-peso { font-family: 'Arial'; font-size: 10pt; width: 100%; line-height: 1.2; }
        </style>";
    }
}