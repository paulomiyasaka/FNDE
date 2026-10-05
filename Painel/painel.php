<?php

$dirRelatorios = __DIR__ . '/relatorios';$mensagem = null;

// Lógica para Exclusão de Arquivos de Relatórios Antigos
if (isset($_GET['acao']) &&$_GET['acao'] === 'excluir' && !empty($_GET['arquivo'])) {$arquivoExcluir = basename($_GET['arquivo']);$caminhoArquivo = $dirRelatorios . '/' .$arquivoExcluir;

    if (file_exists($caminhoArquivo) && str_ends_with($arquivoExcluir, '.html')) {
        unlink($caminhoArquivo);
        $mensagem = ['tipo' => 'success', 'texto' => "Relatório '{$arquivoExcluir}' removido com sucesso."];
    } else {
        $mensagem = ['tipo' => 'danger', 'texto' => "Arquivo não encontrado ou inválido."];
    }
}

// Mensagem vinda do upload
if (isset($_GET['status']) &&$_GET['status'] === 'sucesso' && !empty($_GET['arquivo'])) {$nomeArq = htmlspecialchars($_GET['arquivo']);$mensagem = ['tipo' => 'success', 'texto' => "Novo relatório '{$nomeArq}' gerado e salvo com sucesso!"];
}

// Lista os arquivos .html dentro do diretório /relatorios
$relatorios = [];
if (is_dir($dirRelatorios)) {
    $arquivos = glob($dirRelatorios . '/*.html');
    
    // Ordena do mais recente para o mais antigo
    usort($arquivos, function($a,$b) {
        return filemtime($b) - filemtime($a);
    });

    date_default_timezone_set('America/Sao_Paulo');
    foreach ($arquivos as $filepath) {$relatorios[] = [
            'nome' => basename($filepath),
            'tamanho' => round(filesize($filepath) / 1024, 2) . ' KB',
            'data_criacao' => date('d/m/Y H:i:s', filemtime($filepath)),
            'link_acesso' => 'relatorios/' . basename($filepath)
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Versões dos Relatórios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1"><i class="bi bi-folder-fill text-warning me-2"></i> Painel de Relatórios Gerados</h3>
                        <p class="text-muted mb-0">Gerencie e acesse as versões salvas no servidor.</p>
                    </div>
                    <a href="gerar.php" class="btn btn-success fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Gerar Novo Relatório
                    </a>
                </div>

                <?php if ($mensagem): ?>
                    <div class="alert alert-<?= $mensagem['tipo'] ?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> <?= $mensagem['texto'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-3">Nome do Relatório</th>
                                        <th>Data de Geração</th>
                                        <th>Tamanho</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($relatorios)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                                Nenhum relatório salvo no servidor ainda.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($relatorios as$rel): ?>
                                            <tr>
                                                <td class="ps-3 fw-bold text-primary">
                                                    <i class="bi bi-file-earmark-code me-2 text-secondary"></i>
                                                    <?= htmlspecialchars($rel['nome']) ?>
                                                </td>
                                                <td><?= $rel['data_criacao'] ?></td>
                                                <td><span class="badge bg-light text-dark border"><?= $rel['tamanho'] ?></span></td>
                                                <td class="text-center">
                                                    <a href="<?= $rel['link_acesso'] ?>" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Abrir em Nova Aba">
                                                        <i class="bi bi-eye-fill"></i> Visualizar
                                                    </a>
                                                    <a href="<?= $rel['link_acesso'] ?>" download class="btn btn-sm btn-outline-secondary me-1" title="Baixar Arquivo HTML">
                                                        <i class="bi bi-download"></i> Baixar
                                                    </a>
                                                    <a href="painel.php?acao=excluir&arquivo=<?= urlencode($rel['nome']) ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Tem certeza que deseja excluir esta versão do relatório?');" 
                                                       title="Excluir Versão">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script corrigido para src= -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>