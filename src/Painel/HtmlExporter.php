<?php

namespace FNDE\Painel;

class HtmlExporter
{
    /**
     * Empacota a estrutura HTML aprovada com os dados embutidos via JSON
     */
    public static function generateStandaloneHtml(array $payloadData): string
    {
        $jsonString = json_encode($payloadData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Faturamento e Recebimento</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                Gestão de Faturamento - PNLD / Editoras
            </span>
            <span class="badge bg-light text-primary fs-7">Versão Offline / Cliente</span>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <!-- Cards de Resumo Executivo (Linha 1) -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-primary">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Total Faturado</h6>
                        <h3 class="fw-bold text-primary mb-0" id="cardTotalFaturamento">R$ 0,00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-success">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Total de Exemplares (Tiragem)</h6>
                        <h3 class="fw-bold text-success mb-0" id="cardTotalTiragem">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Total de Títulos Recebidos</h6>
                        <h3 class="fw-bold text-info mb-0" id="cardTotalRegistros">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-danger">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Total de Títulos Não Recebidos</h6>
                        <h3 class="fw-bold text-danger mb-0" id="cardTotalNaoRecebido">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Resumo Executivo (Linha 2) -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-primary">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Total de Posições Porta Palete</h6>
                        <h3 class="fw-bold text-primary mb-0" id="cardTotalPaletes">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-success">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Média de dias (armazenamento)</h6>
                        <h3 class="fw-bold text-success mb-0" id="cardTotalDias">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Valor armazenagem palete/dia</h6>
                        <h3 class="fw-bold text-info mb-0" id="cardValorDiaria">R$ 2,55</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-danger">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Faturado até</h6>
                        <h3 class="fw-bold text-danger mb-0" id="cardFaturadoAte">--/--/----</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controles de Filtro -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar por Fornecedor, SKU, Título do Livro ou Chave...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="filterFornecedor" class="form-select">
                            <option value="">Todos os Fornecedores</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="badge bg-secondary p-2" id="recordCounter">Mostrando 0 registros</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela Dinâmica -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-dark" id="tableHead"></thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payload de Dados Injetado -->
    <script>
        const DATASET = {$jsonString};

        function formatCurrency(val) {
            return (val || 0).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
        }

        function formatNumber(val, decimals = 0) {
            return (val || 0).toLocaleString("pt-BR", { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        }

        function populateSuppliers(dados) {
            const select = document.getElementById("filterFornecedor");
            select.innerHTML = '<option value="">Todos os Fornecedores</option>';
            const suppliers = [...new Set(dados.map(i => i.fornecedor || i.fornecedor_cnpj))].filter(Boolean).sort();
            suppliers.forEach(s => {
                const opt = document.createElement("option");
                opt.value = s;
                opt.textContent = s;
                select.appendChild(opt);
            });
        }

        function updateCards(filtered) {
            const totalFat = filtered.reduce((acc, c) => acc + (c.valor_faturado || c.valorfaturamento || c.subtotal || 0), 0);
            const totalTir = filtered.reduce((acc, c) => acc + (c.tiragem || 0), 0);
            const totalPal = filtered.reduce((acc, c) => acc + (c.posicoes_porta_palete || c.qtdpaletes || 0), 0);
            
            const recebidos = filtered.filter(c => c.data_rec || c.datarecebimento).length;
            const naoRecebidos = filtered.length - recebidos;

            const totalDias = filtered.reduce((acc, c) => acc + (c.dias_armazenados || c.diasarmazenamento || 0), 0);
            const mediaDias = filtered.length > 0 ? (totalDias / filtered.length) : 0;

            document.getElementById("cardTotalFaturamento").textContent = formatCurrency(totalFat);
            document.getElementById("cardTotalTiragem").textContent = formatNumber(totalTir);
            document.getElementById("cardTotalRegistros").textContent = formatNumber(recebidos);
            document.getElementById("cardTotalNaoRecebido").textContent = formatNumber(naoRecebidos);
            document.getElementById("cardTotalPaletes").textContent = formatNumber(totalPal);
            document.getElementById("cardTotalDias").textContent = `\${formatNumber(mediaDias, 1)} dias`;
            
            if (DATASET.metadados) {
                document.getElementById("cardFaturadoAte").textContent = DATASET.metadados.dataGeracao.split(' ')[0];
            }
            document.getElementById("recordCounter").textContent = `Mostrando \${filtered.length} registros`;
        }

        function renderTable(colunas, dados) {
            const thead = document.getElementById("tableHead");
            const tbody = document.getElementById("tableBody");

            let headHtml = "<tr>";
            colunas.forEach(c => {
                headHtml += `<th class="\${c.tipo === 'currency' || c.tipo === 'number' ? 'text-center' : ''}">\${c.rotulo}</th>`;
            });
            headHtml += "</tr>";
            thead.innerHTML = headHtml;

            tbody.innerHTML = "";
            dados.forEach(item => {
                const tr = document.createElement("tr");
                let rowHtml = "";
                colunas.forEach(c => {
                    const raw = item[c.chave];
                    let formatted = raw ?? "-";
                    if (c.tipo === 'currency') formatted = formatCurrency(raw);
                    else if (c.tipo === 'number') formatted = formatNumber(raw);

                    const align = (c.tipo === 'currency' || c.tipo === 'number') ? 'text-center' : '';
                    rowHtml += `<td class="\${align}">\${formatted}</td>`;
                });
                tr.innerHTML = rowHtml;
                tbody.appendChild(tr);
            });
        }

        function applyFilters() {
            const search = document.getElementById("searchInput").value.toLowerCase();
            const supplier = document.getElementById("filterFornecedor").value;

            const filtered = DATASET.dados.filter(item => {
                const matchText = Object.values(item).some(val => String(val).toLowerCase().includes(search));
                const itemSupplier = item.fornecedor || item.fornecedor_cnpj;
                const matchSupplier = supplier === "" || itemSupplier === supplier;
                return matchText && matchSupplier;
            });

            renderTable(DATASET.colunas, filtered);
            updateCards(filtered);
        }

        document.addEventListener("DOMContentLoaded", () => {
            populateSuppliers(DATASET.dados);
            renderTable(DATASET.colunas, DATASET.dados);
            updateCards(DATASET.dados);

            document.getElementById("searchInput").addEventListener("input", applyFilters);
            document.getElementById("filterFornecedor").addEventListener("change", applyFilters);
        });
    </script>
</body>
</html>
HTML;
    }
}