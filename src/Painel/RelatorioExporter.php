<?php

namespace FNDE\Painel;

class RelatorioExporter
{
    /**
     * Empacota o template completo do dashboard com o payload de dados injetado
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
    <title>Painel de Faturamento e Recebimento - PNLD</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .fs-7 { font-size: 0.85rem; }
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
                        <h6 class="text-muted fw-normal mb-1">Total Tiragem (Exemplares)</h6>
                        <h3 class="fw-bold text-success mb-0" id="cardTotalTiragem">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Títulos Recebidos</h6>
                        <h3 class="fw-bold text-info mb-0" id="cardTotalRegistros">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-danger">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Títulos Não Recebidos</h6>
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
                        <h6 class="text-muted fw-normal mb-1">Posições Porta Palete</h6>
                        <h3 class="fw-bold text-primary mb-0" id="cardTotalPaletes">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-success">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Média de Dias Armazenamento</h6>
                        <h3 class="fw-bold text-success mb-0" id="cardTotalDias">0 dias</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-1">Diária Palete/Dia</h6>
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
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar por Fornecedor, SKU, Descrição...">
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

        <!-- Tabela -->
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

    <!-- Modal para Detalhar Item / Colunas Excedentes -->
    <div class="modal fade" id="modalDetalhesItem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTitulo">Detalhes do Registro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" id="modalBodyContent">
                    <!-- Conteúdo dinâmico montado via JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Payload de Dados Injetado -->
    <script>
        const DATASET = {$jsonString};
        let filteredDataset = [];

        function formatCurrency(val) {
            return (val || 0).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
        }

        function formatNumber(val, decimals = 0) {
            return (val || 0).toLocaleString("pt-BR", { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        }

        function populateSuppliers(dados) {
            const select = document.getElementById("filterFornecedor");
            select.innerHTML = '<option value="">Todos os Fornecedores</option>';
            
            const suppliers = [...new Set(dados.map(i => {
                return i.fornecedor || i.fornecedor_cnpj || i.editora || '';
            }))].filter(Boolean).sort();

            suppliers.forEach(s => {
                const opt = document.createElement("option");
                opt.value = s;
                opt.textContent = s;
                select.appendChild(opt);
            });
        }

        function updateCards(filtered) {
            const totalFat = filtered.reduce((acc, c) => {
                const val = c.valor_faturamento || c.valorfaturamento || c.valor_faturado || c.subtotal || 0;
                return acc + Number(val);
            }, 0);

            const totalTir = filtered.reduce((acc, c) => acc + Number(c.tiragem || 0), 0);
            const totalPal = filtered.reduce((acc, c) => {
                const paletes = c.posicao_porta_palete || c.posicoes_porta_palete || c.qtdpaletesporpor milheiro || c.qtdpaletes || c.paletes || 0;
                return acc + Number(paletes);
            }, 0);

            const totalDias = filtered.reduce((acc, c) => acc + Number(c.dias_armazenamento || c.diasarmazenamento || 0), 0);
            const totalRegistros = filtered.length;
            const mediaDias = totalRegistros > 0 ? (totalDias / totalRegistros) : 0;

            const naoRecebidos = filtered.filter(c => {
                const dataRec = c.data_recebimento || c.datarecebimento || c.data_rec || '';
                return String(dataRec).toLowerCase().includes('não recebido') || !dataRec;
            }).length;

            const recebidos = totalRegistros - naoRecebidos;

            document.getElementById("cardTotalFaturamento").textContent = formatCurrency(totalFat);
            document.getElementById("cardTotalTiragem").textContent = formatNumber(totalTir);
            document.getElementById("cardTotalRegistros").textContent = formatNumber(recebidos);
            document.getElementById("cardTotalNaoRecebido").textContent = formatNumber(naoRecebidos);
            document.getElementById("cardTotalPaletes").textContent = formatNumber(totalPal);
            document.getElementById("cardTotalDias").textContent = `\${formatNumber(mediaDias, 1)} dias`;

            if (DATASET.metadados && DATASET.metadados.dataGeracao) {
                document.getElementById("cardFaturadoAte").textContent = DATASET.metadados.dataGeracao.split(' ')[0];
            }

            document.getElementById("recordCounter").textContent = `Mostrando \${filtered.length} registros`;
        }

        function renderTable(colunas, dados) {
            const thead = document.getElementById("tableHead");
            const tbody = document.getElementById("tableBody");

            // 1. Renderiza o Cabeçalho (rotulos originais da planilha)
            let headHtml = "<tr><th class='text-center'>#</th>";
            colunas.forEach(c => {
                const align = (c.tipo === 'currency' || c.tipo === 'number') ? 'text-center' : 'text-start';
                headHtml += `<th class="\${align}">\${c.rotulo}</th>`;
            });
            headHtml += "</tr>";
            thead.innerHTML = headHtml;

            // 2. Renderiza as Linhas com Formatações e Links para Modal
            tbody.innerHTML = "";
            dados.forEach((item, index) => {
                const tr = document.createElement("tr");
                let rowHtml = `<td class="fw-bold text-secondary text-center">\${index + 1}</td>`;

                colunas.forEach(c => {
                    const val = item[c.chave];
                    let formatted = val ?? "-";

                    // Formatação por Tipo de Coluna
                    if (c.tipo === 'currency') {
                        formatted = `<span class="fw-bold text-success">\${formatCurrency(val)}</span>`;
                    } else if (c.tipo === 'number') {
                        formatted = formatNumber(val);
                    }

                    // Destaques e Links para o Modal no Fornecedor, SKU e Descrição
                    if (c.chave.includes('fornecedor')) {
                        formatted = `<a href="javascript:void(0)" class="text-decoration-none fw-semibold text-dark" onclick="abrirModalDetalhes(\${index})">\${val || '-'}</a>`;
                    } else if (c.chave.includes('sku')) {
                        formatted = `<a href="javascript:void(0)" class="badge bg-light text-dark border text-decoration-none" onclick="abrirModalDetalhes(\${index})">\${val || '-'}</a>`;
                    } else if (c.chave.includes('descricao') || c.chave.includes('livro') || c.chave.includes('titulo')) {
                        formatted = `<a href="javascript:void(0)" class="text-decoration-none fw-semibold text-primary" onclick="abrirModalDetalhes(\${index})">\${val || '-'}</a>`;
                    } else if (c.chave.includes('data') || c.chave.includes('recebimento')) {
                        if (String(val).toLowerCase().includes('não recebido') || !val) {
                            formatted = `<span class="badge bg-danger text-white">Não Recebido</span>`;
                        } else {
                            formatted = `<span class="badge bg-info text-dark">\${val}</span>`;
                        }
                    } else if (String(val).endsWith('.pdf')) {
                        formatted = `<a class="btn btn-sm btn-outline-primary" target="_blank" href="\${val}"><i class="bi bi-file-earmark-pdf"></i></a>`;
                    }

                    const align = (c.tipo === 'currency' || c.tipo === 'number') ? 'text-center' : '';
                    rowHtml += `<td class="\${align}">\${formatted}</td>`;
                });

                tr.innerHTML = rowHtml;
                tbody.appendChild(tr);
            });
        }

        function abrirModalDetalhes(index) {
            const item = filteredDataset[index];
            if (!item) return;

            const colunasMap = DATASET.colunas || [];
            let titulo = "Detalhes do Registro";

            let htmlConteudo = `<div class="table-responsive">
                <table class="table table-sm table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr><th>Coluna</th><th>Valor</th></tr>
                    </thead>
                    <tbody>`;

            colunasMap.forEach(col => {
                const val = item[col.chave];
                let displayVal = val !== null && val !== undefined && val !== '' ? val : '-';

                if (col.tipo === 'currency') displayVal = formatCurrency(val);
                else if (col.tipo === 'number') displayVal = formatNumber(val);

                if (col.chave.includes('descricao') || col.chave.includes('livro')) {
                    titulo = val;
                }

                htmlConteudo += `<tr>
                    <td class="fw-semibold text-secondary">\${col.rotulo}</td>
                    <td>\${displayVal}</td>
                </tr>`;
            });

            htmlConteudo += `</tbody></table></div>`;

            document.getElementById("modalTitulo").textContent = titulo || "Detalhes do Registro";
            document.getElementById("modalBodyContent").innerHTML = htmlConteudo;

            const modalElement = new bootstrap.Modal(document.getElementById("modalDetalhesItem"));
            modalElement.show();
        }

        function applyFilters() {
            const search = document.getElementById("searchInput").value.toLowerCase();
            const supplier = document.getElementById("filterFornecedor").value;

            filteredDataset = DATASET.dados.filter(item => {
                const matchText = Object.values(item).some(val => String(val).toLowerCase().includes(search));
                const itemSupplier = item.fornecedor || item.fornecedor_cnpj || item.editora || '';
                const matchSupplier = supplier === "" || itemSupplier === supplier;
                return matchText && matchSupplier;
            });

            renderTable(DATASET.colunas, filteredDataset);
            updateCards(filteredDataset);
        }

        document.addEventListener("DOMContentLoaded", () => {
            filteredDataset = DATASET.dados || [];
            populateSuppliers(filteredDataset);
            renderTable(DATASET.colunas, filteredDataset);
            updateCards(filteredDataset);

            document.getElementById("searchInput").addEventListener("input", applyFilters);
            document.getElementById("filterFornecedor").addEventListener("change", applyFilters);
        });
    </script>
</body>
</html>
HTML;
    }
}