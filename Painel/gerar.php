<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FNDE\Painel\ExcelReader;
use FNDE\Painel\HtmlExporter;

$mensagemErro = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    try {
        $file = $_FILES['excel_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erro no upload da planilha (Código: {$file['error']}).");
        }

        // Validação da extensão
        $extensao = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extensao !== 'xlsx') {
            throw new Exception("Tipo de arquivo inválido. Por favor, envie uma planilha no formato .xlsx.");
        }

        // 1. Processa a planilha
        $reader = new ExcelReader($file['tmp_name']);
        $payloadData = $reader->parse();

        // 2. Gera o conteúdo HTML autônomo
        $htmlContent = HtmlExporter::generateStandaloneHtml($payloadData);

        // 3. Garante que o diretório /relatorios existe com permissão adequada
        $dirRelatorios = __DIR__ . '/relatorios';
        if (!is_dir($dirRelatorios)) {
            mkdir($dirRelatorios, 0755, true);
        }
        date_default_timezone_set('America/Sao_Paulo');
        // 4. Salva o arquivo fisicamente na pasta relatorios
        $filename = "Relatorio_Faturamento_" . date('Ymd_His') . ".html";
        $filepath = $dirRelatorios . '/' . $filename;

        if (file_put_contents($filepath, $htmlContent) === false) {
            throw new Exception("Falha ao salvar o arquivo no servidor em /relatorios.");
        }

        // Redireciona de volta para a tela do painel
        header("Location: painel.php?status=sucesso&arquivo=" . urlencode($filename));
        exit;

    } catch (Exception $e) {
        $mensagemErro = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Relatórios Offline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title fw-bold m-0">
                                <i class="bi bi-file-earmark-excel text-success me-2"></i>
                                Importar Planilha e Gerar Versão
                            </h4>
                            <a href="painel.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Voltar ao Painel
                            </a>
                        </div>
                        
                        <?php if ($mensagemErro): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div><?= htmlspecialchars($mensagemErro) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="gerar.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="excel_file" class="form-label fw-semibold">Selecione a planilha (.xlsx):</label>
                                <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-gear-fill me-2"></i> Processar e Salvar em /relatorios
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>