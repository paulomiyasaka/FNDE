<?php
namespace FNDE\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class GerarRotuloDOMPDF {

    /**
     * Gera o código DataMatrix em formato de imagem inline (Base64)
     * utilizando o motor do TCPDF (altamente estável no PHP 8.1).
     */
    private function gerarDataMatrixBase64($texto) {
        // Instancia o gerador bidimensional isolado do TCPDF
        $barcodeObj = new \TCPDF2DBarcode($texto, 'DATAMATRIX');
        
        // Gera os dados puros do PNG (parâmetros: largura do módulo, altura do módulo)
        $pngData = $barcodeObj->getBarcodePngData(4, 4);
        
        return 'data:image/png;base64,' . base64_encode($pngData);
    }

    /**
     * Renderiza o documento completo em formato Paisagem com até 15 DataMatrix por página.
     */
    public function renderizar($dadosGerais, array $paletes) {
        // Configurações do Dompdf para habilitar parse correto de HTML5
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Útil caso use elementos externos futuros
        
        $dompdf = new Dompdf($options);

        // Divide o array de paletes em páginas de no máximo 15 itens
        $chunks = array_chunk($paletes, 15);
        $totalPaginas = count($chunks);
        
        $html = "<html><head>" . $this->getCSS() . "</head><body>";

        foreach ($chunks as $index => $paginaPaletes) {
            $paginaAtual = $index + 1;
            
            // Gera o DataMatrix Master de 21 caracteres
            $imgMasterBase64 = $this->gerarDataMatrixBase64($dadosGerais['qr_master']);

            $html .= "
            <div class='pagina'>
                <div class='titulo'>PALETES ENGLOBADOS</div>
                
                <table class='tabela-cabecalho'>
                    <tr>
                        <td colspan='2' class='label' style='width: 50%;'>Destino:</td>
                        <td class='label' style='width: 20%;'>SE:</td>
                        <td rowspan='3' class='celula-master'>
                            <img src='{$imgMasterBase64}' class='barcode-master'>
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
                        <td class='label' style='vertical-align: middle;'>Página: <span class='saida'>{$paginaAtual} de {$totalPaginas}</span></td>
                    </tr>
                </table>

                <div class='grid-paletes'>";

            foreach ($paginaPaletes as $palete) {
                // Gera o DataMatrix individual de 97 caracteres
                $imgPaleteBase64 = $this->gerarDataMatrixBase64($palete->qr_97_chars);
                
                $html .= "
                <div class='moldura-palete'>
                    <div class='palete-id'>{$palete->id_etiqueta}</div>
                    <div class='container-barcode'>
                        <img src='{$imgPaleteBase64}' class='barcode-palete'>
                    </div>
                    <div class='palete-peso'>Peso (kg): <span class='saida-bold'>{$palete->peso}</span></div>
                </div>";
            }

            $html .= "</div></div>"; // Fecha .grid-paletes e .pagina

            // Se houver uma próxima página, injeta a quebra estrutural do CSS
            if ($paginaAtual < $totalPaginas) {
                $html .= "<div class='quebra-pagina'></div>";
            }
        }

        $html .= "</body></html>";

        // Carrega o HTML processado, define papel A4 em modo Paisagem (Landscape)
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Força a exibição direta no navegador (I = Inline)
        $dompdf->stream('Relatorio_Agrupamento_FNDE.pdf', ['Attachment' => false]);
    }

    /**
     * Retorna as folhas de estilo estritas com conversão de mm para px (96 DPI)
     */
    private function getCSS() {
        return "
        <style>
            @page { margin: 30px 38px; }
            body { margin: 0; padding: 0; background-color: #fff; }
            
            .pagina { width: 100%; height: 100%; page-break-inside: avoid; }
            .quebra-pagina { page-break-after: always; clear: both; }
            
            /* Fontes e Tipografia conforme especificação */
            .titulo { 
                font-family: 'Times New Roman', Times, serif; 
                font-size: 36pt; 
                font-weight: bold; 
                text-align: center; 
                margin-bottom: 8px; 
            }
            
            .tabela-cabecalho { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
            .tabela-cabecalho td { border: 1px solid #000; padding: 4px; vertical-align: top; }
            
            .label { font-family: 'Times New Roman', Times, serif; font-size: 20pt; font-weight: bold; }
            .saida { font-family: Arial, Helvetica, sans-serif; font-size: 14pt; font-weight: normal; }
            .saida-bold { font-family: Arial, Helvetica, sans-serif; font-size: 14pt; font-weight: bold; }

            /* Espaço fixo para o DataMatrix Master (30mm x 3.78 = ~114px) */
            .celula-master { width: 120px; text-align: center; vertical-align: middle !important; padding: 2px; }
            .barcode-master { width: 114px; height: 114px; display: block; margin: 0 auto; }

            /* Grid de Rótulos utilizando a flutuação nativa que o Dompdf gerencia perfeitamente */
            .grid-paletes { width: 100%; clear: both; }
            
            /* Moldura do Palete: Largura aproximada para acomodar os 52mm internos + margens */
            .moldura-palete { 
                float: left;
                width: 204px;   /* ~54mm total */
                height: 260px;  /* ~69mm total */
                border: 1px solid #000; 
                margin: 5px;
                text-align: center;
                box-sizing: border-box;
                padding-top: 4px;
            }
            
            .palete-id { font-family: Arial, sans-serif; font-size: 14pt; font-weight: bold; margin-bottom: 4px; }
            
            /* Container do Barcode Palete (52mm largura x 50mm altura -> 197px x 189px) */
            .container-barcode {
                width: 197px;
                height: 189px;
                margin: 0 auto;
                overflow: hidden;
            }
            .barcode-palete {
                width: 197px;
                height: 189px;
            }
            
            .palete-peso { font-family: Arial, sans-serif; font-size: 11pt; margin-top: 4px; }
        </style>";
    }
}