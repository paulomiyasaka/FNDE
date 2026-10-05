// Estado global
let parsedData = [];

function formatCurrency(val) {
    return (val || 0).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

function formatNumber(val, decimals = 0) {
    return (val || 0).toLocaleString("pt-BR", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function populateSupplierSelect(data) {
    const select = document.getElementById("filterFornecedor");
    if (!select) return;
    select.innerHTML = '<option value="">Todos os Fornecedores</option>';

    const suppliers = [...new Set(data.map(item => item.fornecedor))].sort();

    suppliers.forEach(supplier => {
        if (!supplier) return;
        const option = document.createElement("option");
        option.value = supplier;
        option.textContent = supplier;
        select.appendChild(option);
    });
}

function updateCards(filteredData) {
    const totalFaturamento = filteredData.reduce((acc, cur) => {
        return cur.dataRecebimento === "Não Recebido" ? acc : acc + (cur.valorFaturamento || 0);
    }, 0);
    
    const totalTiragem = filteredData.reduce((acc, cur) => {
        return cur.dataRecebimento === "Não Recebido" ? acc : acc + (cur.tiragem || 0);
    }, 0);

    const totalPaletes = filteredData.reduce((acc, cur) => acc + (cur.qtdPaletesPorMilheiro || 0), 0);
    const totalDias = filteredData.reduce((acc, cur) => acc + (cur.diasArmazenamento || 0), 0);
    const qtdRegistros = filteredData.filter(item => item.dataRecebimento !== "Não Recebido").length;

    if (document.getElementById("cardTotalFaturamento")) document.getElementById("cardTotalFaturamento").textContent = formatCurrency(totalFaturamento);
    if (document.getElementById("cardTotalTiragem")) document.getElementById("cardTotalTiragem").textContent = formatNumber(totalTiragem);
    if (document.getElementById("cardTotalNaoRecebido")) document.getElementById("cardTotalNaoRecebido").textContent = filteredData.filter(item => item.dataRecebimento === "Não Recebido").length;
    if (document.getElementById("cardTotalRegistros")) document.getElementById("cardTotalRegistros").textContent = qtdRegistros;
    if (document.getElementById("cardTotalPaletes")) document.getElementById("cardTotalPaletes").textContent = formatNumber(totalPaletes);
    if (document.getElementById("cardTotalDias")) document.getElementById("cardTotalDias").textContent = formatNumber(qtdRegistros > 0 ? totalDias / qtdRegistros : 0);

    if (document.getElementById("recordCounter")) document.getElementById("recordCounter").textContent = `Mostrando ${filteredData.length} registros`;
}

/**
 * Renderiza a Tabela com links clicáveis para abrir os detalhes excedentes
 */
function renderTable(data) {
    const tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = "";

    if (data.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="11" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Nenhum registro encontrado para os filtros selecionados.
                </td>
            </tr>
        `;
        return;
    }

    data.forEach((item, index) => {
        const tr = document.createElement("tr");
        const statusNaoRecebido = item.dataRecebimento === 'Não Recebido';

        // Links interativos que disparam o Modal
        const btnDetalharSupplier = `<a href="javascript:void(0)" class="text-decoration-none fw-semibold text-dark" onclick="abrirModalDetalhes(${index})">${item.fornecedor}</a>`;
        const btnDetalharSku = `<a href="javascript:void(0)" class="badge bg-light text-dark border text-decoration-none" onclick="abrirModalDetalhes(${index})">${item.sku}</a>`;
        const btnDetalharDesc = `<a href="javascript:void(0)" class="text-decoration-none fw-semibold text-primary" onclick="abrirModalDetalhes(${index})">${item.descricao}</a>`;

        let linha = `
            <td class="fw-bold text-secondary fs-7 text-center">${index + 1}</td>
            <td>
                <div>${btnDetalharSupplier}</div>
                <small class="text-muted">CNPJ: ${item.cnpj || 'N/A'}</small>
            </td>
            <td class="text-center">${btnDetalharSku}</td>
            <td>${btnDetalharDesc}</td>
            <td class="text-center"><span class="badge bg-secondary">${item.acervo || ''}</span></td>
            <td class="text-center fw-bold">${formatNumber(item.tiragem)}</td>
            <td class="text-center">${formatNumber(item.qtdPaletesPorMilheiro)}</td>
            <td class="text-center text-muted">${formatNumber(item.diasArmazenamento)}</td>
        `;

        if (statusNaoRecebido) {
            linha += `
                <td class="text-center"><span class="badge bg-danger text-white">${item.dataRecebimento}</span></td>
                <td class="text-center fw-bold text-danger">${formatCurrency(item.valorFaturamento)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-secondary" title="Livro não recebido" onclick="alert('O livro ${item.descricao} não foi recebido.')">
                        <i class="bi bi-exclamation-circle"></i>
                    </button>
                </td>
            `;
        } else {
            linha += `
                <td class="text-center"><span class="badge bg-info text-dark">${item.dataRecebimento}</span></td>
                <td class="text-center fw-bold text-success">${formatCurrency(item.valorFaturamento)}</td>
                <td class="text-center">
                    <a class="btn btn-sm btn-outline-primary" target="_blank" title="Download NF" href="saida/${item.arquivoNf}">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                </td>
            `;
        }

        tr.innerHTML = linha;
        tableBody.appendChild(tr);
    });
}

/**
 * Exibe o Modal com TODAS as informações do item (incluindo as colunas excedentes)
 */
function abrirModalDetalhes(index) {
    const item = parsedData[index];
    if (!item) return;

    document.getElementById("modalTitulo").textContent = item.descricao || "Detalhes do Item";

    let htmlConteudo = `
        <div class="row g-3">
            <div class="col-md-6"><strong>Fornecedor:</strong> ${item.fornecedor}</div>
            <div class="col-md-6"><strong>CNPJ:</strong> ${item.cnpj || 'N/A'}</div>
            <div class="col-md-6"><strong>SKU:</strong> ${item.sku}</div>
            <div class="col-md-6"><strong>Acervo:</strong> ${item.acervo}</div>
            <div class="col-md-6"><strong>Tiragem:</strong> ${formatNumber(item.tiragem)}</div>
            <div class="col-md-6"><strong>Valor Faturamento:</strong> ${formatCurrency(item.valorFaturamento)}</div>
            <div class="col-md-6"><strong>Data Recebimento:</strong> ${item.dataRecebimento}</div>
            <div class="col-md-6"><strong>Dias Armazenamento:</strong> ${item.diasArmazenamento}</div>
        </div>
        <hr>
        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-info-circle me-1"></i> Informações Adicionais (Colunas Excedentes)</h6>
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Campo</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Percorre campos ocultos/excedentes
    let temExcedentes = false;

    // Se vier do PHP no nó 'detalhesExcedentes' ou se lermos direto do objeto ignorando os padrões
    const camposPadrao = ['chave','cnpj','fornecedor','sku','descricao','acervo','tiragem','qtdPaletesPorMilheiro','diasArmazenamento','dataRecebimento','valorFaturamento','arquivoNf','detalhesExcedentes'];

    for (const [key, value] of Object.entries(item)) {
        if (!camposPadrao.includes(key)) {
            temExcedentes = true;
            htmlConteudo += `
                <tr>
                    <td class="fw-semibold text-capitalize">${key.replace(/_/g, ' ')}</td>
                    <td>${value !== null && value !== '' ? value : '-'}</td>
                </tr>
            `;
        }
    }

    if (item.detalhesExcedentes) {
        for (const [key, value] of Object.entries(item.detalhesExcedentes)) {
            temExcedentes = true;
            htmlConteudo += `
                <tr>
                    <td class="fw-semibold text-capitalize">${key}</td>
                    <td>${value !== null && value !== '' ? value : '-'}</td>
                </tr>
            `;
        }
    }

    if (!temExcedentes) {
        htmlConteudo += `<tr><td colspan="2" class="text-center text-muted">Nenhuma informação adicional cadastrada.</td></tr>`;
    }

    htmlConteudo += `</tbody></table></div>`;

    document.getElementById("modalBodyContent").innerHTML = htmlConteudo;

    const modalElement = new bootstrap.Modal(document.getElementById("modalDetalhesItem"));
    modalElement.show();
}

function applyFilters() {
    const searchText = document.getElementById("searchInput").value.toLowerCase();
    const selectedSupplier = document.getElementById("filterFornecedor").value;

    const filtered = parsedData.filter(item => {
        const matchesSearch = 
            (item.descricao && item.descricao.toLowerCase().includes(searchText)) ||
            (item.sku && item.sku.toLowerCase().includes(searchText)) ||
            (item.fornecedor && item.fornecedor.toLowerCase().includes(searchText)) ||
            (item.dataRecebimento && item.dataRecebimento.toLowerCase().includes(searchText));

        const matchesSupplier = selectedSupplier === "" || item.fornecedor === selectedSupplier;

        return matchesSearch && matchesSupplier;
    });

    renderTable(filtered);
    updateCards(filtered);
}

function carregarDados() {
    try {
        if (typeof faturamentoDados !== 'undefined') {
            parsedData = faturamentoDados;
        } else {
            parsedData = [];
        }
        
        populateSupplierSelect(parsedData);
        renderTable(parsedData);
        updateCards(parsedData);

    } catch (error) {
        console.error("Erro ao carregar dados:", error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    carregarDados();

    if (document.getElementById("searchInput")) {
        document.getElementById("searchInput").addEventListener("input", applyFilters);
    }
    if (document.getElementById("filterFornecedor")) {
        document.getElementById("filterFornecedor").addEventListener("change", applyFilters);
    }
});