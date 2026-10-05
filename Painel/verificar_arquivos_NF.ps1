# 1. Configuração dos caminhos
$origem = Get-Location # Ou defina o caminho completo: "C:\Caminho\Para\Pasta"
$pastaDestino = Join-Path -Path $origem -ChildPath "nf_correcao"
$arquivoRelacao = Join-Path -Path $origem -ChildPath "arquivos_nf.txt"

# 2. Verifica se o arquivo de relação existe
if (-not (Test-Path -Path $arquivoRelacao)) {
    Write-Error "O arquivo 'arquivos_nf.txt' não foi encontrado na pasta de origem."
    exit
}

# 3. Cria a pasta de destino caso ela não exista
if (-not (Test-Path -Path $pastaDestino)) {
    New-Item -Path $pastaDestino -ItemType Directory | Out-Null
    Write-Host "Pasta 'nf_correcao' criada com sucesso." -ForegroundColor Green
}

# 4. Carrega a lista de arquivos permitidos em um HashSet (busca O(1) extremamente rápida)
# Remove espaços em branco e ignora linhas vazias
$permitidos = [System.Collections.Generic.HashSet[string]]::new([System.StringComparer]::OrdinalIgnoreCase)

Get-Content -Path $arquivoRelacao | ForEach-Object {
    $linha = $_.Trim()
    if ($linha -ne "") {
        # Adiciona a extensão .pdf caso a linha no TXT venha apenas com o nome
        if (-not $linha.EndsWith(".pdf", [System.StringComparison]::OrdinalIgnoreCase)) {
            $linha += ".pdf"
        }
        [void]$permitidos.Add($linha)
    }
}

Write-Host "Foram carregados $($permitidos.Count) nomes válidos do arquivo de relação." -ForegroundColor Cyan

# 5. Obtém todos os arquivos PDF da pasta origem (ignorando a própria pasta nf_correcao)
$arquivosNaPasta = Get-ChildItem -Path $origem -Filter "*.pdf" -File

$movidos = 0
$mantidos = 0

# 6. Processa a movimentação
foreach ($arquivo in $arquivosNaPasta) {
    if ($permitidos.Contains($arquivo.Name)) {
        $mantidos++
    } else {
        Move-Item -Path $arquivo.FullName -Destination $pastaDestino -Force
        $movidos++
    }
}

# 7. Resumo da execução
Write-Host "`n--- Processamento Concluído ---" -ForegroundColor Yellow
Write-Host "Arquivos mantidos na pasta: $mantidos" -ForegroundColor Green
Write-Host "Arquivos movidos para 'nf_correcao': $movidos" -ForegroundColor Red