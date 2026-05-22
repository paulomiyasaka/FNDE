<?php
require_once __DIR__ . '/vendor/autoload.php';

use FNDE\Services\GerarRotuloAgrupamento2;

// Objeto anônimo para simular o seu objeto Palete
$paletes = [];
for ($i = 1; $i <= 20; $i++) {
    $palete = new stdClass();
    $palete->id_etiqueta = "PE" . str_pad($i, 9, "0", STR_PAD_LEFT);
    $palete->peso = number_format(rand(10, 50) + (rand(0, 99)/100), 2, ',', '');
    $palete->qr_97_chars = str_repeat("A", 97); // Simulação dos 97 caracteres
    $paletes[] = $palete;
}

$dadosGerais = [
    'destino' => 'FNDE_BENFICA_AGRUPAR',
    'se' => 'RJ',
    'sigla' => 'BFC',
    'qtd_total' => '20',
    'peso_total' => '663,86',
    'qr_master' => '123456789010001BFCRJ' // Exemplo de 21 caracteres
];

$relatorio = new GerarRotuloAgrupamento2();
$relatorio->renderizar($dadosGerais, $paletes);