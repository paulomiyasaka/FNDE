<?php

namespace FNDE\Painel;

class HtmlExporter
{
    /**
     * Gera o arquivo HTML standalone offline com correção matemática contábil
     * e renderização completa da tabela e modais.
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
    <title>Painel de Faturamento e Recebimento - FNDE / PNLD</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .table-responsive { max-height: 65vh; overflow-y: auto; }
        th.sortable { cursor: pointer; user-select: none; }
        th.sortable:hover { background-color: #343a40 !important; }
        .detail-row-header { background-color: #f8f9fa; font-weight: 600; color: #495057; width: 35%; }
        .link-modal-detail { color: inherit; text-decoration: none; border-bottom: 1px dashed #0d6efd; cursor: pointer; }
        .link-modal-detail:hover { color: #0d6efd; border-bottom-style: solid; }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                Gestão de Faturamento - PNLD / Editoras
            </span>
            <span class="badge bg-light text-primary fs-7">Versão Offline / Exportada</span>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <!-- Cards de Resumo Executivo (Linha 1) -->
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-primary">
                    <div class="card-body py-3">
                        <h6 class="text-muted fw-normal mb-1">Total Faturado</h6>
                        <h3 class="fw-bold text-primary mb-0" id="cardTotalFaturamento">R$ 0,00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-success">
                    <div class="card-body py-3">
                        <h6 class="text-muted fw-normal mb-1">Total de Exemplares (Tiragem)</h6>
                        <h3 class="fw-bold text-success mb-0" id="cardTotalTiragem">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body py-3">
                        <h6 class="text-muted fw-normal mb-1">Títulos Recebidos</h6>
                        <h3 class="fw-bold text-info mb-0" id="cardTotalRecebidos">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-danger">
                    <div class="card-body py-3">
                        <h6 class="text-muted fw-normal mb-1">Títulos Não Recebidos</h6>
                        <h3 class="fw-bold text-danger mb-0" id="cardTotalNaoRecebidos">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Resumo Executivo (Linha 2) -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-primary">
                    <div class="card-body py-3">
                        <h6 class="text-muted fw-normal mb-1">Total Posições Porta Palete</h6>
                        <h3 class="fw-bold text-primary mb-0" id="cardTotalPaletes">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-success">
                    <div class="card-body py-3">
                        <h6 class="text-muted fw-normal mb-1">Média de Dias (Armazenamento)</h6>
                        <h3 class="fw-bold text-success mb-0" id="cardMediaDias">0 dias</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body py-3">
                        <h6 class="text-muted fw-normal mb-1">Média Armazenagem Palete/Dia</h6>
                        <h3 class="fw-bold text-info mb-0" id="cardValorDiaria">R$ 0,00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-danger">
                    <div class="card-body py-3">
                        <h6 class="text-muted fw-normal mb-1">Faturado até</h6>
                        <h3 class="fw-bold text-danger mb-0" id="cardFaturadoAte">--/--/----</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Filtros e Busca -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar por Fornecedor, SKU, Descrição, Chave...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="filterFornecedor" class="form-select">
                            <option value="">Todos os Fornecedores</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="badge bg-secondary p-2 fs-6" id="recordCounter">Mostrando 0 registros</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela Dinâmica -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-dark text-nowrap" id="tableHead"></thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE FICHA COMPLETA -->
    <div class="modal fade" id="modalDetails" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fs-6" id="modalDetailsLabel"><i class="bi bi-file-earmark-text me-2"></i> Detalhes do Registro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0 align-middle">
                            <tbody id="modalDetailsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE VISUALIZAÇÃO DA NOTA FISCAL -->
    <div class="modal fade" id="modalNf" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="height: 90vh;">
            <div class="modal-content h-100 shadow">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title fs-6" id="modalNfLabel"><i class="bi bi-file-earmark-pdf me-2"></i> Visualização NF</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 bg-secondary-subtle d-flex flex-column align-items-center justify-content-center">
                    <iframe id="iframeNf" src="" class="w-100 h-100 border-0" style="display: none;"></iframe>
                    <div id="modalFallback" class="text-center p-4">
                        <i class="bi bi-file-earmark-pdf text-muted display-1 mb-3"></i>
                        <p class="fs-5 text-secondary mb-3" id="modalFallbackText">Nenhum documento anexado.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const DATASET = {$jsonString};

        let currentFilteredData = [];
        let bsModalNf = null;
        let bsModalDetails = null;
        let sortKey = null;
        let sortAsc = true;

        document.addEventListener("DOMContentLoaded", () => {
            bsModalNf = new bootstrap.Modal(document.getElementById('modalNf'));
            bsModalDetails = new bootstrap.Modal(document.getElementById('modalDetails'));

            populateFornecedores(DATASET.dados || []);
            applyFilters();

            document.getElementById("searchInput").addEventListener("input", applyFilters);
            document.getElementById("filterFornecedor").addEventListener("change", applyFilters);
        });

        /**
         * PARSER MATEMÁTICO PRECIOSO
         * Garante que "2,55" vira 2.55 e "30.600,00" / "30600.00" viram 30600.00
         */
        function parseCurrencyNumber(val) {
            if (val === null || val === undefined || val === "") return 0;
            if (typeof val === "number") return val;

            let str = String(val).replace(/R\$\s?|\s/g, "").trim();
            if (!str) return 0;

            const hasComma = str.includes(",");
            const hasDot = str.includes(".");

            if (hasComma && hasDot) {
                // Formato BR clássico: "30.600,00" -> tira ponto de milhar e muda vírgula
                str = str.replace(/\./g, "").replace(",", ".");
            } else if (hasComma && !hasDot) {
                // Formato BR com vírgula: "2,55" ou "30600,00" -> muda vírgula para ponto
                str = str.replace(",", ".");
            } else if (hasDot && !hasComma) {
                // Analisa a parte após o ponto para diferenciar "30.600" (milhar) de "2.55" (decimal)
                const parts = str.split(".");
                if (parts.length > 1) {
                    const lastPart = parts[parts.length - 1];
                    // Se o último bloco tiver 3 dígitos e houver mais de um ponto (ex: 1.000.000 ou 30.600 inteiro Sem centavos)
                    if (lastPart.length === 3 && parts.length > 2) {
                        str = str.replace(/\./g, "");
                    }
                }
            }

            const num = parseFloat(str);
            return isNaN(num) ? 0 : num;
        }

        function formatCurrency(val) {
            const num = parseCurrencyNumber(val);
            return num.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
        }

        function formatNumber(val, decimals = 0) {
            const num = parseCurrencyNumber(val);
            return num.toLocaleString("pt-BR", { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        }

        function parseDate(dateStr) {
            if (!dateStr || typeof dateStr !== "string") return null;
            const parts = dateStr.trim().split("/");
            if (parts.length === 3) {
                return new Date(parseInt(parts[2], 10), parseInt(parts[1], 10) - 1, parseInt(parts[0], 10));
            }
            return null;
        }

        function updateCards(dados) {
            let totalFaturamento = 0;
            let totalTiragem = 0;
            let totalPaletes = 0;
            let titulosRecebidos = 0;
            let titulosNaoRecebidos = 0;
            let somaDiasArmazenamento = 0;
            let somaValoresDiarias = 0;
            let qtdValoresDiarias = 0;
            let maiorDataObj = null;
            let maiorDataTexto = "--/--/----";

            dados.forEach(item => {
                const dtRec = item.data_recebimento || item.dataRecebimento || "";
                const isNaoRecebido = (!dtRec || dtRec === "Não Recebido" || String(dtRec).trim() === "");

                // REGRA DE NEGÓCIO: Não contabiliza Faturamento e Tiragem se for Não Recebido
                if (!isNaoRecebido) {
                    titulosRecebidos++;
                    const valFat = parseCurrencyNumber(item.valor_faturamento || item.subtotal || item.valorFaturamento || 0);
                    totalFaturamento += valFat;

                    const tir = parseCurrencyNumber(item.tiragem || 0);
                    totalTiragem += tir;

                    somaDiasArmazenamento += parseCurrencyNumber(item.dias_armazenamento || item.diasArmazenamento || 0);
                } else {
                    titulosNaoRecebidos++;
                }

                totalPaletes += parseCurrencyNumber(item.posicoes_porta_palete || item.qtdPaletesPorMilheiro || 0);

                const valDiariaLinha = parseCurrencyNumber(item.valor_dia_armazenagem || item.valor_armazenagem_palete_dia || item.valorDiaria || 0);
                if (valDiariaLinha > 0) {
                    somaValoresDiarias += valDiariaLinha;
                    qtdValoresDiarias++;
                }

                const dataStr = item.data_atual || item.data_faturamento || item.dataFaturamento || "";
                const dObj = parseDate(dataStr);
                if (dObj && (!maiorDataObj || dObj > maiorDataObj)) {
                    maiorDataObj = dObj;
                    maiorDataTexto = dataStr;
                }
            });

            const mediaDias = titulosRecebidos > 0 ? (somaDiasArmazenamento / titulosRecebidos) : 0;
            const mediaValDiaria = qtdValoresDiarias > 0 ? (somaValoresDiarias / qtdValoresDiarias) : 0;

            document.getElementById("cardTotalFaturamento").textContent = formatCurrency(totalFaturamento);
            document.getElementById("cardTotalTiragem").textContent = formatNumber(totalTiragem);
            document.getElementById("cardTotalRecebidos").textContent = formatNumber(titulosRecebidos);
            document.getElementById("cardTotalNaoRecebidos").textContent = formatNumber(titulosNaoRecebidos);
            document.getElementById("cardTotalPaletes").textContent = formatNumber(totalPaletes);
            document.getElementById("cardMediaDias").textContent = `\${formatNumber(mediaDias, 0)} dias`;
            document.getElementById("cardValorDiaria").textContent = formatCurrency(mediaValDiaria);
            document.getElementById("cardFaturadoAte").textContent = maiorDataTexto;

            document.getElementById("recordCounter").textContent = `Mostrando \${dados.length} registros`;
        }

        function renderHeader(colunas) {
            const thead = document.getElementById("tableHead");
            if (!thead || !colunas) return;
            let html = "<tr>";

            colunas.forEach(col => {
                let alignClass = "text-start";
                if (col.tipo === "numero" || col.tipo === "moeda" || col.tipo === "dias") alignClass = "text-end";
                if (col.tipo === "status_data" || col.tipo === "link_nf" || col.tipo === "badge") alignClass = "text-center";

                const isSorted = sortKey === col.chave;
                const icon = isSorted ? (sortAsc ? '<i class="bi bi-arrow-up text-warning ms-1"></i>' : '<i class="bi bi-arrow-down text-warning ms-1"></i>') : '';

                html += `<th class="\${alignClass} sortable" onclick="sortTable('\${col.chave}')">\${col.rotulo}\${icon}</th>`;
            });

            html += "</tr>";
            thead.innerHTML = html;
        }

        function renderTable(colunas, dados) {
            renderHeader(colunas);
            const tbody = document.getElementById("tableBody");
            if (!tbody) return;
            tbody.innerHTML = "";

            if (!dados || dados.length === 0) {
                tbody.innerHTML = `<tr><td colspan="\${colunas ? colunas.length : 10}" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Nenhum registro encontrado.</td></tr>`;
                return;
            }

            dados.forEach((row, idx) => {
                const tr = document.createElement("tr");

                colunas.forEach(col => {
                    const td = document.createElement("td");
                    const rawVal = row[col.chave];

                    if (col.tipo === "link_nf" || col.chave === "link_nf") {
                        td.className = "text-center";
                        const url = row.link_nf || row.pdf || "";
                        if (url && url.trim() !== "") {
                            td.innerHTML = `<button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="openNfModal('\${url}')"><i class="bi bi-file-earmark-pdf me-1"></i>Ver NF</button>`;
                        } else {
                            td.innerHTML = `<span class="badge bg-light text-muted border">Sem NF</span>`;
                        }
                    } else if (col.tipo === "moeda") {
                        td.className = "text-end fw-semibold text-success";
                        td.textContent = formatCurrency(rawVal);
                    } else if (col.tipo === "numero") {
                        td.className = "text-end";
                        td.textContent = formatNumber(rawVal);
                    } else if (col.tipo === "status_data") {
                        td.className = "text-center";
                        if (!rawVal || rawVal === "Não Recebido") {
                            td.innerHTML = `<span class="badge bg-danger-subtle text-danger border border-danger">Não Recebido</span>`;
                        } else {
                            td.innerHTML = `<span class="badge bg-success-subtle text-success border border-success">\${rawVal}</span>`;
                        }
                    } else {
                        td.textContent = rawVal !== undefined && rawVal !== null ? rawVal : "-";
                    }

                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            });
        }

        function populateFornecedores(dados) {
            const select = document.getElementById("filterFornecedor");
            if (!select) return;
            select.innerHTML = '<option value="">Todos os Fornecedores</option>';

            const list = dados.map(i => i.nome_do_fornecedor || i.fornecedor).filter(Boolean);
            const fornecedores = [...new Set(list)].sort();

            fornecedores.forEach(f => {
                const opt = document.createElement("option");
                opt.value = f;
                opt.textContent = f;
                select.appendChild(opt);
            });
        }

        function applyFilters() {
            const search = document.getElementById("searchInput").value.toLowerCase();
            const fornecedor = document.getElementById("filterFornecedor").value;

            const baseDados = DATASET.dados || [];
            currentFilteredData = baseDados.filter(item => {
                const matchSearch = Object.values(item).some(v => String(v ?? '').toLowerCase().includes(search));
                const itemForn = item.nome_do_fornecedor || item.fornecedor;
                const matchForn = !fornecedor || itemForn === fornecedor;

                return matchSearch && matchForn;
            });

            if (DATASET.colunas) {
                renderTable(DATASET.colunas, currentFilteredData);
            }
            updateCards(currentFilteredData);
        }

        function sortTable(chave) {
            if (sortKey === chave) {
                sortAsc = !sortAsc;
            } else {
                sortKey = chave;
                sortAsc = true;
            }
            currentFilteredData.sort((a, b) => {
                let valA = a[sortKey] ?? '';
                let valB = b[sortKey] ?? '';
                const numA = parseCurrencyNumber(valA);
                const numB = parseCurrencyNumber(valB);
                if (!isNaN(numA) && !isNaN(numB) && typeof valA !== 'boolean') {
                    return sortAsc ? numA - numB : numB - numA;
                }
                return sortAsc ? String(valA).localeCompare(String(valB)) : String(valB).localeCompare(String(valA));
            });
            renderTable(DATASET.colunas, currentFilteredData);
        }

        function openNfModal(url) {
            const iframe = document.getElementById("iframeNf");
            const fallback = document.getElementById("modalFallback");
            if (url && url.trim() !== "") {
                iframe.src = url;
                iframe.style.display = "block";
                fallback.classList.add("d-none");
            } else {
                iframe.src = "";
                iframe.style.display = "none";
                fallback.classList.remove("d-none");
            }
            bsModalNf.show();
        }
    </script>
</body>
</html>
HTML;
    }
}