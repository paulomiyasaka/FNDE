<?php
require_once __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Mpdf\Mpdf;

// 1. Configuração do mPDF
$mpdf = new Mpdf(['format' => 'A4', 'margin_all' => 10]);

// 2. Simulação de dados do banco (Substitua pela sua query MySQL)
$paletes = range(1, 32); // Exemplo com 32 paletes (vai gerar 2 páginas)

$html = "
<style>
    .tabela-grade { width: 100%; border-collapse: collapse; }
    .celula-rotulo {
        width: 25%; /* 4 colunas */
        height: 6.5cm; /* Ajustado para caber 4 linhas no A4 */
        border: 1px dashed #CCC;
        text-align: center;
        padding: 5px;
        vertical-align: middle;
    }
    .qr-img { width: 4.5cm; height: 4.5cm; } /* QR Code de quase 5cm */
    .id-texto { font-family: sans-serif; font-weight: bold; font-size: 12pt; }
</style>
<table class='tabela-grade'><tr>";

foreach ($paletes as $index => $id) {
    // 3. Gerar QR Code com a versão 5.1
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data("FNDE-PALETE-" . $id)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(300)
        ->margin(0)
        ->build();

    $dataUri = $result->getDataUri();

    // 4. Adicionar ao HTML
    $html .= "
    <td class='celula-rotulo'>
        <div class='id-texto'>PALETE: {$id}</div>
        <img src='{$dataUri}' class='qr-img'>
        <div style='font-size: 8pt;'>OPERAÇÃO FNDE</div>
    </td>";

    // 5. Lógica de quebra de linha (4 colunas) e página (16 itens)
    $proximo = $index + 1;
    
    if ($proximo % 4 == 0 && $proximo % 16 != 0) {
        $html .= "</tr><tr>";
    }

    if ($proximo % 16 == 0 && $proximo < count($paletes)) {
        $html .= "</tr></table><pagebreak /><table class='tabela-grade'><tr>";
    }
}

$html .= "</tr></table>";

$mpdf->WriteHTML($html);
$mpdf->Output('Rotulos_FNDE.pdf', \Mpdf\Output\Destination::DOWNLOAD);