<?php
namespace FNDE\Services;

use Mpdf\Mpdf;

class GerarRotuloAgrupamento {
    private $mpdf;

    public function __construct() {
        // Configura o mPDF em modo PAISAGEM (A4-L) com margens otimizadas
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', 
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10
        ]);
    }

    public function renderizar($dadosGerais, array $paletes) {
        // Divide os paletes em grupos de no máximo 15 por página
        $chunks = array_chunk($paletes, 15); 
        $totalPaginas = count($chunks);

        foreach ($chunks as $index => $paginaPaletes) {
            $paginaAtual = $index + 1;
            
            $html = $this->getCSS();
            $html .= "
            <div class='header-container'>
                <div class='titulo'>PALETES ENGLOBADOS</div>
                <table class='tabela-cabecalho'>
                    <tr>
                        <td colspan='2' class='label'>Destino:</td>
                        <td class='label'>SE:</td>
                        <td rowspan='3' style='width: 35mm; text-align: center; vertical-align: middle;'>
                            <barcode code='{$dadosGerais['qr_master']}' type='DATAMATRIX' size='1.2' error='M' disableborder='1' />
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

            <table class='tabela-grid-paletes'>";
            
            $colunas = 5;
            $totalItensPagina = count($paginaPaletes);
            
            for ($i = 0; $i < $totalItensPagina; $i++) {
                // Se for o primeiro item da linha, abre o <tr>
                if ($i % $colunas == 0) {
                    $html .= "<tr>";
                }
                
                $palete = $paginaPaletes[$i];
                
                $html .= "
                <td class='celula-palete'>
                    <div class='moldura-palete'>
                        <div class='palete-id'>{$palete->id_etiqueta}</div>
                        <div class='container-barcode'>
                            <barcode code='{$palete->qr_97_chars}' type='DATAMATRIX' size='1.8' error='M' disableborder='1' />
                        </div>
                        <div class='palete-peso'>Peso (kg): <span class='saida-bold'>{$palete->peso}</span></div>
                    </div>
                </td>";
                
                // Se for o último item da linha ou o último item da página, fecha o <tr>
                if (($i + 1) % $colunas == 0 || ($i + 1) == $totalItensPagina) {
                    // Preenche o restante da linha com células vazias se a última linha não estiver cheia
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

        // Envia o PDF para o navegador
        $this->mpdf->Output('Relatorio_Agrupamento_DataMatrix.pdf', 'I');
    }

    private function getCSS() {
        return "
        <style>
            .header-container { width: 100%; margin-bottom: 5mm; }
            .titulo { font-family: 'Times New Roman'; font-size: 36pt; font-weight: bold; text-align: center; width: 100%; margin-bottom: 2mm; }
            
            .tabela-cabecalho { width: 100%; border-collapse: collapse; }
            .tabela-cabecalho td { border: 1px solid black; padding: 4px; }
            
            .label { font-family: 'Times New Roman'; font-size: 20pt; font-weight: bold; }
            .saida { font-family: 'Arial'; font-size: 14pt; font-weight: normal; }
            .saida-bold { font-family: 'Arial'; font-size: 14pt; font-weight: bold; }

            /* Estutura em tabela para garantir o alinhamento horizontal perfeito */
            .tabela-grid-paletes { width: 100%; border-collapse: separate; border-spacing: 2mm; }
            .celula-palete { width: 20%; text-align: center; vertical-align: top; padding: 0; }
            
            .moldura-palete { 
                width: 53mm; 
                height: 68mm; 
                border: 1px solid #000; 
                text-align: center;
                margin: 0 auto;
                padding-top: 1mm;
            }
            .container-barcode {
                width: 52mm;
                height: 50mm;
                text-align: center;
                vertical-align: middle;
                margin-top: 1mm;
            }
            .palete-id { font-family: 'Arial'; font-size: 14pt; font-weight: bold; margin-bottom: 1mm; }
            .palete-peso { font-family: 'Arial'; font-size: 11pt; margin-top: 1mm; }
        </style>";
    }
}