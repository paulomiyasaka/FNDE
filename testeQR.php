<?php
require_once __DIR__ . '/vendor/autoload.php';

// Ativa a exibição de todos os erros do PHP na tela
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mpdf = new \Mpdf\Mpdf();

// Força o mPDF a mostrar erros de renderização de barcode
$mpdf->showImageErrors = true; 

$html = "
<h3>Teste Direto de DataMatrix</h3>
<barcode code='123456789012345678901' type='DATAMATRIX' height='1.5' />
";

$mpdf->WriteHTML($html);
$mpdf->Output();