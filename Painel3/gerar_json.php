<?php

// 1. Carrega o Autoload do Composer (para o PhpSpreadsheet)
require_once '../vendor/autoload.php';

// 2. Importa a sua classe do arquivo em src/
require_once '../src/Painel/ExcelToDatasetConverter.php';

use FNDE\Painel\ExcelToDatasetConverter;

try {
    // Definir o caminho da planilha que você deseja ler
    $caminhoPlanilha = __DIR__ . '/planilha_faturamento.xlsx';

    // Instancia e executa o conversor
    $converter = new ExcelToDatasetConverter($caminhoPlanilha);
    $resultado = $converter->processar();

    // Salva o arquivo dados.json na raiz do projeto
    $caminhoJson = __DIR__ . '/dados.json';
    $jsonContent = json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($caminhoJson, $jsonContent) === false) {
        throw new Exception("Falha ao gravar o arquivo dados.json no disco.");
    }

    // Redireciona de volta para o painel com status de sucesso
    header('Location: painel.php?status=success');
    exit;

} catch (Exception $e) {
    // Exibe mensagem amigável em caso de erro
    http_response_code(500);
    echo "<h3>Erro ao processar a planilha:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='painel_2.html'>Voltar ao Painel</a>";
}