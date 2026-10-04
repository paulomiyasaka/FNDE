<?php

// Requer o Autoload oficial existente na raiz (/vendor/autoload.php)
require_once __DIR__ . '/../vendor/autoload.php';

use FNDE\Painel\ExcelReader;
use FNDE\Painel\HtmlExporter;
use FNDE\Painel\ExcelToDatasetConverter;

$mensagemErro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    try {
        $file =$_FILES['excel_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erro ao realizar o upload da planilha (Código: {$file['error']}).");
        }

        // Lê a planilha com a nova classe sob o namespace FNDE\Painel
        $reader = new ExcelReader($file['tmp_name']);
        $payloadData =$reader->parse();

        // Gera o arquivo HTML autônomo com o JSON injetado
        $htmlContent = HtmlExporter::generateStandaloneHtml($payloadData);

        // Download do arquivo .html para entrega ao cliente
        $filename = "Relatorio_Faturamento_" . date('Ymd_His') . ".html";
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($htmlContent));
        
        echo $htmlContent;
        exit;

    } catch (Exception $e) {
        $mensagemErro =$e->getMessage();
    }


// 2. Importa a sua classe do arquivo em src/
require_once '../src/Painel/ExcelToDatasetConverter.php';



try {
    // Definir o caminho da planilha que você deseja ler
    $caminhoPlanilha = $file['tmp_name'];

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
    header('Location: painel_2.html?status=success');
    exit;

} catch (Exception $e) {
    // Exibe mensagem amigável em caso de erro
    http_response_code(500);
    echo "<h3>Erro ao processar a planilha:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='painel_2.html'>Voltar ao Painel</a>";
}


}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Relatório Offline - PNLD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h4 class="card-title fw-bold mb-3">
                            <i class="bi bi-file-earmark-excel text-success me-2"></i>
                            Gerador de Relatório Offline (XLSX para HTML)
                        </h4>
                        <p class="text-muted small">
                            Selecione a planilha Excel (`.xlsx`) atualizada. O sistema lerá dinamicamente as colunas e dados sem alterar o autoload do Composer.
                        </p>

                        <?php if ($mensagemErro): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div><?= htmlspecialchars($mensagemErro) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="gerar_json.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="excel_file" class="form-label fw-semibold">Selecione a planilha (.xlsx):</label>
                                <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-download me-2"></i> Processar Planilha e Baixar HTML para o Cliente
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>