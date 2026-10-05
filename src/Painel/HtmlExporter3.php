<?php

namespace FNDE\Painel;

class HtmlExporter
{
    /**
     * Empacota a estrutura HTML com Modal de NF, Cards e Tabela Interativa
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
                        <h3 class="fw-bold text-success mb-0" id="cardTotalDias">0 dias</h3>
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

    <!-- MODAL DE VISUALIZAÇÃO DE NOTA FISCAL (PDF/DOCUMENTO) -->
    <div class="modal fade" id="modalNf" tabindex="-1" aria-labelledby="modalNfLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="height: 90vh;">
            <div class="modal-content h-100 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center" id="modalNfLabel">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Visualização do Documento NF
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-0 bg-secondary-subtle d-flex flex-column align-items-center justify-content-center">
                    <iframe id="iframeNf" src="" class="w-100 h-100 border-0" style="display: none;"></iframe>
                    <div id="modalFallback" class="text-center p-4">
                        <i class="bi bi-file-earmark-pdf text-muted display-1 mb-3"></i>
                        <p class="fs-5 text-secondary mb-3" id="modalFallbackText">Nenhum documento disponível para este registro.</p>
                        <a id="btnExternalNf" href="#" target="_blank" class="btn btn-primary d-none">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Abrir em Nova Aba
                        </a>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Lógica de Processamento e Injeção dos Dados -->
    <script>
        const DATASET = {$jsonString};

        let bsModalNf = null;

        document.addEventListener("DOMContentLoaded", () => {
            bsModalNf = new bootstrap.Modal(document.getElementById('modalNf'));
            
            populateSuppliers(DATASET.dados);
            renderTable(DATASET.colunas, DATASET.dados);
            updateCards(DATASET.dados);

            document.getElementById("searchInput").addEventListener("input", applyFilters);
            document.getElementById("filterFornecedor").addEventListener("change", applyFilters);
        });

        function formatCurrency(val) {
            return (Number(val) || 0).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
        }

        function formatNumber(val, decimals = 0) {
            return (Number(val) || 0).toLocaleString("pt-BR", { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        }

        function openNfModal(url, filename) {
            const iframe = document.getElementById("iframeNf");
            const fallback = document.getElementById("modalFallback");
            const fallbackText = document.getElementById("modalFallbackText");
            const btnExt = document.getElementById("btnExternalNf");
            const modalTitle = document.getElementById("modalNfLabel");

            modalTitle.innerHTML = `<i class="bi bi-file-earmark-pdf me-2"></i> NF: \${filename || 'Documento'}`;

            if (url && url !== '#') {
                iframe.src = url;
                iframe.style.display = "block";
                fallback.classList.add("d-none");
                btnExt.href = url;
                btnExt.classList.remove("d-none");
            } else {
                iframe.src = "";
                iframe.style.display = "none";
                fallback.classList.remove("d-none");
                fallbackText.textContent = "O arquivo PDF desta Nota Fiscal não foi localizado ou não está anexado.";
                btnExt.classList.add("d-none");
            }

            bsModalNf.show();
        }

        function populateSuppliers(dados) {
            const select = document.getElementById("filterFornecedor");
            select.innerHTML = '<option value="">Todos os Fornecedores</option>';
            
            const list = dados.map(i => i.fornecedor || i.nome_do_fornecedor || i.fornecedor_cnpj).filter(Boolean);
            const suppliers = [...new Set(list)].sort();

            suppliers.forEach(s => {
                const opt = document.createElement("option");
                opt.value = s;
                opt.textContent = s;
                select.appendChild(opt);
            });
        }

        function updateCards(filtered) {
            const totalFat = filtered.reduce((acc, c) => acc + (Number(c.valor_faturado || c.valor_faturamento || c.valorfaturamento || c.subtotal) || 0), 0);
            const totalTir = filtered.reduce((acc, c) => acc + (Number(c.tiragem) || 0), 0);
            const totalPal = filtered.reduce((acc, c) => acc + (Number(c.posicoes_porta_palete || c.qtdpaletes) || 0), 0);
            
            const recebidos = filtered.filter(c => c.data_rec || c.datarecebimento || (Number(c.qtd_paletes_recebidos) > 0)).length;
            const naoRecebidos = filtered.length - recebidos;

            const totalDias = filtered.reduce((acc, c) => acc + (Number(c.dias_armazenados || c.dias_armazenamento || c.diasarmazenamento) || 0), 0);
            const mediaDias = filtered.length > 0 ? (totalDias / filtered.length) : 0;

            document.getElementById("cardTotalFaturamento").textContent = formatCurrency(totalFat);
            document.getElementById("cardTotalTiragem").textContent = formatNumber(totalTir);
            document.getElementById("cardTotalRegistros").textContent = formatNumber(recebidos);
            document.getElementById("cardTotalNaoRecebido").textContent = formatNumber(naoRecebidos);
            document.getElementById("cardTotalPaletes").textContent = formatNumber(totalPal);
            document.getElementById("cardTotalDias").textContent = `\${formatNumber(mediaDias, 1)} dias`;
            
            if (DATASET.metadados && DATASET.metadados.dataGeracao) {
                document.getElementById("cardFaturadoAte").textContent = DATASET.metadados.dataGeracao.split(' ')[0];
            } else if (filtered.length > 0 && filtered[0].data_atual) {
                document.getElementById("cardFaturadoAte").textContent = filtered[0].data_atual;
            }

            document.getElementById("recordCounter").textContent = `Mostrando \${filtered.length} registros`;
        }

        function renderTable(colunas, dados) {
            const thead = document.getElementById("tableHead");
            const tbody = document.getElementById("tableBody");

            let headHtml = "<tr>";
            colunas.forEach(c => {
                const alignClass = (c.tipo === 'currency' || c.tipo === 'number') ? 'text-end' : (c.chave.includes('pdf') || c.chave.includes('link') ? 'text-center' : '');
                headHtml += `<th class="\${alignClass}">\${c.rotulo}</th>`;
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

                    // Coluna de Link/Ação para Modal da NF
                    if (c.chave === 'link_nf' || c.chave === 'pdf' || c.chave === 'nome_link_nf' || c.chave === 'arquivo_nf') {
                        const url = item.link_nf || item.pdf || '#';
                        const label = item.nome_link_nf || item.arquivo_nf || 'Ver NF';
                        formatted = `<button class="btn btn-sm btn-outline-primary" onclick="openNfModal('\${url}', '\${label}')">
                                        <i class="bi bi-file-pdf me-1"></i>\${label}
                                     </button>`;
                        rowHtml += `<td class="text-center">\${formatted}</td>`;
                    } 
                    // Formatação Moeda
                    else if (c.tipo === 'currency') {
                        formatted = formatCurrency(raw);
                        rowHtml += `<td class="text-end fw-semibold">\${formatted}</td>`;
                    } 
                    // Formatação Numérica
                    else if (c.tipo === 'number') {
                        formatted = formatNumber(raw);
                        rowHtml += `<td class="text-end">\${formatted}</td>`;
                    } 
                    // Outras colunas
                    else {
                        rowHtml += `<td>\${formatted}</td>`;
                    }
                });

                tr.innerHTML = rowHtml;
                tbody.appendChild(tr);
            });
        }

        function applyFilters() {
            const search = document.getElementById("searchInput").value.toLowerCase();
            const supplier = document.getElementById("filterFornecedor").value;

            const filtered = DATASET.dados.filter(item => {
                const matchText = Object.values(item).some(val => String(val ?? '').toLowerCase().includes(search));
                const itemSupplier = item.fornecedor || item.nome_do_fornecedor || item.fornecedor_cnpj;
                const matchSupplier = supplier === "" || itemSupplier === supplier;
                return matchText && matchSupplier;
            });

            renderTable(DATASET.colunas, filtered);
            updateCards(filtered);
        }
    </script>
</body>
</html>
HTML;
    }
}