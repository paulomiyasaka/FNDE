<?php
// Ativa tratamento de erros para ajudar no desenvolvimento local
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use FNDE\RelatorioAgrupamentoDompdf;

// 1. Criamos uma lista de 16 paletes para testar a quebra exata no 16º item
$paletes = [];
for ($i = 1; $i <= 16; $i++) {
    $palete = new stdClass();
    // Ex: PE321456789
    $palete->id_etiqueta = "PE" . rand(100000000, 999999999);
    // Peso formatado com vírgula conforme padrão nacional
    $palete->peso = number_format(rand(8, 45) + (rand(0, 99)/100), 2, ',', '');
    
    // String exata simulando a especificação de 97 caracteres industriais
    $palete->qr_97_chars = "FNDE_PALETE_DATA_" . str_pad($i, 5, "0", STR_PAD_LEFT) . "_" . str_repeat("X", 75); 
    
    $paletes[] = $palete;
}

// 2. Dados gerais consolidados do topo do agrupamento
$dadosGerais = [
    'destino'     => 'FNDE_BENFICA_AGRUPAR',
    'se'          => 'RJ',
    'sigla'       => 'BFC',
    'qtd_total'   => '16',
    'peso_total'  => '663,86',
    // String exata de 21 caracteres para o identificador master
    'qr_master'   => '00000663860016BFCRJXX' 
];

// 3. Executa a renderização
$relatorio = new RelatorioAgrupamentoDompdf();
$relatorio->renderizar($dadosGerais, $paletes);